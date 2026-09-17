<?php

namespace App\Services;

use App\Models\Item;
use Illuminate\Support\Facades\Storage;

class ModelValidationService
{
    public function validate(Item $item): array
    {
        $messages = [];
        $path = is_array($item->model_path) ? ($item->model_path[0] ?? null) : $item->model_path;

        if (! $path || ! Storage::disk('public')->exists($path)) {
            return ['status' => 'invalid', 'messages' => ['The GLB file is missing from public storage.']];
        }

        $size = Storage::disk('public')->size($path);
        if ($size > 100 * 1024 * 1024) {
            $messages[] = 'The GLB file exceeds the 100 MB upload limit.';
        }

        $stream = Storage::disk('public')->readStream($path);
        $header = $stream ? fread($stream, 12) : false;
        if (is_resource($stream)) {
            fclose($stream);
        }

        if (! $header || substr($header, 0, 4) !== 'glTF') {
            $messages[] = 'The file does not contain a valid GLB header.';
        } elseif (strlen($header) >= 8 && unpack('Vversion', substr($header, 4, 4))['version'] !== 2) {
            $messages[] = 'The GLB must use version 2.';
        }

        return [
            'status' => $messages === [] ? 'valid' : 'invalid',
            'messages' => $messages,
        ];
    }

    public function validateAndStore(Item $item): array
    {
        $result = $this->validate($item);
        $item->forceFill([
            'model_validation_status' => $result['status'],
            'model_validation_messages' => $result['messages'],
        ])->saveQuietly();

        return $result;
    }
}

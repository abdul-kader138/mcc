<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'locale' => ['required', 'in:'.implode(',', User::SUPPORTED_LOCALES)],
        ]);

        if ($request->user()) {
            $request->user()->forceFill(['locale' => $validated['locale']])->save();
        } else {
            $request->session()->put('locale', $validated['locale']);
        }

        return back();
    }
}

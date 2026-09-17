<?php

namespace App\Services\Account;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AccountDataService
{
    public function export(User $user): array
    {
        return [
            'profile' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'created_at' => $user->created_at,
            ],
            'items' => $user->items->map(fn ($item) => [
                'name' => $item->name,
                'description' => $item->description,
                'slug' => $item->slug,
                'is_published' => $item->is_published,
                'created_at' => $item->created_at,
            ]),
        ];
    }

    public function anonymize(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $user->forceFill([
                'first_name' => 'Deleted',
                'last_name' => 'User',
                'email' => "deleted-user-{$user->id}@example.invalid",
                'phone' => null,
                'marketing_opt_in' => false,
                'google_id' => null,
                'avatar' => null,
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'two_factor_confirmed_at' => null,
                'password' => Hash::make(Str::random(40)),
            ])->save();

            DB::table('sessions')->where('user_id', $user->id)->delete();
        });
    }
}

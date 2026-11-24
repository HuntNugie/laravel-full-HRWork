<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('hrwork.admin_email');
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \RuntimeException('Set a valid HRWORK_ADMIN_EMAIL.');
        }
        $existing = User::where('email', $email)->first();
        if ($existing) {
            if (! $existing->hasRole('administrator')) {
                throw new \RuntimeException('Bootstrap email belongs to a non-administrator.');
            }
            return;
        }
        $password = config('hrwork.admin_password');
        if (! is_string($password) || strlen($password) < 12) {
            throw new \RuntimeException('Set HRWORK_ADMIN_PASSWORD to a unique password of at least 12 characters.');
        }
        $admin = User::create(['name' => 'Administrator', 'email' => $email, 'password' => $password, 'status' => 'active']);
        $admin->assignRole('administrator');
    }
}

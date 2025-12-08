<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class ForceSuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'claudio.rosasvico@gmail.com';
        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'name' => 'Claudio',
                'email' => $email,
                'password' => bcrypt('password'),
                'is_active' => true,
            ]);
        }

        $user->is_super_admin = true;
        $user->is_active = true;
        $user->email_verified_at = now();
        $user->save();

        $this->command->info("User {$email} FORCED as Super Admin (ID: {$user->id})");
    }
}

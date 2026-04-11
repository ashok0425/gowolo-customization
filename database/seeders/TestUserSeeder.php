<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Seeds a test user in the dashboard_db (gowolov2) users table
 * so they can log in at /login and submit customization requests.
 */
class TestUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'testuser@gowolo.com';

        $exists = DB::connection('dashboard_db')
            ->table('users')
            ->where('email', $email)
            ->exists();

        if ($exists) {
            $this->command->info("Test user already exists: {$email}");
            return;
        }

        DB::connection('dashboard_db')->table('users')->insert([
            'name'       => 'Test',
            'last_name'  => 'User',
            'email'      => $email,
            'phone'      => '1234567890',
            'password'   => Hash::make('Test@12345'),
            'user_type'  => 0,
            'active'     => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Test user created successfully.');
        $this->command->info("Login: {$email} / Test@12345");
    }
}

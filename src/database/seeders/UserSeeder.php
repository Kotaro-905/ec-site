<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $users = [
            ['name' => 'user1', 'email' => 'user1@example.com', 'password' => 'password'],
            ['name' => 'user2', 'email' => 'user2@example.com', 'password' => 'password'],
            ['name' => 'user3', 'email' => 'user3@example.com', 'password' => 'password'],
            ['name' => 'user4', 'email' => 'user4@example.com', 'password' => 'password'],
            ['name' => 'user5', 'email' => 'user5@example.com', 'password' => 'password'],
        ];

        foreach ($users as $u) {
            DB::table('users')->updateOrInsert(
                ['email' => $u['email']],
                [
                    'name'       => $u['name'],
                    'email'      => $u['email'],
                    'password'   => Hash::make($u['password']),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
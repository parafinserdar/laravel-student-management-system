<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Teacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        $superAdminRole = \App\Models\Role::where('slug', 'super-admin')->first();

        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $superAdminRole->id
        ]);

        Teacher::create([
            'user_id' => $user->id
        ]);
    }
} 
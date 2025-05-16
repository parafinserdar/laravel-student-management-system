<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        Role::create([
            'name' => 'Öğrenci',
            'slug' => 'student'
        ]);

        Role::create([
            'name' => 'Öğretmen',
            'slug' => 'teacher'
        ]);

        Role::create([
            'name' => 'Süper Admin',
            'slug' => 'super-admin'
        ]);
    }
} 
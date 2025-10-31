<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@elearning.com',
            'password' => bcrypt('password123')
        ]);
        $admin->assignRole('admin');

        $teacher = User::create([
            'name' => 'Yassine KHANBOUB',
            'email' => 'teacher@elearning.com',
            'password' => bcrypt('password123')
        ]);
        $teacher->assignRole('teacher');

        $student = User::create([
            'name' => 'Fatima',
            'email' => 'student@elearning.com',
            'password' => bcrypt('password123')
        ]);
        $student->assignRole('student');


        User::factory(5)->create()->each(function ($user) {
            $user->assignRole('student');
        });
    }
}

<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run()
    {
        $teacher = User::role('teacher')->first();

        Course::create([
            'title' => 'Laravel pour débutants',
            'description' => 'Apprenez les bases de Laravel et créez votre première application web.',
            'teacher_id' => $teacher->id
        ]);

        Course::create([
            'title' => 'API REST avec Laravel',
            'description' => 'Maîtrisez la création d\'APIs RESTful professionnelles avec Laravel.',
            'teacher_id' => $teacher->id
        ]);

        Course::create([
            'title' => 'Vue.js Avancé',
            'description' => 'Techniques avancées de Vue.js pour des applications modernes.',
            'teacher_id' => $teacher->id
        ]);
    }
}

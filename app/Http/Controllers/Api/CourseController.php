<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{

    public function index()
    {
        $courses = Course::with(['teacher', 'students'])->get();

        return CourseResource::collection($courses);
    }

    public function store(StoreCourseRequest $request)
    {
        $course = Course::create([
            'title' => $request->title,
            'description' => $request->description,
            'teacher_id' => auth()->id(),
        ]);


        $course->load('teacher');

        return response()->json([
            'message' => 'Cours créé avec succès',
            'course' => new CourseResource($course)
        ], 201);
    }

    public function show($id)
    {
        $course = Course::with(['teacher', 'students'])->findOrFail($id);

        return new CourseResource($course);
    }
    public function update(UpdateCourseRequest $request, $id)
    {
        $course = Course::findOrFail($id);
        $user = auth()->user();
        $isAdmin = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_id', $user->id)
            ->where('model_has_roles.model_type', 'App\Models\User')
            ->where('roles.name', 'admin')
            ->exists();

        if ($course->teacher_id != $user->id && !$isAdmin) {
            return response()->json([
                'message' => 'Vous n\'êtes pas autorisé à modifier ce cours'
            ], 403);
        }
        $course->update($request->validated());
        $course->load('teacher');

        return response()->json([
            'message' => 'Cours modifié avec succès',
            'course' => new CourseResource($course)
        ], 200);
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $user = auth()->user();
        $isAdmin = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_id', $user->id)
            ->where('model_has_roles.model_type', 'App\Models\User')
            ->where('roles.name', 'admin')
            ->exists();

        if ($course->teacher_id != $user->id && !$isAdmin) {
            return response()->json([
                'message' => 'Vous n\'êtes pas autorisé à supprimer ce cours'
            ], 403);
        }

        $course->delete();

        return response()->json([
            'message' => 'Cours supprimé avec succès'
        ], 200);
    }

    public function enroll($id)
    {
        $course = Course::findOrFail($id);
        $user = auth()->user();

        $alreadyEnrolled = DB::table('enrollments')
            ->where('student_id', $user->id)
            ->where('course_id', $id)
            ->exists();

        if ($alreadyEnrolled) {
            return response()->json([
                'message' => 'Vous êtes déjà inscrit à ce cours'
            ], 400);
        }

        DB::table('enrollments')->insert([
            'student_id' => $user->id,
            'course_id' => $id,
            'enrolled_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'message' => 'Inscription réussie au cours',
            'course' => new CourseResource($course->load('teacher'))
        ], 200);
    }

    public function myCourses()
    {
        $user = auth()->user();

        $courseIds = DB::table('enrollments')
            ->where('student_id', $user->id)
            ->pluck('course_id');
        $courses = Course::with(['teacher', 'students'])
            ->whereIn('id', $courseIds)
            ->get();

        return CourseResource::collection($courses);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Course extends Model
{
    use HasFactory;

    /**
     * Les attributs qui peuvent être assignés en masse
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'teacher_id',
    ];

    /**
     * Relation : Un cours appartient à un enseignant (User)
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Relation : Les étudiants inscrits à ce cours
     * Relation many-to-many via la table enrollments
     */
    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_id', 'student_id')
                    ->withTimestamps()
                    ->withPivot('enrolled_at');
    }

    /**
     * Vérifier si un utilisateur est inscrit à ce cours
     */
    public function isEnrolledBy($userId)
    {
        return DB::table('enrollments')
            ->where('course_id', $this->id)
            ->where('student_id', $userId)
            ->exists();
    }

    /**
     * Obtenir le nombre d'étudiants inscrits
     */
    public function getStudentsCountAttribute()
    {
        return DB::table('enrollments')
            ->where('course_id', $this->id)
            ->count();
    }
}
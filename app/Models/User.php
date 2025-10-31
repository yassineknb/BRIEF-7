<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * Les attributs qui peuvent être assignés en masse
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Les attributs qui doivent être cachés lors de la sérialisation
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Les attributs qui doivent être castés
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relation : Un utilisateur (enseignant) peut avoir plusieurs cours
     */
    public function courses()
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    /**
     * Relation : Les cours auxquels l'utilisateur (étudiant) est inscrit
     * C'est une relation many-to-many via la table enrollments
     */
    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'enrollments', 'student_id', 'course_id')
                    ->withTimestamps()
                    ->withPivot('enrolled_at');
    }

    /**
     * Vérifier si l'utilisateur a un rôle spécifique
     * Méthode alternative qui utilise directement la base de données
     */
    public function hasRoleCustom($roleName)
    {
        return DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_id', $this->id)
            ->where('model_has_roles.model_type', 'App\Models\User')
            ->where('roles.name', $roleName)
            ->exists();
    }

    /**
     * Vérifier si l'utilisateur est admin
     */
    public function isAdmin()
    {
        return $this->hasRoleCustom('admin');
    }

    /**
     * Vérifier si l'utilisateur est enseignant
     */
    public function isTeacher()
    {
        return $this->hasRoleCustom('teacher');
    }

    /**
     * Vérifier si l'utilisateur est étudiant
     */
    public function isStudent()
    {
        return $this->hasRoleCustom('student');
    }

    /**
     * Obtenir le nom du rôle principal de l'utilisateur
     */
    public function getRoleName()
    {
        $role = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_id', $this->id)
            ->where('model_has_roles.model_type', 'App\Models\User')
            ->first();

        return $role ? $role->name : null;
    }
}
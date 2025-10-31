<?php


namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentResource extends JsonResource
{
    /**
     * Transformer la ressource en tableau
     * C'est ici qu'on définit comment afficher les données d'une inscription
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            
            // Informations sur le cours
            'course' => [
                'id' => $this->course_id,
                'title' => $this->whenLoaded('course', function() {
                    return $this->course->title;
                }),
                'description' => $this->whenLoaded('course', function() {
                    return $this->course->description;
                }),
            ],
            
            // Informations sur l'étudiant
            'student' => [
                'id' => $this->student_id,
                'name' => $this->whenLoaded('student', function() {
                    return $this->student->name;
                }),
                'email' => $this->whenLoaded('student', function() {
                    return $this->student->email;
                }),
            ],
            
            // Date d'inscription
            'enrolled_at' => $this->enrolled_at 
                ? $this->enrolled_at->format('Y-m-d H:i:s') 
                : $this->created_at->format('Y-m-d H:i:s'),
            
            // Date de création de l'enregistrement
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
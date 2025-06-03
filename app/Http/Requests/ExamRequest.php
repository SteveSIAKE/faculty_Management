<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExamRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:midterm,final,quiz,assignment,other',
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'duration' => 'required|integer|min:1',
            'total_points' => 'required|numeric|min:0',
            'passing_score' => 'required|numeric|min:0',
            'status' => 'required|in:scheduled,ongoing,completed,cancelled',
            'room' => 'nullable|string|max:255',
            'instructions' => 'nullable|string'
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Le titre est obligatoire',
            'title.string' => 'Le titre doit être une chaîne de caractères',
            'title.max' => 'Le titre ne peut pas dépasser 255 caractères',
            'description.required' => 'La description est obligatoire',
            'description.string' => 'La description doit être une chaîne de caractères',
            'type.required' => 'Le type est obligatoire',
            'type.in' => 'Le type doit être midterm, final, quiz, assignment ou other',
            'course_id.required' => 'Le cours est obligatoire',
            'course_id.exists' => 'Le cours sélectionné n\'existe pas',
            'teacher_id.required' => 'L\'enseignant est obligatoire',
            'teacher_id.exists' => 'L\'enseignant sélectionné n\'existe pas',
            'date.required' => 'La date est obligatoire',
            'date.date' => 'La date doit être une date valide',
            'duration.required' => 'La durée est obligatoire',
            'duration.integer' => 'La durée doit être un nombre entier',
            'duration.min' => 'La durée doit être d\'au moins 1 minute',
            'total_points.required' => 'Le nombre total de points est obligatoire',
            'total_points.numeric' => 'Le nombre total de points doit être un nombre',
            'total_points.min' => 'Le nombre total de points ne peut pas être négatif',
            'passing_score.required' => 'La note de passage est obligatoire',
            'passing_score.numeric' => 'La note de passage doit être un nombre',
            'passing_score.min' => 'La note de passage ne peut pas être négative',
            'status.required' => 'Le statut est obligatoire',
            'status.in' => 'Le statut doit être scheduled, ongoing, completed ou cancelled',
            'room.string' => 'La salle doit être une chaîne de caractères',
            'room.max' => 'La salle ne peut pas dépasser 255 caractères',
            'instructions.string' => 'Les instructions doivent être une chaîne de caractères'
        ];
    }
} 
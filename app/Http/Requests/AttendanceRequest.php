<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttendanceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'date' => 'required|date',
            'status' => ['required', Rule::in(['present', 'absent', 'late'])],
            'notes' => 'nullable|string'
        ];

        // Ajouter les règles pour student_id et course_id uniquement lors de la création
        if ($this->isMethod('POST')) {
            $rules['student_id'] = 'required|exists:users,id';
            $rules['course_id'] = 'required|exists:courses,id';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'student_id.required' => 'L\'étudiant est requis',
            'student_id.exists' => 'L\'étudiant sélectionné n\'existe pas',
            'course_id.required' => 'Le cours est requis',
            'course_id.exists' => 'Le cours sélectionné n\'existe pas',
            'date.required' => 'La date est requise',
            'date.date' => 'La date doit être une date valide',
            'status.required' => 'Le statut est requis',
            'status.in' => 'Le statut doit être présent, absent ou en retard'
        ];
    }
} 
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AbsenceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'date' => 'required|date',
            'type' => ['required', Rule::in(['absence', 'retard'])],
            'status' => ['required', Rule::in(['pending', 'justified', 'unjustified'])],
            'justification' => 'nullable|string',
            'justification_file' => 'nullable|file|max:10240', // 10MB max
            'comments' => 'nullable|string'
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
            'type.required' => 'Le type est requis',
            'type.in' => 'Le type doit être soit une absence soit un retard',
            'status.required' => 'Le statut est requis',
            'status.in' => 'Le statut doit être en attente, justifié ou non justifié',
            'justification_file.file' => 'Le fichier de justification doit être un fichier valide',
            'justification_file.max' => 'Le fichier de justification ne doit pas dépasser 10MB'
        ];
    }
} 
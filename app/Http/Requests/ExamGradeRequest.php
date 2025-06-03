<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExamGradeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'score' => 'required|numeric|min:0',
            'comments' => 'nullable|string',
            'status' => ['required', Rule::in(['pending', 'graded', 'appealed', 'final'])],
        ];

        if ($this->isMethod('POST')) {
            $rules = array_merge($rules, [
                'exam_id' => 'required|exists:exams,id',
                'student_id' => 'required|exists:users,id',
            ]);
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'exam_id.required' => 'L\'examen est requis',
            'exam_id.exists' => 'L\'examen sélectionné n\'existe pas',
            'student_id.required' => 'L\'étudiant est requis',
            'student_id.exists' => 'L\'étudiant sélectionné n\'existe pas',
            'score.required' => 'La note est requise',
            'score.numeric' => 'La note doit être un nombre',
            'score.min' => 'La note ne peut pas être négative',
            'comments.string' => 'Les commentaires doivent être du texte',
            'status.required' => 'Le statut est requis',
            'status.in' => 'Le statut sélectionné n\'est pas valide',
        ];
    }
} 
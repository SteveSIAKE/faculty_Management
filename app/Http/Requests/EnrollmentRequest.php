<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EnrollmentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'student_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'status' => 'required|in:pending,approved,rejected',
            'enrollment_date' => 'required|date',
            'notes' => 'nullable|string'
        ];
    }

    public function messages()
    {
        return [
            'student_id.required' => 'L\'étudiant est obligatoire',
            'student_id.exists' => 'L\'étudiant sélectionné n\'existe pas',
            'course_id.required' => 'Le cours est obligatoire',
            'course_id.exists' => 'Le cours sélectionné n\'existe pas',
            'status.required' => 'Le statut est obligatoire',
            'status.in' => 'Le statut doit être pending, approved ou rejected',
            'enrollment_date.required' => 'La date d\'inscription est obligatoire',
            'enrollment_date.date' => 'La date d\'inscription doit être une date valide'
        ];
    }
} 
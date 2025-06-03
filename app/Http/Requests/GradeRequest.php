<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GradeRequest extends FormRequest
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
            'teacher_id' => 'required|exists:users,id',
            'exam_type' => 'required|in:midterm,final,quiz,assignment,other',
            'grade' => 'required|numeric|min:0|max:20',
            'comments' => 'nullable|string'
        ];
    }

    public function messages()
    {
        return [
            'student_id.required' => 'L\'étudiant est obligatoire',
            'student_id.exists' => 'L\'étudiant sélectionné n\'existe pas',
            'course_id.required' => 'Le cours est obligatoire',
            'course_id.exists' => 'Le cours sélectionné n\'existe pas',
            'teacher_id.required' => 'L\'enseignant est obligatoire',
            'teacher_id.exists' => 'L\'enseignant sélectionné n\'existe pas',
            'exam_type.required' => 'Le type d\'examen est obligatoire',
            'exam_type.in' => 'Le type d\'examen doit être midterm, final, quiz, assignment ou other',
            'grade.required' => 'La note est obligatoire',
            'grade.numeric' => 'La note doit être un nombre',
            'grade.min' => 'La note ne peut pas être inférieure à 0',
            'grade.max' => 'La note ne peut pas être supérieure à 20',
            'comments.string' => 'Les commentaires doivent être une chaîne de caractères'
        ];
    }
} 
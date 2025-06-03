<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CourseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('courses')->where(function ($query) {
                    return $query->where('academic_year_id', $this->academic_year_id);
                })->ignore($this->course)
            ],
            'description' => 'required|string',
            'credits' => 'required|integer|min:1',
            'teacher_id' => 'required|exists:users,id',
            'department_id' => 'required|exists:departments,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'max_students' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive,planned'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Le nom est obligatoire',
            'name.string' => 'Le nom doit être une chaîne de caractères',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères',
            'code.required' => 'Le code est obligatoire',
            'code.string' => 'Le code doit être une chaîne de caractères',
            'code.max' => 'Le code ne peut pas dépasser 50 caractères',
            'code.unique' => 'Ce code existe déjà pour cette année académique',
            'description.required' => 'La description est obligatoire',
            'description.string' => 'La description doit être une chaîne de caractères',
            'credits.required' => 'Le nombre de crédits est obligatoire',
            'credits.integer' => 'Le nombre de crédits doit être un nombre entier',
            'credits.min' => 'Le nombre de crédits doit être au moins 1',
            'teacher_id.required' => 'L\'enseignant est obligatoire',
            'teacher_id.exists' => 'L\'enseignant sélectionné n\'existe pas',
            'department_id.required' => 'Le département est obligatoire',
            'department_id.exists' => 'Le département sélectionné n\'existe pas',
            'academic_year_id.required' => 'L\'année académique est obligatoire',
            'academic_year_id.exists' => 'L\'année académique sélectionnée n\'existe pas',
            'start_date.required' => 'La date de début est obligatoire',
            'start_date.date' => 'La date de début doit être une date valide',
            'end_date.required' => 'La date de fin est obligatoire',
            'end_date.date' => 'La date de fin doit être une date valide',
            'end_date.after' => 'La date de fin doit être postérieure à la date de début',
            'max_students.required' => 'Le nombre maximum d\'étudiants est obligatoire',
            'max_students.integer' => 'Le nombre maximum d\'étudiants doit être un nombre entier',
            'max_students.min' => 'Le nombre maximum d\'étudiants doit être au moins 1',
            'status.required' => 'Le statut est obligatoire',
            'status.in' => 'Le statut doit être active, inactive ou planned'
        ];
    }
} 
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AcademicYearRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,inactive,planned',
            'registration_start' => 'required|date|before:start_date',
            'registration_end' => 'required|date|after:registration_start|before:start_date'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Le nom est obligatoire',
            'name.string' => 'Le nom doit être une chaîne de caractères',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères',
            'description.required' => 'La description est obligatoire',
            'description.string' => 'La description doit être une chaîne de caractères',
            'start_date.required' => 'La date de début est obligatoire',
            'start_date.date' => 'La date de début doit être une date valide',
            'end_date.required' => 'La date de fin est obligatoire',
            'end_date.date' => 'La date de fin doit être une date valide',
            'end_date.after' => 'La date de fin doit être postérieure à la date de début',
            'status.required' => 'Le statut est obligatoire',
            'status.in' => 'Le statut doit être active, inactive ou planned',
            'registration_start.required' => 'La date de début des inscriptions est obligatoire',
            'registration_start.date' => 'La date de début des inscriptions doit être une date valide',
            'registration_start.before' => 'La date de début des inscriptions doit être antérieure à la date de début',
            'registration_end.required' => 'La date de fin des inscriptions est obligatoire',
            'registration_end.date' => 'La date de fin des inscriptions doit être une date valide',
            'registration_end.after' => 'La date de fin des inscriptions doit être postérieure à la date de début des inscriptions',
            'registration_end.before' => 'La date de fin des inscriptions doit être antérieure à la date de début'
        ];
    }
} 
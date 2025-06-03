<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DepartmentRequest extends FormRequest
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
                Rule::unique('departments')->where(function ($query) {
                    return $query->where('academic_year_id', $this->academic_year_id);
                })->ignore($this->department)
            ],
            'description' => 'required|string',
            'academic_year_id' => 'required|exists:academic_years,id',
            'head_id' => 'nullable|exists:users,id'
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
            'academic_year_id.required' => 'L\'année académique est obligatoire',
            'academic_year_id.exists' => 'L\'année académique sélectionnée n\'existe pas',
            'head_id.exists' => 'Le responsable sélectionné n\'existe pas'
        ];
    }
} 
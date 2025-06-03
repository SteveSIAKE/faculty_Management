<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles')->ignore($this->role)
            ],
            'description' => 'required|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'is_system' => 'boolean'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Le nom est obligatoire',
            'name.string' => 'Le nom doit être une chaîne de caractères',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères',
            'name.unique' => 'Ce nom de rôle existe déjà',
            'description.required' => 'La description est obligatoire',
            'description.string' => 'La description doit être une chaîne de caractères',
            'permissions.array' => 'Les permissions doivent être un tableau',
            'permissions.*.exists' => 'Une ou plusieurs permissions sélectionnées n\'existent pas',
            'is_system.boolean' => 'Le champ système doit être un booléen'
        ];
    }
} 
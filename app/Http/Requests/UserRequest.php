<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->user)
            ],
            'password' => [
                $this->isMethod('POST') ? 'required' : 'nullable',
                'string',
                'min:8'
            ],
            'role_id' => 'required|exists:roles,id',
            'department_id' => 'nullable|exists:departments,id',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Le nom est obligatoire',
            'name.string' => 'Le nom doit être une chaîne de caractères',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères',
            'email.required' => 'L\'email est obligatoire',
            'email.email' => 'L\'email doit être une adresse email valide',
            'email.max' => 'L\'email ne peut pas dépasser 255 caractères',
            'email.unique' => 'Cet email est déjà utilisé',
            'password.required' => 'Le mot de passe est obligatoire',
            'password.string' => 'Le mot de passe doit être une chaîne de caractères',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères',
            'role_id.required' => 'Le rôle est obligatoire',
            'role_id.exists' => 'Le rôle sélectionné n\'existe pas',
            'department_id.exists' => 'Le département sélectionné n\'existe pas',
            'phone.string' => 'Le numéro de téléphone doit être une chaîne de caractères',
            'phone.max' => 'Le numéro de téléphone ne peut pas dépasser 20 caractères',
            'address.string' => 'L\'adresse doit être une chaîne de caractères',
            'birth_date.date' => 'La date de naissance doit être une date valide',
            'gender.in' => 'Le genre doit être male, female ou other'
        ];
    }
} 
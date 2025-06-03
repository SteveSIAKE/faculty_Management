<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
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
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères',
            'email.required' => 'L\'adresse email est obligatoire',
            'email.email' => 'L\'adresse email doit être valide',
            'email.max' => 'L\'adresse email ne peut pas dépasser 255 caractères',
            'email.unique' => 'Cette adresse email est déjà utilisée',
            'password.required' => 'Le mot de passe est obligatoire',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas',
            'role_id.required' => 'Le rôle est obligatoire',
            'role_id.exists' => 'Le rôle sélectionné n\'existe pas',
            'department_id.exists' => 'Le département sélectionné n\'existe pas',
            'phone.max' => 'Le numéro de téléphone ne peut pas dépasser 20 caractères',
            'birth_date.date' => 'La date de naissance doit être une date valide',
            'gender.in' => 'Le genre doit être male, female ou other'
        ];
    }
} 
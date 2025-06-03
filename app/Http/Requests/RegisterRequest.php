<?php

namespace App\Http\Requests;

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
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'department_id' => 'nullable|exists:departments,id'
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
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas',
            'role_id.required' => 'Le rôle est obligatoire',
            'role_id.exists' => 'Le rôle sélectionné n\'existe pas',
            'department_id.exists' => 'Le département sélectionné n\'existe pas'
        ];
    }
} 
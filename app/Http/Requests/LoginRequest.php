<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string'
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'L\'email est obligatoire',
            'email.email' => 'L\'email doit être une adresse email valide',
            'password.required' => 'Le mot de passe est obligatoire',
            'password.string' => 'Le mot de passe doit être une chaîne de caractères'
        ];
    }
} 
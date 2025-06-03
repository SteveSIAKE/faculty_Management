<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotificationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,warning,success,error',
            'link' => 'nullable|string|max:255',
            'is_read' => 'boolean'
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => 'L\'utilisateur est obligatoire',
            'user_id.exists' => 'L\'utilisateur sélectionné n\'existe pas',
            'title.required' => 'Le titre est obligatoire',
            'title.string' => 'Le titre doit être une chaîne de caractères',
            'title.max' => 'Le titre ne peut pas dépasser 255 caractères',
            'message.required' => 'Le message est obligatoire',
            'message.string' => 'Le message doit être une chaîne de caractères',
            'type.required' => 'Le type est obligatoire',
            'type.in' => 'Le type doit être info, warning, success ou error',
            'link.string' => 'Le lien doit être une chaîne de caractères',
            'link.max' => 'Le lien ne peut pas dépasser 255 caractères',
            'is_read.boolean' => 'Le statut de lecture doit être un booléen'
        ];
    }
} 
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResourceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:document,video,link,other',
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'required|exists:users,id',
            'file_path' => 'nullable|string',
            'url' => 'nullable|url'
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Le titre est obligatoire',
            'title.string' => 'Le titre doit être une chaîne de caractères',
            'title.max' => 'Le titre ne peut pas dépasser 255 caractères',
            'description.required' => 'La description est obligatoire',
            'description.string' => 'La description doit être une chaîne de caractères',
            'type.required' => 'Le type est obligatoire',
            'type.in' => 'Le type doit être document, video, link ou other',
            'course_id.required' => 'Le cours est obligatoire',
            'course_id.exists' => 'Le cours sélectionné n\'existe pas',
            'teacher_id.required' => 'L\'enseignant est obligatoire',
            'teacher_id.exists' => 'L\'enseignant sélectionné n\'existe pas',
            'file_path.string' => 'Le chemin du fichier doit être une chaîne de caractères',
            'url.url' => 'L\'URL doit être une URL valide'
        ];
    }
} 
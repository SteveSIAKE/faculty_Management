<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->with(['role', 'department']);

        if ($request->has('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(10);

        return response()->json($users);
    }

    public function store(UserRequest $request)
    {
        $user = User::create($request->validated());

        return response()->json([
            'message' => 'Utilisateur créé avec succès',
            'user' => $user->load(['role', 'department'])
        ], 201);
    }

    public function show(User $user)
    {
        return response()->json([
            'user' => $user->load(['role', 'department'])
        ]);
    }

    public function update(UserRequest $request, User $user)
    {
        $user->update($request->validated());

        return response()->json([
            'message' => 'Utilisateur mis à jour avec succès',
            'user' => $user->load(['role', 'department'])
        ]);
    }

    public function destroy(User $user)
    {
        // Empêcher la suppression de l'utilisateur connecté
        if ($user->id === auth()->id()) {
            return response()->json([
                'message' => 'Impossible de supprimer votre propre compte'
            ], 422);
        }

        $user->delete();

        return response()->json([
            'message' => 'Utilisateur supprimé avec succès'
        ]);
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'users' => 'required|array',
            'users.*.name' => 'required|string|max:255',
            'users.*.email' => 'required|email|max:255|unique:users,email',
            'users.*.password' => 'required|string|min:8',
            'users.*.role_id' => 'required|exists:roles,id',
            'users.*.department_id' => 'nullable|exists:departments,id',
            'users.*.phone' => 'nullable|string|max:20',
            'users.*.address' => 'nullable|string',
            'users.*.birth_date' => 'nullable|date',
            'users.*.gender' => 'nullable|in:male,female,other'
        ]);

        $users = collect($request->users)->map(function ($userData) {
            return User::create($userData);
        });

        return response()->json([
            'message' => 'Utilisateurs créés avec succès',
            'users' => $users->load(['role', 'department'])
        ], 201);
    }

    public function courses(User $user)
    {
        $courses = $user->courses()
            ->with(['teacher', 'department', 'academicYear'])
            ->paginate(10);

        return response()->json($courses);
    }

    public function grades(User $user)
    {
        $grades = $user->grades()
            ->with(['course', 'teacher'])
            ->paginate(10);

        return response()->json($grades);
    }

    public function attendance(User $user)
    {
        $attendance = $user->attendance()
            ->with(['course', 'teacher'])
            ->paginate(10);

        return response()->json($attendance);
    }
} 
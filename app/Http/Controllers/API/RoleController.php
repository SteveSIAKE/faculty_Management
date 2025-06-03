<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $query = Role::query()->with('permissions');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $roles = $query->latest()->paginate(10);

        return response()->json($roles);
    }

    public function store(RoleRequest $request)
    {
        $role = Role::create($request->validated());

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        return response()->json([
            'message' => 'Rôle créé avec succès',
            'role' => $role->load('permissions')
        ], 201);
    }

    public function show(Role $role)
    {
        return response()->json([
            'role' => $role->load(['permissions', 'users'])
        ]);
    }

    public function update(RoleRequest $request, Role $role)
    {
        // Empêcher la modification des rôles système
        if ($role->is_system) {
            return response()->json([
                'message' => 'Impossible de modifier un rôle système'
            ], 422);
        }

        $role->update($request->validated());

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        return response()->json([
            'message' => 'Rôle mis à jour avec succès',
            'role' => $role->load('permissions')
        ]);
    }

    public function destroy(Role $role)
    {
        // Empêcher la suppression des rôles système
        if ($role->is_system) {
            return response()->json([
                'message' => 'Impossible de supprimer un rôle système'
            ], 422);
        }

        // Vérifier si le rôle a des utilisateurs
        if ($role->users()->count() > 0) {
            return response()->json([
                'message' => 'Impossible de supprimer ce rôle car il est attribué à des utilisateurs'
            ], 422);
        }

        $role->delete();

        return response()->json([
            'message' => 'Rôle supprimé avec succès'
        ]);
    }

    public function users(Role $role)
    {
        $users = $role->users()
            ->with(['role', 'department'])
            ->paginate(10);

        return response()->json($users);
    }

    public function permissions(Role $role)
    {
        return response()->json([
            'permissions' => $role->permissions
        ]);
    }

    public function updatePermissions(Request $request, Role $role)
    {
        // Empêcher la modification des rôles système
        if ($role->is_system) {
            return response()->json([
                'message' => 'Impossible de modifier les permissions d\'un rôle système'
            ], 422);
        }

        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role->permissions()->sync($request->permissions);

        return response()->json([
            'message' => 'Permissions mises à jour avec succès',
            'permissions' => $role->permissions
        ]);
    }
} 
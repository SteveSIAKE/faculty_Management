<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResourceRequest;
use App\Models\Resource;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    public function index(Request $request)
    {
        $query = Resource::query()->with(['course', 'teacher']);

        if ($request->has('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->has('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $resources = $query->latest()->paginate(10);

        return response()->json($resources);
    }

    public function store(ResourceRequest $request)
    {
        $resource = Resource::create($request->validated());

        return response()->json([
            'message' => 'Ressource créée avec succès',
            'resource' => $resource->load(['course', 'teacher'])
        ], 201);
    }

    public function show(Resource $resource)
    {
        return response()->json([
            'resource' => $resource->load(['course', 'teacher'])
        ]);
    }

    public function update(ResourceRequest $request, Resource $resource)
    {
        $resource->update($request->validated());

        return response()->json([
            'message' => 'Ressource mise à jour avec succès',
            'resource' => $resource->load(['course', 'teacher'])
        ]);
    }

    public function destroy(Resource $resource)
    {
        // Supprimer le fichier physique si nécessaire
        if ($resource->file_path && file_exists(public_path($resource->file_path))) {
            unlink(public_path($resource->file_path));
        }

        $resource->delete();

        return response()->json([
            'message' => 'Ressource supprimée avec succès'
        ]);
    }

    public function download(Resource $resource)
    {
        if (!$resource->file_path || !file_exists(public_path($resource->file_path))) {
            return response()->json([
                'message' => 'Le fichier n\'existe pas'
            ], 404);
        }

        return response()->download(public_path($resource->file_path));
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'resources' => 'required|array',
            'resources.*.title' => 'required|string|max:255',
            'resources.*.description' => 'required|string',
            'resources.*.type' => 'required|in:document,video,link,other',
            'resources.*.course_id' => 'required|exists:courses,id',
            'resources.*.teacher_id' => 'required|exists:users,id',
            'resources.*.file_path' => 'nullable|string',
            'resources.*.url' => 'nullable|url'
        ]);

        $resources = collect($request->resources)->map(function ($item) {
            return Resource::create($item);
        });

        return response()->json([
            'message' => 'Ressources créées avec succès',
            'resources' => $resources->load(['course', 'teacher'])
        ], 201);
    }
} 
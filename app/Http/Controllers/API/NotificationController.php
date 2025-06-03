<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\NotificationRequest;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::query()->with(['user']);

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('is_read')) {
            $query->where('is_read', $request->boolean('is_read'));
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $notifications = $query->latest()->paginate(10);

        return response()->json($notifications);
    }

    public function store(NotificationRequest $request)
    {
        $notification = Notification::create($request->validated());

        return response()->json([
            'message' => 'Notification créée avec succès',
            'notification' => $notification->load('user')
        ], 201);
    }

    public function show(Notification $notification)
    {
        return response()->json([
            'notification' => $notification->load('user')
        ]);
    }

    public function update(NotificationRequest $request, Notification $notification)
    {
        $notification->update($request->validated());

        return response()->json([
            'message' => 'Notification mise à jour avec succès',
            'notification' => $notification->load('user')
        ]);
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();

        return response()->json([
            'message' => 'Notification supprimée avec succès'
        ]);
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->is_read) {
            return response()->json([
                'message' => 'La notification est déjà marquée comme lue'
            ], 422);
        }

        $notification->update(['is_read' => true]);

        return response()->json([
            'message' => 'Notification marquée comme lue avec succès',
            'notification' => $notification->load('user')
        ]);
    }

    public function markAsUnread(Notification $notification)
    {
        if (!$notification->is_read) {
            return response()->json([
                'message' => 'La notification est déjà marquée comme non lue'
            ], 422);
        }

        $notification->update(['is_read' => false]);

        return response()->json([
            'message' => 'Notification marquée comme non lue avec succès',
            'notification' => $notification->load('user')
        ]);
    }

    public function markAllAsRead(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $count = Notification::where('user_id', $request->user_id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'message' => 'Toutes les notifications ont été marquées comme lues',
            'count' => $count
        ]);
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'notifications' => 'required|array',
            'notifications.*.user_id' => 'required|exists:users,id',
            'notifications.*.title' => 'required|string|max:255',
            'notifications.*.message' => 'required|string',
            'notifications.*.type' => 'required|in:info,warning,success,error',
            'notifications.*.link' => 'nullable|string|max:255',
            'notifications.*.is_read' => 'boolean'
        ]);

        $notifications = collect($request->notifications)->map(function ($notificationData) {
            return Notification::create($notificationData);
        });

        return response()->json([
            'message' => 'Notifications créées avec succès',
            'notifications' => $notifications->load('user')
        ], 201);
    }
} 
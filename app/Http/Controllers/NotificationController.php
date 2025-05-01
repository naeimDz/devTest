<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\User;



class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = $user
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($notification) {
                return [
                    'id' => $notification->id,
                    "data" => $notification->data,
                    'read' => $notification->read_at ? true : false,
                    'title' => $notification->data['title'] ?? '',
                    'body' => $notification->data['body'] ?? '',
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at->diffForHumans(),
                ];
            });
        return Inertia::render('Notifications/Index', [
            'notifications' => $notifications,
            'unread_count' => $user->unreadNotifications->count(),
        ]);
    }


    
    public function show()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'notifications' => $notifications
        ]);
    }

}

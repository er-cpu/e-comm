<?php

namespace App\Http\Controllers;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->latest()->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(DatabaseNotification $notification)
    {
        if ($notification->notifiable_id !== Auth::id()) {
            abort(403);
        }

        $notification->markAsRead();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        $url = request()->query('redirect', data_get($notification->data, 'url', route('notifications.index')));
        return redirect($url);
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'All notifications marked as read.');
    }

    public function unreadCount()
    {
        return response()->json([
            'count' => Auth::user()->unreadNotifications()->count(),
        ]);
    }

    public function recent()
    {
        $notifications = Auth::user()->notifications()->latest()->take(5)->get();
        return response()->json($notifications->map(fn ($n) => [
            'id' => $n->id,
            'message' => $n->data['message'] ?? 'Notification',
            'url' => $n->data['url'] ?? null,
            'read' => !is_null($n->read_at),
            'time' => $n->created_at->diffForHumans(),
        ]));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->take(30)->get()->map(fn($n) => [
            'id'   => $n->id,
            'data' => $n->data,
            'read' => !is_null($n->read_at),
            'time' => $n->created_at->diffForHumans(),
        ]);

        return response()->json([
            'unread'        => $user->unreadNotifications()->count(),
            'notifications' => $notifications,
        ]);
    }

    public function destroy($id)
    {
        Auth::user()->notifications()->where('id', $id)->delete();
        return response()->json(['ok' => true]);
    }

    public function clear()
    {
        Auth::user()->notifications()->delete();
        return response()->json(['ok' => true]);
    }

    public function readAll()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['ok' => true]);
    }
}

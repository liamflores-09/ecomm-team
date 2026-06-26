<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('creator')
            ->orderByDesc('pinned')
            ->orderByDesc('created_at')
            ->paginate(10);

        $user = Auth::user();
        return view('announcements', compact('announcements', 'user'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'body'       => 'required|string',
            'pinned'     => 'nullable|boolean',
            'expires_at' => 'nullable|date|after:now',
        ]);

        Announcement::create([
            'title'      => $data['title'],
            'body'       => $data['body'],
            'pinned'     => $request->boolean('pinned'),
            'expires_at' => $data['expires_at'] ?? null,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('announcements')->with('success', 'Announcement posted.');
    }

    public function update(Request $request, Announcement $announcement)
    {
        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'body'       => 'required|string',
            'pinned'     => 'nullable|boolean',
            'expires_at' => 'nullable|date',
        ]);

        $announcement->update([
            'title'      => $data['title'],
            'body'       => $data['body'],
            'pinned'     => $request->boolean('pinned'),
            'expires_at' => $data['expires_at'] ?? null,
        ]);

        return redirect()->route('announcements')->with('success', 'Announcement updated.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return redirect()->route('announcements')->with('success', 'Announcement deleted.');
    }

    public function togglePin(Announcement $announcement)
    {
        $announcement->update(['pinned' => !$announcement->pinned]);
        return response()->json(['success' => true, 'pinned' => $announcement->pinned]);
    }
}

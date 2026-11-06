<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(): View
    {
        $announcements = Announcement::where('published_at', '<=', now())
            ->latest('published_at')
            ->paginate(10);

        return view('announcements.index', compact('announcements'));
    }

    public function show(Announcement $announcement): View
    {
        abort_if($announcement->published_at === null || $announcement->published_at->isFuture(), 404);

        return view('announcements.show', compact('announcement'));
    }
}

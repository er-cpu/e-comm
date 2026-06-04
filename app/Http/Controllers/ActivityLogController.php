<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    public function index()
    {
        $activities = ActivityLog::where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        return view('history.index', compact('activities'));
    }

    public function adminIndex()
    {
        $activities = ActivityLog::with('user')
            ->latest()
            ->paginate(20);

        return view('admin.history', compact('activities'));
    }
}

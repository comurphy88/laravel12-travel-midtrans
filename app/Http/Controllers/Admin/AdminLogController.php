<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\View\View;

class AdminLogController extends Controller
{
    public function index(): View
    {
        $logs = ActivityLog::with('user')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.logs.index', compact('logs'));
    }
}
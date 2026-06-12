<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use Illuminate\View\View;

class AdminEmailLogController extends Controller
{
    public function index(): View
    {
        $emailLogs = EmailLog::with('user')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.email-logs.index', compact('emailLogs'));
    }
}
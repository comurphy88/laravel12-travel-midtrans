<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminNotificationController extends Controller
{
    public function index(): View
    {
        $notifications = Notification::with('user')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function destroy(Notification $notification): RedirectResponse
    {
        $notification->delete();

        return redirect()
            ->route('admin.notifications.index')
            ->with('success', 'Notifikasi berhasil dihapus.');
    }
}
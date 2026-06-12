<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminReviewController extends Controller
{
    public function index(Request $request): View
    {
        $query = Review::with(['user', 'destination', 'booking'])->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $reviews = $query->paginate(15)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function show(Review $review): View
    {
        $review->load(['user', 'destination', 'booking', 'reviewer']);

        return view('admin.reviews.show', compact('review'));
    }

    public function updateStatus(Request $request, Review $review): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $old = $review->status;

        $review->update([
            'status'      => $validated['status'],
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        ActivityLog::log(
            'update',
            "Mengubah status review #{$review->id}: {$old} → {$validated['status']}",
            'reviews',
            $review->id
        );

        return back()->with('success', 'Status review berhasil diperbarui.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        ActivityLog::log(
            'delete',
            "Menghapus review #{$review->id} dari {$review->user?->name}",
            'reviews',
            $review->id
        );

        $review->delete();

        return redirect()->route('admin.reviews.index')->with('success', 'Review berhasil dihapus.');
    }
}
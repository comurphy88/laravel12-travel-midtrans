<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /** @return array<string, string> */
    private function rules(?int $ignoreId = null): array
    {
        $emailUnique = $ignoreId
            ? "unique:users,email,{$ignoreId}"
            : 'unique:users,email';

        return [
            'name'  => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'email' => "required|email|max:255|{$emailUnique}",
            'role'  => 'required|in:user,admin',
            'phone' => 'nullable|string|max:20|regex:/^[0-9+\-\s()]+$/',
        ];
    }

    public function index(Request $request): View
    {
        $query = User::withCount('bookings');

        if ($request->filled('search')) {
            $term = $request->string('search')->trim()->toString();
            $query->where(function ($q) use ($term): void {
                $q->where('name',  'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%");
            });
        }

        $users = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.form');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            ...$this->rules(),
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone'    => $validated['phone'] ?? null,
            'role'     => $validated['role'],
        ]);

        ActivityLog::log('create', "Menambah user: {$user->name} ({$user->email})", 'users', $user->id);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.form', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate($this->rules($user->id));

        $user->fill([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role'  => $validated['role'],
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8']);
            $user->password = Hash::make($request->string('password')->toString());
        }

        $user->save();

        ActivityLog::log('update', "Mengubah user: {$user->name} ({$user->email})", 'users', $user->id);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        ActivityLog::log('delete', "Menghapus user: {$user->name} ({$user->email})", 'users', $user->id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}
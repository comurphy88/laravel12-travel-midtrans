<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminSettingController extends Controller
{
    private const CACHE_KEY = 'admin.settings';
    private const CACHE_TTL = 3600;

    /** @return array<string, string> */
    private function rules(?int $ignoreId = null): array
    {
        $unique = $ignoreId
            ? "unique:settings,setting_key,{$ignoreId}"
            : 'unique:settings,setting_key';

        return [
            'setting_key'   => "required|string|max:255|{$unique}",
            'setting_value' => 'nullable|string',
            'setting_type'  => 'required|in:string,number,boolean,json',
            'description'   => 'nullable|string',
        ];
    }

    public function index(): View
    {
        $settings = Cache::remember(self::CACHE_KEY, self::CACHE_TTL, fn () =>
            Setting::orderBy('setting_key')->get()
        );

        return view('admin.settings.index', compact('settings'));
    }

    public function create(): View
    {
        return view('admin.settings.form');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $this->validateSettingValue($validated['setting_type'], $validated['setting_value'] ?? null);

        $setting = Setting::create($validated);

        ActivityLog::log('create', "Menambah pengaturan: {$setting->setting_key}", 'settings', $setting->id);
        Cache::forget(self::CACHE_KEY);

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan berhasil ditambahkan.');
    }

    public function edit(Setting $setting): View
    {
        return view('admin.settings.form', compact('setting'));
    }

    public function update(Request $request, Setting $setting): RedirectResponse
    {
        $validated = $request->validate($this->rules($setting->id));
        $this->validateSettingValue($validated['setting_type'], $validated['setting_value'] ?? null);

        $setting->update($validated);

        ActivityLog::log('update', "Mengubah pengaturan: {$setting->setting_key}", 'settings', $setting->id);
        Cache::forget(self::CACHE_KEY);

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan berhasil diperbarui.');
    }

    public function destroy(Setting $setting): RedirectResponse
    {
        ActivityLog::log('delete', "Menghapus pengaturan: {$setting->setting_key}", 'settings', $setting->id);
        $setting->delete();
        Cache::forget(self::CACHE_KEY);

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan berhasil dihapus.');
    }

    // -------------------------------------------------------------------------

    private function validateSettingValue(string $type, ?string $value): void
    {
        if ($value === null || $value === '') {
            return;
        }

        match ($type) {
            'number' => ! is_numeric($value) && throw ValidationException::withMessages([
                'setting_value' => 'Nilai harus berupa angka untuk tipe number.',
            ]),
            'json' => json_validate($value) === false && throw ValidationException::withMessages([
                'setting_value' => 'JSON tidak valid.',
            ]),
            default => null,
        };
    }
}
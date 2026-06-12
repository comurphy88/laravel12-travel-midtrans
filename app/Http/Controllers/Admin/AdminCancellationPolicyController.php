<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CancellationPolicy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCancellationPolicyController extends Controller
{
    /** @return array<string, string> */
    private function rules(): array
    {
        return [
            'name'                => 'required|string|max:255',
            'hours_before_travel' => 'required|integer|min:0',
            'refund_percentage'   => 'required|numeric|min:0|max:100',
            'description'         => 'nullable|string',
            'active'              => 'boolean',
        ];
    }

    public function index(): View
    {
        $policies = CancellationPolicy::orderBy('hours_before_travel')->paginate(15);

        return view('admin.cancellation-policies.index', compact('policies'));
    }

    public function create(): View
    {
        return view('admin.cancellation-policies.form');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $validated['description'] = isset($validated['description']) ? strip_tags($validated['description']) : null;
        $validated['active']      = $request->boolean('active');

        $policy = CancellationPolicy::create($validated);

        ActivityLog::log('create', "Menambah kebijakan pembatalan: {$policy->name}", 'cancellation_policies', $policy->id);

        return redirect()
            ->route('admin.cancellation-policies.index')
            ->with('success', 'Kebijakan pembatalan berhasil ditambahkan.');
    }

    public function edit(CancellationPolicy $cancellationPolicy): View
    {
        return view('admin.cancellation-policies.form', ['policy' => $cancellationPolicy]);
    }

    public function update(Request $request, CancellationPolicy $cancellationPolicy): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $validated['description'] = isset($validated['description']) ? strip_tags($validated['description']) : null;
        $validated['active']      = $request->boolean('active');

        $cancellationPolicy->update($validated);

        ActivityLog::log('update', "Mengubah kebijakan pembatalan: {$cancellationPolicy->name}", 'cancellation_policies', $cancellationPolicy->id);

        return redirect()
            ->route('admin.cancellation-policies.index')
            ->with('success', 'Kebijakan pembatalan berhasil diperbarui.');
    }

    public function destroy(CancellationPolicy $cancellationPolicy): RedirectResponse
    {
        ActivityLog::log('delete', "Menghapus kebijakan pembatalan: {$cancellationPolicy->name}", 'cancellation_policies', $cancellationPolicy->id);
        $cancellationPolicy->delete();

        return redirect()
            ->route('admin.cancellation-policies.index')
            ->with('success', 'Kebijakan pembatalan berhasil dihapus.');
    }
}
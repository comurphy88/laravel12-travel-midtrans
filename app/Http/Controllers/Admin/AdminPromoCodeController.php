<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\PromoCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPromoCodeController extends Controller
{
    /**
     * @param  int|null $ignoreId  Promo code ID to exclude from the unique check on update.
     * @return array<string, string>
     */
    private function rules(Request $request, ?int $ignoreId = null): array
    {
        $unique        = $ignoreId ? "unique:promo_codes,code,{$ignoreId}" : 'unique:promo_codes,code';
        $discountValue = 'required|numeric|min:0';

        if ($request->input('discount_type') === 'percentage') {
            $discountValue .= '|max:100';
        }

        return [
            'code'           => "required|string|max:50|{$unique}",
            'description'    => 'nullable|string|max:500',
            'discount_type'  => 'required|in:percentage,fixed',
            'discount_value' => $discountValue,
            'min_purchase'   => 'nullable|numeric|min:0',
            'max_discount'   => 'nullable|numeric|min:0',
            'usage_limit'    => 'nullable|integer|min:0',
            'valid_from'     => 'nullable|date',
            'valid_until'    => 'nullable|date|after_or_equal:valid_from',
            'active'         => 'boolean',
        ];
    }

    /** Apply shared defaults & sanitize after validation. */
    private function applyDefaults(array $validated, Request $request): array
    {
        $validated['active']       = $request->boolean('active');
        $validated['min_purchase'] = $validated['min_purchase'] ?? 0;
        $validated['max_discount'] = $validated['max_discount'] ?? 0;
        $validated['usage_limit']  = $validated['usage_limit']  ?? 0;
        $validated['description']  = isset($validated['description'])
            ? strip_tags($validated['description'])
            : null;

        return $validated;
    }

    public function index(): View
    {
        $promoCodes = PromoCode::orderByDesc('created_at')->paginate(15);

        return view('admin.promo-codes.index', compact('promoCodes'));
    }

    public function create(): View
    {
        return view('admin.promo-codes.form');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->applyDefaults($request->validate($this->rules($request)), $request);

        $promo = PromoCode::create($validated);

        ActivityLog::log('create', "Menambah kode promo: {$promo->code}", 'promo_codes', $promo->id);

        return redirect()->route('admin.promo-codes.index')->with('success', 'Kode promo berhasil ditambahkan.');
    }

    public function edit(PromoCode $promoCode): View
    {
        return view('admin.promo-codes.form', compact('promoCode'));
    }

    public function update(Request $request, PromoCode $promoCode): RedirectResponse
    {
        $validated = $this->applyDefaults($request->validate($this->rules($request, $promoCode->id)), $request);

        $promoCode->update($validated);

        ActivityLog::log('update', "Mengubah kode promo: {$promoCode->code}", 'promo_codes', $promoCode->id);

        return redirect()->route('admin.promo-codes.index')->with('success', 'Kode promo berhasil diperbarui.');
    }

    public function destroy(PromoCode $promoCode): RedirectResponse
    {
        if ($promoCode->bookings()->exists()) {
            return redirect()
                ->route('admin.promo-codes.index')
                ->with('error', 'Kode promo tidak bisa dihapus karena masih digunakan di pesanan.');
        }

        ActivityLog::log('delete', "Menghapus kode promo: {$promoCode->code}", 'promo_codes', $promoCode->id);
        $promoCode->delete();

        return redirect()->route('admin.promo-codes.index')->with('success', 'Kode promo berhasil dihapus.');
    }
}
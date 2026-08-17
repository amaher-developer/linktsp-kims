<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Loyalty\Models\LoyaltyRule;

class LoyaltyRuleController extends Controller
{
    public function index(): View
    {
        return view('admin::admin.loyalty-rules.index', [
            'rules' => LoyaltyRule::orderByDesc('priority')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin::admin.loyalty-rules.create');
    }

    public function store(Request $request): RedirectResponse
    {
        LoyaltyRule::create($this->validated($request));

        return redirect()->route('admin.loyalty-rules.index')->with('status', 'saved');
    }

    public function edit(LoyaltyRule $loyaltyRule): View
    {
        return view('admin::admin.loyalty-rules.edit', ['rule' => $loyaltyRule]);
    }

    public function update(Request $request, LoyaltyRule $loyaltyRule): RedirectResponse
    {
        $loyaltyRule->update($this->validated($request));

        return redirect()->route('admin.loyalty-rules.index')->with('status', 'saved');
    }

    public function destroy(LoyaltyRule $loyaltyRule): RedirectResponse
    {
        $loyaltyRule->delete();

        return redirect()->route('admin.loyalty-rules.index')->with('status', 'deleted');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'priority' => ['required', 'integer'],
            'earn_points_rate' => ['required', 'numeric'],
            'earn_amount_unit' => ['required', 'numeric'],
            'redeem_points_unit' => ['required', 'numeric'],
            'redeem_value' => ['required', 'numeric'],
            'minimum_redeem_points' => ['required', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
        ]);
    }
}

<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Catalog\Models\Product;
use Modules\Loyalty\Models\Reward;

class RewardController extends Controller
{
    public function index(): View
    {
        return view('admin::admin.rewards.index', ['rewards' => Reward::orderBy('name_en')->paginate(20)]);
    }

    public function create(): View
    {
        return view('admin::admin.rewards.create', ['products' => Product::orderBy('name_en')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Reward::create($this->validated($request));

        return redirect()->route('admin.rewards.index')->with('status', 'saved');
    }

    public function edit(Reward $reward): View
    {
        return view('admin::admin.rewards.edit', ['reward' => $reward, 'products' => Product::orderBy('name_en')->get()]);
    }

    public function update(Request $request, Reward $reward): RedirectResponse
    {
        $reward->update($this->validated($request));

        return redirect()->route('admin.rewards.index')->with('status', 'saved');
    }

    public function destroy(Reward $reward): RedirectResponse
    {
        $reward->delete();

        return redirect()->route('admin.rewards.index')->with('status', 'deleted');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'product_id' => ['nullable', 'exists:kims_products,id'],
            'name_en' => ['required', 'string', 'max:150'],
            'name_ar' => ['required', 'string', 'max:150'],
            'points_cost' => ['required', 'integer', 'min:1'],
            'reward_type' => ['required', 'in:product,discount'],
            'is_active' => ['sometimes', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
        ]);
    }
}

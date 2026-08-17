<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Catalog\Models\Branch;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Models\OptionGroup;
use Modules\Catalog\Models\Product;

class ProductController extends Controller
{
    public function index(): View
    {
        return view('admin::admin.products.index', [
            'products' => Product::with('category')->orderBy('name_en')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin::admin.products.create', ['categories' => Category::orderBy('name_en')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $product = Product::create($this->validated($request));

        return redirect()->route('admin.products.edit', $product)->with('status', 'product-saved');
    }

    public function edit(Product $product): View
    {
        $product->load('branches', 'optionGroups');

        return view('admin::admin.products.edit', [
            'product' => $product,
            'categories' => Category::orderBy('name_en')->get(),
            'branches' => Branch::orderBy('name_en')->get(),
            'optionGroups' => OptionGroup::orderBy('name_en')->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $product->update($this->validated($request));

        foreach ($request->input('branch_available', []) as $branchId => $flag) {
            $product->branches()->syncWithoutDetaching([
                $branchId => [
                    'is_available' => true,
                    'price_override' => $request->input("branch_price.$branchId") ?: null,
                ],
            ]);
        }

        $product->optionGroups()->sync($request->input('option_groups', []));

        return redirect()->route('admin.products.edit', $product)->with('status', 'product-saved');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('status', 'product-deleted');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'foodics_id' => ['required', 'integer'],
            'category_id' => ['nullable', 'exists:kims_categories,id'],
            'sku' => ['nullable', 'string', 'max:100'],
            'name_en' => ['required', 'string', 'max:150'],
            'name_ar' => ['required', 'string', 'max:150'],
            'description_en' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'is_available' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }
}

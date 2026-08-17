<?php

namespace Modules\Catalog\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Catalog\Http\Resources\ProductResource;
use Modules\Catalog\Models\Product;

class ProductController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $branchId = $request->integer('branch_id') ?: null;

        $products = Product::query()
            ->where('is_active', true)
            ->where('is_available', true)
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->integer('category_id')))
            ->when($branchId, fn ($q) => $q->whereHas('branches', fn ($b) => $b->where('branch_id', $branchId)->where('is_available', true)))
            ->when($branchId, fn ($q) => $q->with(['branches' => fn ($b) => $b->where('branch_id', $branchId)]))
            ->orderBy('name_en')
            ->get();

        return ProductResource::collection($products);
    }

    public function show(Request $request, Product $product): ProductResource
    {
        abort_unless($product->is_active, 404);

        $branchId = $request->integer('branch_id') ?: null;

        $product->load(['optionGroups.options']);

        if ($branchId) {
            $product->load(['branches' => fn ($b) => $b->where('branch_id', $branchId)]);
        }

        return new ProductResource($product);
    }
}

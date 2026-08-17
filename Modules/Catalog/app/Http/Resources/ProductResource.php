<?php

namespace Modules\Catalog\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \Modules\Catalog\Models\Product
 *
 * When the controller eager-loads `branches` scoped to a single branch
 * (via ->with(['branches' => fn ($q) => $q->where('branch_id', $id)])),
 * this resource exposes that branch's effective price/availability
 * instead of the branch-agnostic base_price.
 */
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $branchPivot = $this->relationLoaded('branches') ? $this->branches->first()?->pivot : null;

        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'sku' => $this->sku,
            'name_en' => $this->name_en,
            'name_ar' => $this->name_ar,
            'description_en' => $this->description_en,
            'description_ar' => $this->description_ar,
            'image_url' => $this->image_url,
            'base_price' => $this->base_price,
            'price' => $branchPivot?->price_override ?? $this->base_price,
            'is_available' => $branchPivot ? (bool) $branchPivot->is_available : $this->is_available,
            'option_groups' => OptionGroupResource::collection($this->whenLoaded('optionGroups')),
        ];
    }
}

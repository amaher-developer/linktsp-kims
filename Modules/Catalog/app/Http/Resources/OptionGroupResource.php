<?php

namespace Modules\Catalog\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \Modules\Catalog\Models\OptionGroup */
class OptionGroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name_en' => $this->name_en,
            'name_ar' => $this->name_ar,
            'min_select' => $this->min_select,
            'max_select' => $this->max_select,
            'is_required' => $this->is_required,
            'sort_order' => $this->sort_order,
            'options' => OptionResource::collection($this->whenLoaded('options')),
        ];
    }
}

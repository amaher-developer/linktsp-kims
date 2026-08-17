<?php

namespace Modules\Catalog\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \Modules\Catalog\Models\Option */
class OptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'option_group_id' => $this->option_group_id,
            'name_en' => $this->name_en,
            'name_ar' => $this->name_ar,
            'price_delta' => $this->price_delta,
            'sort_order' => $this->sort_order,
        ];
    }
}

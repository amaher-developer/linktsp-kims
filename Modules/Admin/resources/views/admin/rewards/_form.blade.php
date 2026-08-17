@php $reward = $reward ?? null; @endphp

<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="name_en" :value="__('admin.name_en')" />
        <x-text-input id="name_en" name="name_en" type="text" class="block mt-1 w-full" :value="old('name_en', $reward?->name_en)" required />
        <x-input-error class="mt-2" :messages="$errors->get('name_en')" />
    </div>
    <div>
        <x-input-label for="name_ar" :value="__('admin.name_ar')" />
        <x-text-input id="name_ar" name="name_ar" type="text" dir="rtl" class="block mt-1 w-full" :value="old('name_ar', $reward?->name_ar)" required />
        <x-input-error class="mt-2" :messages="$errors->get('name_ar')" />
    </div>
    <div>
        <x-input-label for="product_id" :value="__('admin.linked_product')" />
        <select id="product_id" name="product_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
            <option value="">{{ __('admin.none') }}</option>
            @foreach ($products as $product)
                <option value="{{ $product->id }}" @selected(old('product_id', $reward?->product_id) == $product->id)>{{ $product->name_en }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <x-input-label for="reward_type" :value="__('admin.type')" />
        <select id="reward_type" name="reward_type" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>
            <option value="product" @selected(old('reward_type', $reward?->reward_type) === 'product')>{{ __('admin.product') }}</option>
            <option value="discount" @selected(old('reward_type', $reward?->reward_type) === 'discount')>{{ __('admin.discount') }}</option>
        </select>
    </div>
    <div>
        <x-input-label for="points_cost" :value="__('admin.points_cost')" />
        <x-text-input id="points_cost" name="points_cost" type="number" class="block mt-1 w-full" :value="old('points_cost', $reward?->points_cost)" required />
    </div>
    <div>
        <x-input-label for="starts_at" :value="__('admin.starts_at')" />
        <x-text-input id="starts_at" name="starts_at" type="datetime-local" class="block mt-1 w-full" :value="old('starts_at', $reward?->starts_at?->format('Y-m-d\TH:i'))" />
    </div>
    <div>
        <x-input-label for="ends_at" :value="__('admin.ends_at')" />
        <x-text-input id="ends_at" name="ends_at" type="datetime-local" class="block mt-1 w-full" :value="old('ends_at', $reward?->ends_at?->format('Y-m-d\TH:i'))" />
    </div>
</div>

<label class="inline-flex items-center pt-2">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300" @checked(old('is_active', $reward?->is_active ?? true))>
    <span class="ms-2 text-sm text-gray-700">{{ __('admin.active') }}</span>
</label>

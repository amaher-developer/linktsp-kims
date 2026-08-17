@php $product = $product ?? null; @endphp

<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="foodics_id" :value="__('admin.foodics_id')" />
        <x-text-input id="foodics_id" name="foodics_id" type="number" class="block mt-1 w-full" :value="old('foodics_id', $product?->foodics_id)" required />
        <x-input-error class="mt-2" :messages="$errors->get('foodics_id')" />
    </div>
    <div>
        <x-input-label for="category_id" :value="__('admin.category')" />
        <select id="category_id" name="category_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
            <option value="">{{ __('admin.none') }}</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $product?->category_id) == $category->id)>{{ $category->name_en }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <x-input-label for="name_en" :value="__('admin.name_en')" />
        <x-text-input id="name_en" name="name_en" type="text" class="block mt-1 w-full" :value="old('name_en', $product?->name_en)" required />
        <x-input-error class="mt-2" :messages="$errors->get('name_en')" />
    </div>
    <div>
        <x-input-label for="name_ar" :value="__('admin.name_ar')" />
        <x-text-input id="name_ar" name="name_ar" type="text" class="block mt-1 w-full" dir="rtl" :value="old('name_ar', $product?->name_ar)" required />
        <x-input-error class="mt-2" :messages="$errors->get('name_ar')" />
    </div>
    <div>
        <x-input-label for="sku" :value="__('admin.sku')" />
        <x-text-input id="sku" name="sku" type="text" class="block mt-1 w-full" :value="old('sku', $product?->sku)" />
    </div>
    <div>
        <x-input-label for="base_price" :value="__('admin.base_price')" />
        <x-text-input id="base_price" name="base_price" type="number" step="0.01" class="block mt-1 w-full" :value="old('base_price', $product?->base_price)" required />
        <x-input-error class="mt-2" :messages="$errors->get('base_price')" />
    </div>
    <div class="col-span-2">
        <x-input-label for="description_en" :value="__('admin.description_en')" />
        <textarea id="description_en" name="description_en" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">{{ old('description_en', $product?->description_en) }}</textarea>
    </div>
    <div class="col-span-2">
        <x-input-label for="description_ar" :value="__('admin.description_ar')" />
        <textarea id="description_ar" name="description_ar" dir="rtl" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">{{ old('description_ar', $product?->description_ar) }}</textarea>
    </div>
</div>

<div class="flex gap-6 pt-2">
    <label class="inline-flex items-center">
        <input type="hidden" name="is_available" value="0">
        <input type="checkbox" name="is_available" value="1" class="rounded border-gray-300" @checked(old('is_available', $product?->is_available ?? true))>
        <span class="ms-2 text-sm text-gray-700">{{ __('admin.available') }}</span>
    </label>
    <label class="inline-flex items-center">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300" @checked(old('is_active', $product?->is_active ?? true))>
        <span class="ms-2 text-sm text-gray-700">{{ __('admin.active') }}</span>
    </label>
</div>

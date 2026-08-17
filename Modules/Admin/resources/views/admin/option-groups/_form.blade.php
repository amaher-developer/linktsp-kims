@php $optionGroup = $optionGroup ?? null; @endphp

<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="foodics_id" :value="__('admin.foodics_id')" />
        <x-text-input id="foodics_id" name="foodics_id" type="number" class="block mt-1 w-full" :value="old('foodics_id', $optionGroup?->foodics_id)" />
    </div>
    <div></div>
    <div>
        <x-input-label for="name_en" :value="__('admin.name_en')" />
        <x-text-input id="name_en" name="name_en" type="text" class="block mt-1 w-full" :value="old('name_en', $optionGroup?->name_en)" required />
        <x-input-error class="mt-2" :messages="$errors->get('name_en')" />
    </div>
    <div>
        <x-input-label for="name_ar" :value="__('admin.name_ar')" />
        <x-text-input id="name_ar" name="name_ar" type="text" class="block mt-1 w-full" dir="rtl" :value="old('name_ar', $optionGroup?->name_ar)" required />
        <x-input-error class="mt-2" :messages="$errors->get('name_ar')" />
    </div>
    <div>
        <x-input-label for="min_select" :value="__('admin.min_select')" />
        <x-text-input id="min_select" name="min_select" type="number" class="block mt-1 w-full" :value="old('min_select', $optionGroup?->min_select ?? 0)" required />
    </div>
    <div>
        <x-input-label for="max_select" :value="__('admin.max_select')" />
        <x-text-input id="max_select" name="max_select" type="number" class="block mt-1 w-full" :value="old('max_select', $optionGroup?->max_select ?? 1)" required />
    </div>
    <div>
        <x-input-label for="sort_order" :value="__('admin.sort_order')" />
        <x-text-input id="sort_order" name="sort_order" type="number" class="block mt-1 w-full" :value="old('sort_order', $optionGroup?->sort_order ?? 0)" />
    </div>
</div>

<div class="flex gap-6 pt-2">
    <label class="inline-flex items-center">
        <input type="hidden" name="is_required" value="0">
        <input type="checkbox" name="is_required" value="1" class="rounded border-gray-300" @checked(old('is_required', $optionGroup?->is_required))>
        <span class="ms-2 text-sm text-gray-700">{{ __('admin.required') }}</span>
    </label>
    <label class="inline-flex items-center">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300" @checked(old('is_active', $optionGroup?->is_active ?? true))>
        <span class="ms-2 text-sm text-gray-700">{{ __('admin.active') }}</span>
    </label>
</div>

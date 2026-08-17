@php $category = $category ?? null; @endphp

<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="foodics_id" :value="__('admin.foodics_id')" />
        <x-text-input id="foodics_id" name="foodics_id" type="number" class="block mt-1 w-full" :value="old('foodics_id', $category?->foodics_id)" required />
        <x-input-error class="mt-2" :messages="$errors->get('foodics_id')" />
    </div>
    <div>
        <x-input-label for="parent_id" :value="__('admin.parent_category')" />
        <select id="parent_id" name="parent_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
            <option value="">{{ __('admin.none') }}</option>
            @foreach ($categories as $option)
                <option value="{{ $option->id }}" @selected(old('parent_id', $category?->parent_id) == $option->id)>{{ $option->name_en }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <x-input-label for="name_en" :value="__('admin.name_en')" />
        <x-text-input id="name_en" name="name_en" type="text" class="block mt-1 w-full" :value="old('name_en', $category?->name_en)" required />
        <x-input-error class="mt-2" :messages="$errors->get('name_en')" />
    </div>
    <div>
        <x-input-label for="name_ar" :value="__('admin.name_ar')" />
        <x-text-input id="name_ar" name="name_ar" type="text" class="block mt-1 w-full" dir="rtl" :value="old('name_ar', $category?->name_ar)" required />
        <x-input-error class="mt-2" :messages="$errors->get('name_ar')" />
    </div>
    <div>
        <x-input-label for="sort_order" :value="__('admin.sort_order')" />
        <x-text-input id="sort_order" name="sort_order" type="number" class="block mt-1 w-full" :value="old('sort_order', $category?->sort_order ?? 0)" />
    </div>
</div>

<label class="inline-flex items-center pt-2">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300" @checked(old('is_active', $category?->is_active ?? true))>
    <span class="ms-2 text-sm text-gray-700">{{ __('admin.active') }}</span>
</label>

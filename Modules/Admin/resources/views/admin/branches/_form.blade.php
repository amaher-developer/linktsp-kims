@php $branch = $branch ?? null; @endphp

<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="foodics_id" :value="__('admin.foodics_id')" />
        <x-text-input id="foodics_id" name="foodics_id" type="number" class="block mt-1 w-full" :value="old('foodics_id', $branch?->foodics_id)" required />
        <x-input-error class="mt-2" :messages="$errors->get('foodics_id')" />
    </div>
    <div>
        <x-input-label for="code" :value="__('admin.code')" />
        <x-text-input id="code" name="code" type="text" class="block mt-1 w-full" :value="old('code', $branch?->code)" />
        <x-input-error class="mt-2" :messages="$errors->get('code')" />
    </div>
    <div>
        <x-input-label for="name_en" :value="__('admin.name_en')" />
        <x-text-input id="name_en" name="name_en" type="text" class="block mt-1 w-full" :value="old('name_en', $branch?->name_en)" required />
        <x-input-error class="mt-2" :messages="$errors->get('name_en')" />
    </div>
    <div>
        <x-input-label for="name_ar" :value="__('admin.name_ar')" />
        <x-text-input id="name_ar" name="name_ar" type="text" class="block mt-1 w-full" dir="rtl" :value="old('name_ar', $branch?->name_ar)" required />
        <x-input-error class="mt-2" :messages="$errors->get('name_ar')" />
    </div>
    <div class="col-span-2">
        <x-input-label for="address" :value="__('admin.address')" />
        <textarea id="address" name="address" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">{{ old('address', $branch?->address) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('address')" />
    </div>
    <div>
        <x-input-label for="city" :value="__('admin.city')" />
        <x-text-input id="city" name="city" type="text" class="block mt-1 w-full" :value="old('city', $branch?->city)" />
        <x-input-error class="mt-2" :messages="$errors->get('city')" />
    </div>
    <div>
        <x-input-label for="phone" :value="__('admin.phone')" />
        <x-text-input id="phone" name="phone" type="text" class="block mt-1 w-full" :value="old('phone', $branch?->phone)" />
        <x-input-error class="mt-2" :messages="$errors->get('phone')" />
    </div>
    <div>
        <x-input-label for="latitude" :value="__('admin.latitude')" />
        <x-text-input id="latitude" name="latitude" type="text" class="block mt-1 w-full" :value="old('latitude', $branch?->latitude)" />
    </div>
    <div>
        <x-input-label for="longitude" :value="__('admin.longitude')" />
        <x-text-input id="longitude" name="longitude" type="text" class="block mt-1 w-full" :value="old('longitude', $branch?->longitude)" />
    </div>
</div>

<div class="flex gap-6 pt-2">
    <label class="inline-flex items-center">
        <input type="hidden" name="accepts_grab_go" value="0">
        <input type="checkbox" name="accepts_grab_go" value="1" class="rounded border-gray-300" @checked(old('accepts_grab_go', $branch?->accepts_grab_go ?? true))>
        <span class="ms-2 text-sm text-gray-700">{{ __('admin.accepts_grab_go') }}</span>
    </label>
    <label class="inline-flex items-center">
        <input type="hidden" name="accepts_dine_in" value="0">
        <input type="checkbox" name="accepts_dine_in" value="1" class="rounded border-gray-300" @checked(old('accepts_dine_in', $branch?->accepts_dine_in ?? true))>
        <span class="ms-2 text-sm text-gray-700">{{ __('admin.accepts_dine_in') }}</span>
    </label>
    <label class="inline-flex items-center">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300" @checked(old('is_active', $branch?->is_active ?? true))>
        <span class="ms-2 text-sm text-gray-700">{{ __('admin.active') }}</span>
    </label>
</div>

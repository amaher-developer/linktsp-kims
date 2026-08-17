<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('admin.edit_option_group') }}: {{ $optionGroup->name_en }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="text-sm text-green-600">{{ __('admin.saved') }}</div>
            @endif

            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.option-groups.update', $optionGroup) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    @include('admin::admin.option-groups._form')
                    <x-primary-button>{{ __('admin.save_option_group') }}</x-primary-button>
                </form>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">{{ __('admin.options') }}</h3>

                <table class="min-w-full divide-y divide-gray-200 text-sm mb-6">
                    <thead>
                        <tr class="text-start text-gray-500">
                            <th class="py-2 pe-4">{{ __('admin.name') }}</th>
                            <th class="py-2 pe-4">{{ __('admin.price_delta') }}</th>
                            <th class="py-2 pe-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($optionGroup->options as $option)
                            @php $formId = 'option-form-'.$option->id; @endphp
                            <tr>
                                <td class="py-2 pe-4">
                                    <input form="{{ $formId }}" type="text" name="name_en" value="{{ $option->name_en }}" class="border-gray-300 rounded-md shadow-sm text-sm w-32">
                                    <input form="{{ $formId }}" type="text" name="name_ar" dir="rtl" value="{{ $option->name_ar }}" class="border-gray-300 rounded-md shadow-sm text-sm w-32">
                                </td>
                                <td class="py-2 pe-4">
                                    <input form="{{ $formId }}" type="number" step="0.01" name="price_delta" value="{{ $option->price_delta }}" class="border-gray-300 rounded-md shadow-sm text-sm w-24">
                                </td>
                                <td class="py-2 pe-4 text-end space-x-2 rtl:space-x-reverse">
                                    <button form="{{ $formId }}" class="text-indigo-600 hover:underline text-sm">{{ __('admin.save') }}</button>
                                    <button form="delete-{{ $formId }}" class="text-red-600 hover:underline text-sm">{{ __('admin.delete') }}</button>
                                </td>
                            </tr>
                            <tr class="hidden">
                                <td colspan="3">
                                    <form id="{{ $formId }}" method="POST" action="{{ route('admin.option-groups.options.update', [$optionGroup, $option]) }}">
                                        @csrf
                                        @method('PUT')
                                    </form>
                                    <form id="delete-{{ $formId }}" method="POST" action="{{ route('admin.option-groups.options.destroy', [$optionGroup, $option]) }}" onsubmit="return confirm('{{ __('admin.delete_this_option') }}');">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 text-gray-500">{{ __('admin.no_options_yet') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <form method="POST" action="{{ route('admin.option-groups.options.store', $optionGroup) }}" class="grid grid-cols-4 gap-2 items-end">
                    @csrf
                    <div>
                        <x-input-label for="new_name_en" :value="__('admin.name_en')" />
                        <x-text-input id="new_name_en" name="name_en" type="text" class="block mt-1 w-full" required />
                    </div>
                    <div>
                        <x-input-label for="new_name_ar" :value="__('admin.name_ar')" />
                        <x-text-input id="new_name_ar" name="name_ar" type="text" dir="rtl" class="block mt-1 w-full" required />
                    </div>
                    <div>
                        <x-input-label for="new_price_delta" :value="__('admin.price_delta')" />
                        <x-text-input id="new_price_delta" name="price_delta" type="number" step="0.01" value="0" class="block mt-1 w-full" required />
                    </div>
                    <x-primary-button>{{ __('admin.add_option') }}</x-primary-button>
                </form>
            </div>

            <form method="POST" action="{{ route('admin.option-groups.destroy', $optionGroup) }}" onsubmit="return confirm('{{ __('admin.delete_this_option_group') }}');">
                @csrf
                @method('DELETE')
                <x-danger-button>{{ __('admin.delete_option_group') }}</x-danger-button>
            </form>
        </div>
    </div>
</x-app-layout>

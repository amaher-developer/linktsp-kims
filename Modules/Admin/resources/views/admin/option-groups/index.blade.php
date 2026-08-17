<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('admin.option_groups') }}</h2>
            <a href="{{ route('admin.option-groups.create') }}" class="text-sm text-indigo-600 hover:underline">{{ __('admin.new_option_group_2') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="text-start text-gray-500">
                            <th class="py-2 pe-4">{{ __('admin.name') }}</th>
                            <th class="py-2 pe-4">{{ __('admin.select') }}</th>
                            <th class="py-2 pe-4">{{ __('admin.options') }}</th>
                            <th class="py-2 pe-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($optionGroups as $group)
                            <tr>
                                <td class="py-2 pe-4">{{ $group->name_en }} / {{ $group->name_ar }}</td>
                                <td class="py-2 pe-4">{{ $group->min_select }}–{{ $group->max_select }}</td>
                                <td class="py-2 pe-4">{{ $group->options_count }}</td>
                                <td class="py-2 pe-4 text-end">
                                    <a class="text-indigo-600 hover:underline" href="{{ route('admin.option-groups.edit', $group) }}">{{ __('admin.edit') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-4 text-gray-500">{{ __('admin.no_option_groups_yet') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $optionGroups->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('admin.categories') }}</h2>
            <a href="{{ route('admin.categories.create') }}" class="text-sm text-indigo-600 hover:underline">{{ __('admin.new_category_2') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="text-start text-gray-500">
                            <th class="py-2 pe-4">{{ __('admin.name') }}</th>
                            <th class="py-2 pe-4">{{ __('admin.parent') }}</th>
                            <th class="py-2 pe-4">{{ __('admin.sort') }}</th>
                            <th class="py-2 pe-4">{{ __('admin.active') }}</th>
                            <th class="py-2 pe-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($categories as $category)
                            <tr>
                                <td class="py-2 pe-4">{{ $category->name_en }} / {{ $category->name_ar }}</td>
                                <td class="py-2 pe-4">{{ $category->parent?->name_en ?? '—' }}</td>
                                <td class="py-2 pe-4">{{ $category->sort_order }}</td>
                                <td class="py-2 pe-4">{{ $category->is_active ? __('admin.yes') : __('admin.no') }}</td>
                                <td class="py-2 pe-4 text-end">
                                    <a class="text-indigo-600 hover:underline" href="{{ route('admin.categories.edit', $category) }}">{{ __('admin.edit') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-4 text-gray-500">{{ __('admin.no_categories_yet') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $categories->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>

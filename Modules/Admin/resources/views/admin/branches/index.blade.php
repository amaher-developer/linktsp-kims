<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('admin.branches') }}</h2>
            <a href="{{ route('admin.branches.create') }}" class="text-sm text-indigo-600 hover:underline">{{ __('admin.new_branch_2') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                @if (session('status'))
                    <div class="mb-4 text-sm text-green-600">{{ __('admin.saved') }}</div>
                @endif
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="text-start text-gray-500">
                            <th class="py-2 pe-4">{{ __('admin.name') }}</th>
                            <th class="py-2 pe-4">{{ __('admin.code') }}</th>
                            <th class="py-2 pe-4">{{ __('admin.city') }}</th>
                            <th class="py-2 pe-4">{{ __('admin.active') }}</th>
                            <th class="py-2 pe-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($branches as $branch)
                            <tr>
                                <td class="py-2 pe-4">{{ $branch->name_en }} / {{ $branch->name_ar }}</td>
                                <td class="py-2 pe-4">{{ $branch->code }}</td>
                                <td class="py-2 pe-4">{{ $branch->city }}</td>
                                <td class="py-2 pe-4">{{ $branch->is_active ? __('admin.yes') : __('admin.no') }}</td>
                                <td class="py-2 pe-4 text-end">
                                    <a class="text-indigo-600 hover:underline" href="{{ route('admin.branches.edit', $branch) }}">{{ __('admin.edit') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-4 text-gray-500">{{ __('admin.no_branches_yet') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $branches->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>

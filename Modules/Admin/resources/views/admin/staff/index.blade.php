<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('admin.staff') }}</h2>
            <a href="{{ route('admin.staff.create') }}" class="text-sm text-indigo-600 hover:underline">{{ __('admin.new_staff_2') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="text-start text-gray-500">
                            <th class="py-2 pe-4">{{ __('admin.name') }}</th>
                            <th class="py-2 pe-4">{{ __('admin.role') }}</th>
                            <th class="py-2 pe-4">{{ __('admin.email') }}</th>
                            <th class="py-2 pe-4">{{ __('admin.active') }}</th>
                            <th class="py-2 pe-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($staff as $member)
                            <tr>
                                <td class="py-2 pe-4">{{ $member->name }}</td>
                                <td class="py-2 pe-4">{{ $member->role->name }}</td>
                                <td class="py-2 pe-4">{{ $member->email }}</td>
                                <td class="py-2 pe-4">{{ $member->is_active ? __('admin.yes') : __('admin.no') }}</td>
                                <td class="py-2 pe-4 text-end">
                                    <a class="text-indigo-600 hover:underline" href="{{ route('admin.staff.edit', $member) }}">{{ __('admin.edit') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-4 text-gray-500">{{ __('admin.no_staff_yet') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $staff->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>

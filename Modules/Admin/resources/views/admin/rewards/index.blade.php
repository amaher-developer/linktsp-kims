<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('admin.rewards') }}</h2>
            <a href="{{ route('admin.rewards.create') }}" class="text-sm text-indigo-600 hover:underline">{{ __('admin.new_reward_2') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="text-start text-gray-500">
                            <th class="py-2 pe-4">{{ __('admin.name') }}</th>
                            <th class="py-2 pe-4">{{ __('admin.type') }}</th>
                            <th class="py-2 pe-4">{{ __('admin.points') }}</th>
                            <th class="py-2 pe-4">{{ __('admin.active') }}</th>
                            <th class="py-2 pe-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($rewards as $reward)
                            <tr>
                                <td class="py-2 pe-4">{{ $reward->name_en }} / {{ $reward->name_ar }}</td>
                                <td class="py-2 pe-4">{{ $reward->reward_type }}</td>
                                <td class="py-2 pe-4">{{ $reward->points_cost }}</td>
                                <td class="py-2 pe-4">{{ $reward->is_active ? __('admin.yes') : __('admin.no') }}</td>
                                <td class="py-2 pe-4 text-end">
                                    <a class="text-indigo-600 hover:underline" href="{{ route('admin.rewards.edit', $reward) }}">{{ __('admin.edit') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-4 text-gray-500">{{ __('admin.no_rewards_yet') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $rewards->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>

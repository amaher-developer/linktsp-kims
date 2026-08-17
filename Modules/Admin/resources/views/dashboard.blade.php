<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('admin.dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white shadow-sm ring-1 ring-gray-100 sm:rounded-xl p-6 flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">{{ __('admin.branches') }}</div>
                        <div class="text-3xl font-semibold text-gray-900">{{ $branchCount }}</div>
                    </div>
                </div>
                <div class="bg-white shadow-sm ring-1 ring-gray-100 sm:rounded-xl p-6 flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">{{ __('admin.products') }}</div>
                        <div class="text-3xl font-semibold text-gray-900">{{ $productCount }}</div>
                    </div>
                </div>
                <div class="bg-white shadow-sm ring-1 ring-gray-100 sm:rounded-xl p-6 flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">{{ __('admin.customers') }}</div>
                        <div class="text-3xl font-semibold text-gray-900">{{ $customerCount }}</div>
                    </div>
                </div>
                <div class="bg-white shadow-sm ring-1 ring-gray-100 sm:rounded-xl p-6 flex items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-rose-50 text-rose-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">{{ __('admin.open_orders') }}</div>
                        <div class="text-3xl font-semibold text-gray-900">{{ $openOrderCount }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm ring-1 ring-gray-100 sm:rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('admin.recent_orders') }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50">
                            <tr class="text-start text-gray-500">
                                <th class="py-3 px-6 text-start font-medium">{{ __('admin.order') }}</th>
                                <th class="py-3 px-6 text-start font-medium">{{ __('admin.branch') }}</th>
                                <th class="py-3 px-6 text-start font-medium">{{ __('admin.customer') }}</th>
                                <th class="py-3 px-6 text-start font-medium">{{ __('admin.status') }}</th>
                                <th class="py-3 px-6 text-start font-medium">{{ __('admin.total') }}</th>
                                <th class="py-3 px-6 text-start font-medium">{{ __('admin.placed') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($recentOrders as $order)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-6">
                                        <a class="text-indigo-600 hover:underline font-medium" href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a>
                                    </td>
                                    <td class="py-3 px-6">{{ app()->getLocale() === 'ar' ? $order->branch->name_ar : $order->branch->name_en }}</td>
                                    <td class="py-3 px-6">{{ $order->customer->first_name }} {{ $order->customer->last_name }}</td>
                                    <td class="py-3 px-6">
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-700">{{ $order->status->value }}</span>
                                    </td>
                                    <td class="py-3 px-6">{{ number_format($order->total_amount, 2) }}</td>
                                    <td class="py-3 px-6 text-gray-500">{{ $order->placed_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="py-6 px-6 text-center text-gray-500">{{ __('admin.no_orders_yet') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

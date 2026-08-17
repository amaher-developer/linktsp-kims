<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('admin.orders') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <form method="GET" class="mb-4 flex items-center gap-2 text-sm">
                    <label for="status">{{ __('admin.filter_by_status') }}</label>
                    <select id="status" name="status" onchange="this.form.submit()" class="border-gray-300 rounded-md shadow-sm text-sm">
                        <option value="">{{ __('admin.all') }}</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ ucfirst($status->value) }}</option>
                        @endforeach
                    </select>
                </form>

                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="text-start text-gray-500">
                            <th class="py-2 pe-4">{{ __('admin.order') }}</th>
                            <th class="py-2 pe-4">{{ __('admin.branch') }}</th>
                            <th class="py-2 pe-4">{{ __('admin.customer') }}</th>
                            <th class="py-2 pe-4">{{ __('admin.type') }}</th>
                            <th class="py-2 pe-4">{{ __('admin.status') }}</th>
                            <th class="py-2 pe-4">{{ __('admin.total') }}</th>
                            <th class="py-2 pe-4">{{ __('admin.placed') }}</th>
                            <th class="py-2 pe-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($orders as $order)
                            <tr>
                                <td class="py-2 pe-4">{{ $order->order_number }}</td>
                                <td class="py-2 pe-4">{{ $order->branch->name_en }}</td>
                                <td class="py-2 pe-4">{{ $order->customer->first_name }} {{ $order->customer->last_name }}</td>
                                <td class="py-2 pe-4">{{ $order->order_type }}</td>
                                <td class="py-2 pe-4">{{ $order->status->value }}</td>
                                <td class="py-2 pe-4">{{ number_format($order->total_amount, 2) }}</td>
                                <td class="py-2 pe-4">{{ $order->placed_at->format('Y-m-d H:i') }}</td>
                                <td class="py-2 pe-4 text-end">
                                    <a class="text-indigo-600 hover:underline" href="{{ route('admin.orders.show', $order) }}">{{ __('admin.view') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="py-4 text-gray-500">{{ __('admin.no_orders_yet') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $orders->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Reporte: {{ $raffle->name }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total</div>
                    <div class="text-2xl font-bold">{{ $summary['total'] }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Vendidos</div>
                    <div class="text-2xl font-bold text-green-500">{{ $summary['paid'] }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Reservados</div>
                    <div class="text-2xl font-bold text-yellow-500">{{ $summary['reserved'] }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Disponibles</div>
                    <div class="text-2xl font-bold text-blue-500">{{ $summary['available'] }}</div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Números</h3>
                        <a href="{{ route('admin.reports.export.csv', ['raffle_id' => $raffle->id]) }}" class="px-3 py-2 bg-green-600 text-white rounded">Exportar CSV</a>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participante</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($numbers as $num)
                                <tr>
                                    <td class="px-4 py-3">{{ $num->number }}</td>
                                    <td class="px-4 py-3">{{ ucfirst($num->status) }}</td>
                                    <td class="px-4 py-3">{{ $num->participant?->name }}</td>
                                    <td class="px-4 py-3">{{ $num->participant?->phone }}</td>
                                    <td class="px-4 py-3">{{ $num->participant?->email }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Reportes de Rifas
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Navegación de reportes -->
            <div class="mb-6">
                <div class="flex space-x-4">
                    <a href="{{ route('admin.reports.index') }}"
                       class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Reportes de Rifas
                    </a>
                    <a href="{{ route('admin.reports.winners') }}"
                       class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        Reporte de Ganadores
                    </a>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rifa</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sorteo</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendidos</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($raffles as $raffle)
                                <tr>
                                    <td class="px-4 py-3">{{ $raffle->name }}</td>
                                    <td class="px-4 py-3">{{ \Carbon\Carbon::parse($raffle->draw_date)->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3">{{ $raffle->paid_numbers_count }}</td>
                                    <td class="px-4 py-3 text-right space-x-2">
                                        <a href="{{ route('admin.reports.raffle', $raffle) }}" class="px-3 py-1 bg-indigo-600 text-white rounded">Ver</a>
                                        <a href="{{ route('admin.reports.export.csv', ['raffle_id' => $raffle->id]) }}" class="px-3 py-1 bg-green-600 text-white rounded">Export CSV</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $raffles->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

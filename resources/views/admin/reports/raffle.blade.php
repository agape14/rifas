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
                    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 mb-4">
                        <div>
                            <h3 class="text-lg font-semibold">Números</h3>
                            @if(!empty($q))
                                <p class="text-xs text-gray-500 dark:text-gray-400">Filtro: "{{ $q }}"</p>
                            @endif
                        </div>
                        <form method="GET" action="{{ route('admin.reports.raffle', $raffle) }}" class="flex items-center gap-2">
                            <input type="hidden" name="page" value="1">
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar número, estado, nombre, teléfono, email" class="w-72 px-3 py-2 rounded border dark:bg-gray-700 dark:border-gray-600" />
                            <button type="submit" class="px-3 py-2 bg-indigo-600 text-white rounded">Buscar</button>
                            @if(request('q'))
                                <a href="{{ route('admin.reports.raffle', $raffle) }}" class="px-3 py-2 bg-gray-300 dark:bg-gray-700 dark:text-gray-100 text-gray-800 rounded">Limpiar</a>
                            @endif
                        </form>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.reports.export.csv', ['raffle_id' => $raffle->id, 'q' => request('q')]) }}" class="px-3 py-2 bg-green-600 text-white rounded">Exportar CSV</a>
                            <a href="{{ route('admin.reports.export.csv', ['raffle_id' => $raffle->id, 'q' => request('q'), 'excel' => 1]) }}" class="px-3 py-2 bg-emerald-600 text-white rounded">Exportar Excel</a>
                        </div>
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

                    <div class="mt-4">
                        {{ $numbers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

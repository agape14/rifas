<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Reporte de Ganadores
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

            <!-- Selector de rifa y filtros -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Filtros y Selección</h3>
                    <form method="GET" action="{{ route('admin.reports.winners') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <div>
                            <label for="raffle_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Rifa
                            </label>
                            <select name="raffle_id" id="raffle_id" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todas las rifas...</option>
                                @foreach($raffles as $raffle)
                                    <option value="{{ $raffle->id }}" {{ request('raffle_id') == $raffle->id ? 'selected' : '' }}>
                                        {{ $raffle->name }} ({{ $raffle->winners_count }} ganadores)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="date_range" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Rango de fechas
                            </label>
                            <select name="date_range" id="date_range" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500" onchange="updateDateRange()">
                                <option value="custom" {{ request('date_range') == 'custom' ? 'selected' : '' }}>Personalizado</option>
                                <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Hoy</option>
                                <option value="7days" {{ request('date_range') == '7days' ? 'selected' : '' }}>Últimos 7 días</option>
                                <option value="15days" {{ request('date_range') == '15days' ? 'selected' : '' }}>Últimos 15 días</option>
                                <option value="30days" {{ request('date_range') == '30days' ? 'selected' : '' }}>Últimos 30 días</option>
                                <option value="quarter" {{ request('date_range') == 'quarter' ? 'selected' : '' }}>Trimestre actual</option>
                                <option value="semester" {{ request('date_range') == 'semester' ? 'selected' : '' }}>Semestre actual</option>
                                <option value="year" {{ request('date_range') == 'year' ? 'selected' : '' }}>Año actual</option>
                            </select>
                        </div>
                        <div>
                            <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Fecha desde
                            </label>
                            <input type="date" name="date_from" id="date_from" value="{{ request('date_from', date('Y-m-d')) }}"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Fecha hasta
                            </label>
                            <input type="date" name="date_to" id="date_to" value="{{ request('date_to', date('Y-m-d')) }}"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="md:col-span-3 flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Filtrar
                            </button>
                            <a href="{{ route('admin.reports.winners') }}" class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-400 dark:hover:bg-gray-600">
                                Limpiar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            @if($selectedRaffle)
                <!-- Información de la rifa seleccionada -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h3 class="text-xl font-bold">{{ $selectedRaffle->name }}</h3>
                                <p class="text-gray-600 dark:text-gray-400">
                                    Fecha del sorteo: {{ \Carbon\Carbon::parse($selectedRaffle->draw_date)->format('d/m/Y H:i') }}
                                </p>
                                <p class="text-gray-600 dark:text-gray-400">
                                    Total de ganadores: {{ $winners->count() }}
                                </p>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('admin.reports.winners.export.csv', ['raffle_id' => $selectedRaffle->id, 'date_from' => request('date_from'), 'date_to' => request('date_to')]) }}"
                                   class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Exportar CSV
                                </a>
                                <a href="{{ route('admin.reports.winners.export.pdf', ['raffle_id' => $selectedRaffle->id, 'date_from' => request('date_from'), 'date_to' => request('date_to')]) }}"
                                   class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    Exportar PDF
                                </a>
                                <a href="{{ route('admin.reports.winners.export.pdf.simple', ['raffle_id' => $selectedRaffle->id, 'date_from' => request('date_from'), 'date_to' => request('date_to')]) }}"
                                   class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500"
                                   title="Versión simple del PDF (más compatible)">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    PDF Simple
                                </a>
                                <button type="button" onclick="toggleCharts()"
                                        class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    Ver Gráficos
                                </button>
                                <a href="{{ route('admin.reports.winners') }}"
                                   class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                    Limpiar
                                </a>
                            </div>
                        </div>

                        <!-- Estadísticas rápidas -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                <div class="flex items-center">
                                    <div class="p-2 bg-blue-100 dark:bg-blue-800 rounded-lg">
                                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Total Ganadores</p>
                                        <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $winners->count() }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                                <div class="flex items-center">
                                    <div class="p-2 bg-green-100 dark:bg-green-800 rounded-lg">
                                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-green-600 dark:text-green-400">Premios Únicos</p>
                                        <p class="text-2xl font-bold text-green-900 dark:text-green-100">{{ $winners->pluck('prize_id')->unique()->count() }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                                <div class="flex items-center">
                                    <div class="p-2 bg-purple-100 dark:bg-purple-800 rounded-lg">
                                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-purple-600 dark:text-purple-400">Participantes Únicos</p>
                                        <p class="text-2xl font-bold text-purple-900 dark:text-purple-100">{{ $winners->pluck('participant_id')->unique()->count() }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-orange-50 dark:bg-orange-900/20 p-4 rounded-lg">
                                <div class="flex items-center">
                                    <div class="p-2 bg-orange-100 dark:bg-orange-800 rounded-lg">
                                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-orange-600 dark:text-orange-400">Último Sorteo</p>
                                        <p class="text-sm font-bold text-orange-900 dark:text-orange-100">
                                            {{ $winners->max('drawn_at') ? $winners->max('drawn_at')->format('d/m H:i') : 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Gráficos de estadísticas -->
                        <div id="charts-section" class="hidden mb-6">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- Gráfico de ganadores por día -->
                                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                                    <h4 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Ganadores por Día</h4>
                                    <canvas id="winnersByDayChart" width="400" height="200"></canvas>
                                </div>

                                <!-- Gráfico de premios otorgados -->
                                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                                    <h4 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Distribución de Premios</h4>
                                    <canvas id="prizesChart" width="400" height="200"></canvas>
                                </div>

                                <!-- Gráfico de participantes únicos -->
                                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                                    <h4 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Participantes Únicos por Día</h4>
                                    <canvas id="participantsChart" width="400" height="200"></canvas>
                                </div>

                                <!-- Gráfico de tendencia temporal -->
                                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                                    <h4 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Tendencia de Ganadores</h4>
                                    <canvas id="trendChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de ganadores -->
                @if($winners->count() > 0)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h3 class="text-lg font-semibold mb-4">Ganadores</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                #
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Premio
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Número Ganador
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Ganador
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Teléfono
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Email
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Fecha de Sorteo
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($winners as $index => $winner)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $index + 1 }}
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center">
                                                        @if($winner->prize->image)
                                                            <img src="{{ asset('storage/' . $winner->prize->image) }}"
                                                                 alt="{{ $winner->prize->name }}"
                                                                 class="w-12 h-12 object-cover rounded-lg mr-3">
                                                        @endif
                                                        <div>
                                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                                {{ $winner->prize->name }}
                                                            </div>
                                                            @if($winner->prize->description)
                                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                                    {{ Str::limit($winner->prize->description, 50) }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                        {{ $winner->number }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $winner->participant->name ?? 'Sin nombre' }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $winner->participant->phone ?? '-' }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $winner->participant->email ?? '-' }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $winner->drawn_at->format('d/m/Y H:i:s') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No hay ganadores registrados</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Esta rifa aún no tiene ganadores registrados en el sistema.
                            </p>
                        </div>
                    </div>
                @endif
            @else
                <!-- Estado inicial -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Selecciona una rifa</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Elige una rifa de la lista para ver sus ganadores.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Datos para los gráficos
        const winnersData = @json($winners);
        const chartData = {
            winnersByDay: @json($winners->groupBy(function($item) { return $item->drawn_at->format('Y-m-d'); })->map->count()),
            prizesDistribution: @json($winners->groupBy('prize.name')->map->count()),
            participantsByDay: @json($winners->groupBy(function($item) { return $item->drawn_at->format('Y-m-d'); })->map(function($group) { return $group->pluck('participant_id')->unique()->count(); })),
            trendData: @json($winners->sortBy('drawn_at')->values())
        };

        // Función para actualizar rangos de fecha
        function updateDateRange() {
            const range = document.getElementById('date_range').value;
            const dateFrom = document.getElementById('date_from');
            const dateTo = document.getElementById('date_to');
            const today = new Date();

            switch(range) {
                case 'today':
                    dateFrom.value = today.toISOString().split('T')[0];
                    dateTo.value = today.toISOString().split('T')[0];
                    break;
                case '7days':
                    const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
                    dateFrom.value = weekAgo.toISOString().split('T')[0];
                    dateTo.value = today.toISOString().split('T')[0];
                    break;
                case '15days':
                    const twoWeeksAgo = new Date(today.getTime() - 15 * 24 * 60 * 60 * 1000);
                    dateFrom.value = twoWeeksAgo.toISOString().split('T')[0];
                    dateTo.value = today.toISOString().split('T')[0];
                    break;
                case '30days':
                    const monthAgo = new Date(today.getTime() - 30 * 24 * 60 * 60 * 1000);
                    dateFrom.value = monthAgo.toISOString().split('T')[0];
                    dateTo.value = today.toISOString().split('T')[0];
                    break;
                case 'quarter':
                    const quarterStart = new Date(today.getFullYear(), Math.floor(today.getMonth() / 3) * 3, 1);
                    dateFrom.value = quarterStart.toISOString().split('T')[0];
                    dateTo.value = today.toISOString().split('T')[0];
                    break;
                case 'semester':
                    const semesterStart = new Date(today.getFullYear(), today.getMonth() < 6 ? 0 : 6, 1);
                    dateFrom.value = semesterStart.toISOString().split('T')[0];
                    dateTo.value = today.toISOString().split('T')[0];
                    break;
                case 'year':
                    const yearStart = new Date(today.getFullYear(), 0, 1);
                    dateFrom.value = yearStart.toISOString().split('T')[0];
                    dateTo.value = today.toISOString().split('T')[0];
                    break;
            }
        }

        // Función para mostrar/ocultar gráficos
        function toggleCharts() {
            const chartsSection = document.getElementById('charts-section');
            const button = event.target;

            if (chartsSection.classList.contains('hidden')) {
                chartsSection.classList.remove('hidden');
                button.textContent = 'Ocultar Gráficos';
                initializeCharts();
            } else {
                chartsSection.classList.add('hidden');
                button.textContent = 'Ver Gráficos';
            }
        }

        // Inicializar gráficos
        function initializeCharts() {
            // Gráfico de ganadores por día
            const winnersByDayCtx = document.getElementById('winnersByDayChart').getContext('2d');
            new Chart(winnersByDayCtx, {
                type: 'bar',
                data: {
                    labels: Object.keys(chartData.winnersByDay),
                    datasets: [{
                        label: 'Ganadores',
                        data: Object.values(chartData.winnersByDay),
                        backgroundColor: 'rgba(59, 130, 246, 0.5)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Gráfico de distribución de premios
            const prizesCtx = document.getElementById('prizesChart').getContext('2d');
            new Chart(prizesCtx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(chartData.prizesDistribution),
                    datasets: [{
                        data: Object.values(chartData.prizesDistribution),
                        backgroundColor: [
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(168, 85, 247, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(239, 68, 68, 0.8)',
                            'rgba(16, 185, 129, 0.8)'
                        ]
                    }]
                },
                options: {
                    responsive: true
                }
            });

            // Gráfico de participantes únicos
            const participantsCtx = document.getElementById('participantsChart').getContext('2d');
            new Chart(participantsCtx, {
                type: 'line',
                data: {
                    labels: Object.keys(chartData.participantsByDay),
                    datasets: [{
                        label: 'Participantes Únicos',
                        data: Object.values(chartData.participantsByDay),
                        borderColor: 'rgba(168, 85, 247, 1)',
                        backgroundColor: 'rgba(168, 85, 247, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Gráfico de tendencia
            const trendCtx = document.getElementById('trendChart').getContext('2d');
            const trendLabels = chartData.trendData.map(item => item.drawn_at.split('T')[0]);
            const trendValues = chartData.trendData.map((item, index) => index + 1);

            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: trendLabels,
                    datasets: [{
                        label: 'Acumulado de Ganadores',
                        data: trendValues,
                        borderColor: 'rgba(245, 158, 11, 1)',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Inicializar fechas por defecto si no hay filtros aplicados
        document.addEventListener('DOMContentLoaded', function() {
            if (!document.getElementById('date_from').value) {
                updateDateRange();
            }
        });
    </script>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $title }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex flex-wrap justify-between items-center gap-2 mb-4">
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Fecha: {{ $date }}</div>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.reports.proposal.edit') }}" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded">
                                Editar Propuesta
                            </a>
                            <a href="{{ route('admin.reports.proposal', ['download' => 1]) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded">
                                Descargar PDF
                            </a>
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold mb-3">Resumen de características</h3>
                    <ul class="list-disc list-inside space-y-1 mb-6">
                        @foreach($features as $f)
                            <li>{{ $f }}</li>
                        @endforeach
                    </ul>

                    <h3 class="text-lg font-semibold mb-3">Tarifario sugerido - Plataforma de Rifas/Sorteos</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-3 py-2 text-left">Tipo de Cliente</th>
                                    <th class="px-3 py-2 text-left">Tamaño del Sorteo</th>
                                    <th class="px-3 py-2 text-left">Venta Estimada (S/.)</th>
                                    <th class="px-3 py-2 text-left">Costo Fijo</th>
                                    <th class="px-3 py-2 text-left">Comisión</th>
                                    <th class="px-3 py-2 text-left">Servicios Opcionales</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($pricing as $row)
                                    <tr>
                                        <td class="px-3 py-2">{{ $row['segment'] }}</td>
                                        <td class="px-3 py-2">{{ $row['size'] }}</td>
                                        <td class="px-3 py-2">S/. {{ number_format($row['estimate'], 0) }}</td>
                                        <td class="px-3 py-2">S/. {{ number_format($row['fixed'], 0) }}</td>
                                        <td class="px-3 py-2">{{ $row['fee_pct'] }}% (S/. {{ number_format($row['estimate'] * $row['fee_pct'] / 100, 0) }})</td>
                                        <td class="px-3 py-2">{{ $row['optional'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 text-sm text-gray-600 dark:text-gray-400">
                        Nota: Los precios son referenciales y pueden ajustarse según alcance, plazos y requerimientos específicos (branding, campañas de difusión, landing personalizada, integración de pagos, etc.).
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

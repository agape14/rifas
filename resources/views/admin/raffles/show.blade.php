<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Detalles de la Rifa: {{ $raffle->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Información básica -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Información General</h3>
                            <div class="space-y-3">
                                <div>
                                    <span class="font-medium">Nombre:</span>
                                    <span class="ml-2">{{ $raffle->name }}</span>
                                </div>
                                <div>
                                    <span class="font-medium">Descripción:</span>
                                    <p class="mt-1 text-sm">{{ $raffle->description }}</p>
                                </div>
                                <div>
                                    <span class="font-medium">Fecha del Sorteo:</span>
                                    <span class="ml-2">{{ \Carbon\Carbon::parse($raffle->draw_date)->format('d/m/Y') }}</span>
                                </div>
                                <div>
                                    <span class="font-medium">Estado:</span>
                                    <span class="ml-2 px-2 py-1 text-xs rounded-full
                                        {{ $raffle->status === 'en_venta' ? 'bg-green-100 text-green-800' :
                                           ($raffle->status === 'finalizada' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst(str_replace('_', ' ', $raffle->status)) }}
                                    </span>
                                </div>
                                <div>
                                    <span class="font-medium">Total de Números:</span>
                                    <span class="ml-2">{{ $raffle->total_numbers }}</span>
                                </div>
                                <div>
                                    <span class="font-medium">Color del Tema:</span>
                                    <div class="flex items-center mt-1">
                                        <div class="w-6 h-6 rounded border" style="background-color: {{ $raffle->theme_color ?? '#000000' }}"></div>
                                        <span class="ml-2">{{ $raffle->theme_color ?? '#000000' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Banner -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Banner</h3>
                            @if($raffle->banner)
                                <img src="{{ asset('storage/' . $raffle->banner) }}" alt="Banner de la rifa" class="w-full rounded-lg">
                            @else
                                <div class="w-full h-48 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                    <span class="text-gray-500 dark:text-gray-400">Sin banner</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Estadísticas -->
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold mb-4">Estadísticas</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                    {{ $raffle->numbers->count() }}
                                </div>
                                <div class="text-sm text-blue-600 dark:text-blue-400">Números Creados</div>
                            </div>
                            <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                    {{ $raffle->numbers->where('status', 'pagado')->count() }}
                                </div>
                                <div class="text-sm text-green-600 dark:text-green-400">Números Vendidos</div>
                            </div>
                            <div class="bg-purple-50 dark:bg-purple-900 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                    {{ $raffle->prizes->count() }}
                                </div>
                                <div class="text-sm text-purple-600 dark:text-purple-400">Premios</div>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="mt-8 flex items-center gap-4">
                        <a href="{{ route('admin.raffles.edit', $raffle->id) }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Editar Rifa
                        </a>
                        <a href="{{ route('admin.raffles.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

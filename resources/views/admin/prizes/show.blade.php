<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Detalles del Premio: {{ $prize->name }}
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
                                    <span class="ml-2">{{ $prize->name }}</span>
                                </div>
                                <div>
                                    <span class="font-medium">Rifa:</span>
                                    <span class="ml-2">{{ $prize->raffle->name }}</span>
                                </div>
                                <div>
                                    <span class="font-medium">Descripción:</span>
                                    <p class="mt-1 text-sm">{{ $prize->description }}</p>
                                </div>
                                <div>
                                    <span class="font-medium">Orden:</span>
                                    <span class="ml-2">{{ $prize->order }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Imagen -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Imagen del Premio</h3>
                            @if($prize->image)
                                <img src="{{ asset('storage/' . $prize->image) }}" alt="Imagen del premio" class="w-full rounded-lg">
                            @else
                                <div class="w-full h-48 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                    <span class="text-gray-500 dark:text-gray-400">Sin imagen</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="mt-8 flex items-center gap-4">
                        <a href="{{ route('admin.prizes.edit', $prize->id) }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Editar Premio
                        </a>
                        <a href="{{ route('admin.prizes.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

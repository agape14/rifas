<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detalle del Participante') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">Participante: {{ $participant->name }}</h3>
                        <div class="flex space-x-3">
                            <a href="{{ route('admin.participants.edit', $participant->id) }}"
                               class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                Editar
                            </a>
                            <a href="{{ route('admin.participants.index') }}"
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Volver
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Información del participante -->
                        <div class="md:col-span-2 space-y-6">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Información Personal</h4>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $participant->name }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Teléfono</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            {{ $participant->phone ? ($participant->phone_formatted ?? $participant->phone) : 'No especificado' }}
                                        </p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            {{ $participant->email ?: 'No especificado' }}
                                        </p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de Registro</label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            {{ $participant->created_at->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Números asignados -->
                            @if($participant->numbers->count() > 0)
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                        Números Asignados ({{ $participant->numbers->count() }})
                                    </h4>

                                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                                        @foreach($participant->numbers as $number)
                                            <div class="bg-white dark:bg-gray-600 rounded-lg p-3 text-center">
                                                <div class="text-lg font-bold text-indigo-600 dark:text-indigo-400">
                                                    {{ $number->number }}
                                                </div>
                                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $number->raffle->name }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                                    {{ ucfirst($number->status) }}
                                                </div>
                                                <div class="text-xs text-gray-400 dark:text-gray-500">
                                                    S/. {{ number_format($number->price, 0) }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Números Asignados</h4>
                                    <p class="text-gray-500 dark:text-gray-400">Este participante no tiene números asignados.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Foto del participante -->
                        <div class="space-y-4">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Foto</h4>

                                @if($participant->photo)
                                    <div class="relative">
                                        <img src="{{ asset('storage/' . $participant->photo) }}"
                                             alt="Foto de {{ $participant->name }}"
                                             class="w-full h-64 object-cover rounded-lg border-2 border-gray-300">
                                    </div>
                                @else
                                    <div class="w-full h-64 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                                        <span class="text-gray-500 dark:text-gray-400">Sin foto</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Estadísticas rápidas -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Estadísticas</h4>

                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Total de números:</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $participant->numbers->count() }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Números pagados:</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $participant->numbers->where('status', 'pagado')->count() }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Números reservados:</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $participant->numbers->where('status', 'reservado')->count() }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Total invertido:</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            S/. {{ number_format($participant->numbers->sum('price'), 0) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

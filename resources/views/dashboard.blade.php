<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">¡Bienvenido al Sistema de Rifas!</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                        <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
                            <h4 class="font-semibold text-blue-800 dark:text-blue-200">Rifas Públicas</h4>
                            <p class="text-blue-600 dark:text-blue-300 text-sm">Ver todas las rifas disponibles</p>
                            <a href="{{ route('public.index') }}" class="inline-block mt-2 text-blue-600 dark:text-blue-300 hover:underline">
                                Ver rifas →
                            </a>
                        </div>

                        @if(Auth::user() && Auth::user()->is_admin)
                            <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg">
                                <h4 class="font-semibold text-green-800 dark:text-green-200">Administración</h4>
                                <p class="text-green-600 dark:text-green-300 text-sm">Gestionar rifas, premios y participantes</p>
                                <a href="{{ route('admin.raffles.index') }}" class="inline-block mt-2 text-green-600 dark:text-green-300 hover:underline">
                                    Ir a administración →
                                </a>
                            </div>
                        @endif

                        <div class="bg-purple-50 dark:bg-purple-900 p-4 rounded-lg">
                            <h4 class="font-semibold text-purple-800 dark:text-purple-200">Mi Perfil</h4>
                            <p class="text-purple-600 dark:text-purple-300 text-sm">Editar información personal</p>
                            <a href="{{ route('profile.edit') }}" class="inline-block mt-2 text-purple-600 dark:text-purple-300 hover:underline">
                                Editar perfil →
                            </a>
                        </div>
                    </div>

                    @if(Auth::user() && Auth::user()->is_admin)
                        <div class="border-t pt-6">
                            <h4 class="font-semibold mb-3">Acciones Rápidas</h4>
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('admin.raffles.create') }}"
                                   class="inline-flex items-center px-3 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                    Nueva Rifa
                                </a>
                                <a href="{{ route('admin.prizes.index') }}"
                                   class="inline-flex items-center px-3 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                    Gestionar Premios
                                </a>
                                <a href="{{ route('admin.participants.index') }}"
                                   class="inline-flex items-center px-3 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700">
                                    Ver Participantes
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

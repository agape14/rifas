<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Crear Nueva Rifa
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('admin.raffles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 sm:space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="name" :value="__('Nombre de la Rifa')" class="text-sm sm:text-base" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full px-3 py-2 sm:py-3 text-sm sm:text-base" :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Descripción')" class="text-sm sm:text-base" />
                            <div class="mt-1">
                                <textarea id="description" name="description" rows="6" class="mt-1 block w-full px-3 py-2 sm:py-3 text-sm sm:text-base border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>{{ old('description') }}</textarea>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Puedes usar HTML para formatear el texto (negritas, cursivas, enlaces, etc.)</p>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="draw_date" :value="__('Fecha del Sorteo')" class="text-sm sm:text-base" />
                                <x-text-input id="draw_date" name="draw_date" type="date" class="mt-1 block w-full px-3 py-2 sm:py-3 text-sm sm:text-base" :value="old('draw_date')" required />
                                <x-input-error :messages="$errors->get('draw_date')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="total_numbers" :value="__('Total de Números')" class="text-sm sm:text-base" />
                                <x-text-input id="total_numbers" name="total_numbers" type="number" class="mt-1 block w-full px-3 py-2 sm:py-3 text-sm sm:text-base" :value="old('total_numbers', 100)" required />
                                <x-input-error :messages="$errors->get('total_numbers')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="status" :value="__('Estado')" class="text-sm sm:text-base" />
                                <select id="status" name="status" class="mt-1 block w-full px-3 py-2 sm:py-3 text-sm sm:text-base border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="programada" {{ old('status') == 'programada' ? 'selected' : '' }}>Programada</option>
                                    <option value="en_venta" {{ old('status') == 'en_venta' ? 'selected' : '' }}>En Venta</option>
                                    <option value="finalizada" {{ old('status') == 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="theme_color" :value="__('Color del Tema')" class="text-sm sm:text-base" />
                                <x-text-input id="theme_color" name="theme_color" type="color" class="mt-1 block w-full h-10 sm:h-12" :value="old('theme_color', '#000000')" />
                                <x-input-error :messages="$errors->get('theme_color')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="banner" :value="__('Banner')" class="text-sm sm:text-base" />
                            <input type="file" id="banner" name="banner" accept="image/*" class="mt-1 block w-full px-3 py-2 sm:py-3 text-sm sm:text-base text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                            <x-input-error :messages="$errors->get('banner')" class="mt-2" />
                        </div>

                        <div class="flex flex-col sm:flex-row items-center gap-4 pt-4">
                            <x-primary-button class="w-full sm:w-auto px-4 py-2 sm:py-3 text-sm sm:text-base">{{ __('Crear Rifa') }}</x-primary-button>
                            <a href="{{ route('admin.raffles.index') }}" class="w-full sm:w-auto inline-flex items-center px-4 py-2 sm:py-3 bg-gray-300 border border-transparent rounded-md font-semibold text-sm sm:text-base text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 text-center">
                                {{ __('Cancelar') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

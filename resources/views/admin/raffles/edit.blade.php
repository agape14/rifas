<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Editar Rifa: {{ $raffle->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('admin.raffles.update', $raffle->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="name" :value="__('Nombre de la Rifa')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $raffle->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Descripción')" />
                            <div class="mt-1">
                                <textarea id="description" name="description" rows="6" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>{{ old('description', $raffle->description) }}</textarea>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Puedes usar HTML para formatear el texto (negritas, cursivas, enlaces, etc.)</p>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="draw_date" :value="__('Fecha del Sorteo')" />
                            <x-text-input id="draw_date" name="draw_date" type="date" class="mt-1 block w-full" :value="old('draw_date', $raffle->draw_date)" required />
                            <x-input-error :messages="$errors->get('draw_date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="total_numbers" :value="__('Total de Números')" />
                            <x-text-input id="total_numbers" name="total_numbers" type="number" class="mt-1 block w-full" :value="old('total_numbers', $raffle->total_numbers)" required />
                            <x-input-error :messages="$errors->get('total_numbers')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="status" :value="__('Estado')" />
                            <select id="status" name="status" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="programada" {{ old('status', $raffle->status) == 'programada' ? 'selected' : '' }}>Programada</option>
                                <option value="en_venta" {{ old('status', $raffle->status) == 'en_venta' ? 'selected' : '' }}>En Venta</option>
                                <option value="finalizada" {{ old('status', $raffle->status) == 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="theme_color" :value="__('Color del Tema')" />
                            <x-text-input id="theme_color" name="theme_color" type="color" class="mt-1 block w-full h-10" :value="old('theme_color', $raffle->theme_color ?? '#000000')" />
                            <x-input-error :messages="$errors->get('theme_color')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="banner" :value="__('Banner')" />
                            @if($raffle->banner)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $raffle->banner) }}" alt="Banner actual" class="w-32 h-20 object-cover rounded">
                                </div>
                            @endif
                            <input type="file" id="banner" name="banner" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Deja vacío para mantener el banner actual</p>
                            <x-input-error :messages="$errors->get('banner')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Actualizar Rifa') }}</x-primary-button>
                            <a href="{{ route('admin.raffles.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Cancelar') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

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

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="total_numbers" :value="__('Total de Números')" />
                                <x-text-input id="total_numbers" name="total_numbers" type="number" class="mt-1 block w-full" :value="old('total_numbers', $raffle->total_numbers)" required />
                                <x-input-error :messages="$errors->get('total_numbers')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="number_price" :value="__('Precio por número (S/.)')" />
                                @php
                                    $defaultPrice = old('number_price');
                                    if ($defaultPrice === null) {
                                        $defaultPrice = optional($raffle->numbers()->first())->price ?? 10;
                                    }
                                @endphp
                                <x-text-input id="number_price" name="number_price" type="number" step="0.01" class="mt-1 block w-full" :value="$defaultPrice" />
                                <x-input-error :messages="$errors->get('number_price')" class="mt-2" />
                            </div>
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

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="organizer_name" :value="__('Organizador - Nombre')" />
                                <x-text-input id="organizer_name" name="organizer_name" type="text" class="mt-1 block w-full" :value="old('organizer_name', $raffle->organizer_name ?? config('raffle.organizer_name'))" />
                            </div>
                            <div>
                                <x-input-label for="organizer_id" :value="__('Organizador - RUC/ID')" />
                                <x-text-input id="organizer_id" name="organizer_id" type="text" class="mt-1 block w-full" :value="old('organizer_id', $raffle->organizer_id ?? config('raffle.organizer_id'))" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="organizer_address" :value="__('Organizador - Dirección')" />
                                <x-text-input id="organizer_address" name="organizer_address" type="text" class="mt-1 block w-full" :value="old('organizer_address', $raffle->organizer_address ?? config('raffle.organizer_address'))" />
                            </div>
                            <div>
                                <x-input-label for="organizer_contact" :value="__('Organizador - Contacto')" />
                                <x-text-input id="organizer_contact" name="organizer_contact" type="text" class="mt-1 block w-full" :value="old('organizer_contact', $raffle->organizer_contact ?? config('raffle.organizer_contact'))" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="organizer_contact_email" :value="__('Organizador - Email')" />
                                <x-text-input id="organizer_contact_email" name="organizer_contact_email" type="email" class="mt-1 block w-full" :value="old('organizer_contact_email', $raffle->organizer_contact_email ?? config('raffle.organizer_contact_email'))" />
                            </div>
                            <div>
                                <x-input-label for="platform_name" :value="__('Plataforma - Nombre')" />
                                <x-text-input id="platform_name" name="platform_name" type="text" class="mt-1 block w-full" :value="old('platform_name', $raffle->platform_name ?? config('raffle.platform_name'))" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="broadcast_platform" :value="__('Transmisión - Plataforma')" />
                                <x-text-input id="broadcast_platform" name="broadcast_platform" type="text" class="mt-1 block w-full" :value="old('broadcast_platform', $raffle->broadcast_platform ?? config('raffle.broadcast_platform'))" />
                            </div>
                            <div>
                                <x-input-label for="privacy_url" :value="__('URL de Privacidad')" />
                                <x-text-input id="privacy_url" name="privacy_url" type="url" class="mt-1 block w-full" :value="old('privacy_url', $raffle->privacy_url ?? config('raffle.privacy_url'))" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="claim_days" :value="__('Días para Reclamo')" />
                                <x-text-input id="claim_days" name="claim_days" type="number" class="mt-1 block w-full" :value="old('claim_days', $raffle->claim_days ?? config('raffle.claim_days'))" />
                            </div>
                            <div>
                                <x-input-label for="jurisdiction_city" :value="__('Ciudad de Jurisdicción')" />
                                <x-text-input id="jurisdiction_city" name="jurisdiction_city" type="text" class="mt-1 block w-full" :value="old('jurisdiction_city', $raffle->jurisdiction_city ?? config('raffle.jurisdiction_city'))" />
                            </div>
                        </div>
                        <div>
                            <x-input-label for="terms_html" :value="__('Términos y Condiciones (HTML)')" />
                            <div class="mt-1">
                                <textarea id="terms_html" name="terms_html" rows="6" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('terms_html', $raffle->terms_html) }}</textarea>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Puedes usar HTML. Este contenido se mostrará en un modal en la página pública.</p>
                            <x-input-error :messages="$errors->get('terms_html')" class="mt-2" />
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

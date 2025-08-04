<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Editar Premio: {{ $prize->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('admin.prizes.update', $prize->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="raffle_id" :value="__('Rifa')" />
                            <select id="raffle_id" name="raffle_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                <option value="">Seleccionar rifa</option>
                                @foreach($raffles as $raffle)
                                    <option value="{{ $raffle->id }}" {{ old('raffle_id', $prize->raffle_id) == $raffle->id ? 'selected' : '' }}>
                                        {{ $raffle->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('raffle_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="name" :value="__('Nombre del Premio')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $prize->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Descripción')" />
                            <textarea id="description" name="description" rows="4" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('description', $prize->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="order" :value="__('Orden')" />
                            <x-text-input id="order" name="order" type="number" class="mt-1 block w-full" :value="old('order', $prize->order)" required />
                            <x-input-error :messages="$errors->get('order')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="image" :value="__('Imagen del Premio')" />
                            @if($prize->image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $prize->image) }}" alt="Imagen actual" class="w-32 h-20 object-cover rounded">
                                </div>
                            @endif
                            <input type="file" id="image" name="image" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Deja vacío para mantener la imagen actual</p>
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Actualizar Premio') }}</x-primary-button>
                            <a href="{{ route('admin.prizes.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Cancelar') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

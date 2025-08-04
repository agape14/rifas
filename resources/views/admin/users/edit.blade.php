<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Usuario') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4 sm:space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="name" :value="__('Nombre')" class="text-sm sm:text-base" />
                            <x-text-input id="name" class="block mt-1 w-full px-3 py-2 sm:py-3 text-sm sm:text-base" type="text" name="name" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Email')" class="text-sm sm:text-base" />
                            <x-text-input id="email" class="block mt-1 w-full px-3 py-2 sm:py-3 text-sm sm:text-base" type="email" name="email" :value="old('email', $user->email)" required autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password" :value="__('Nueva Contraseña (opcional)')" class="text-sm sm:text-base" />
                            <x-text-input id="password" class="block mt-1 w-full px-3 py-2 sm:py-3 text-sm sm:text-base"
                                            type="password"
                                            name="password"
                                            autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password_confirmation" :value="__('Confirmar Nueva Contraseña')" class="text-sm sm:text-base" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full px-3 py-2 sm:py-3 text-sm sm:text-base"
                                            type="password"
                                            name="password_confirmation" autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <div class="flex items-center">
                            <input id="is_admin" type="checkbox" name="is_admin" value="1" {{ $user->is_admin ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <label for="is_admin" class="ml-2 text-sm sm:text-base text-gray-600 dark:text-gray-400">
                                {{ __('Es Administrador') }}
                            </label>
                        </div>

                        <div class="flex flex-col sm:flex-row items-center justify-end mt-4 space-y-2 sm:space-y-0 sm:space-x-3">
                            <a href="{{ route('admin.users.index') }}" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 sm:py-3 px-4 rounded text-sm sm:text-base text-center">
                                Cancelar
                            </a>
                            <x-primary-button class="w-full sm:w-auto px-4 py-2 sm:py-3 text-sm sm:text-base">
                                {{ __('Actualizar Usuario') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

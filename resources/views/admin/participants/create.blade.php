<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Crear Participante') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">Nuevo Participante</h3>
                        <a href="{{ route('admin.participants.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Volver
                        </a>
                    </div>

                    <form action="{{ route('admin.participants.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 sm:space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                            <!-- Información básica -->
                            <div class="space-y-4 sm:space-y-6">
                                <div>
                                    <label for="name" class="block text-sm sm:text-base font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre *</label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                                           class="mt-1 block w-full px-3 py-2 sm:py-3 text-sm sm:text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm sm:text-base font-medium text-gray-700 dark:text-gray-300 mb-1">Teléfono</label>
                                    <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
                                           class="mt-1 block w-full px-3 py-2 sm:py-3 text-sm sm:text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    @error('phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm sm:text-base font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                                           class="mt-1 block w-full px-3 py-2 sm:py-3 text-sm sm:text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Foto -->
                            <div class="space-y-4 sm:space-y-6">
                                <div>
                                    <label for="photo" class="block text-sm sm:text-base font-medium text-gray-700 dark:text-gray-300 mb-1">Foto</label>
                                    <input type="file" name="photo" id="photo" accept="image/*"
                                           class="mt-1 block w-full px-3 py-2 sm:py-3 text-sm sm:text-base text-gray-500 dark:text-gray-400
                                                  file:mr-4 file:py-2 file:px-4
                                                  file:rounded-full file:border-0
                                                  file:text-sm file:font-semibold
                                                  file:bg-indigo-50 file:text-indigo-700
                                                  hover:file:bg-indigo-100
                                                  dark:file:bg-gray-700 dark:file:text-gray-300">
                                    @error('photo')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="text-sm sm:text-base text-gray-500 dark:text-gray-400">
                                    <p>Formatos permitidos: JPG, PNG, GIF</p>
                                    <p>Tamaño máximo: 2MB</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 pt-6">
                            <a href="{{ route('admin.participants.index') }}"
                               class="w-full sm:w-auto bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 sm:py-3 px-4 rounded text-sm sm:text-base text-center">
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 sm:py-3 px-4 rounded text-sm sm:text-base">
                                Crear Participante
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

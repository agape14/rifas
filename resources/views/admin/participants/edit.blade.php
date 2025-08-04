<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Participante') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">Editar Participante: {{ $participant->name }}</h3>
                        <a href="{{ route('admin.participants.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Volver
                        </a>
                    </div>

                    <form action="{{ route('admin.participants.update', $participant->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Información básica -->
                            <div class="space-y-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre *</label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $participant->name) }}"
                                           class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Teléfono</label>
                                    <input type="text" name="phone" id="phone" value="{{ old('phone', $participant->phone) }}"
                                           class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    @error('phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                    <input type="email" name="email" id="email" value="{{ old('email', $participant->email) }}"
                                           class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Foto actual y nueva foto -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Foto Actual</label>
                                    @if($participant->photo)
                                        <div class="relative">
                                            <img src="{{ asset('storage/' . $participant->photo) }}"
                                                 alt="Foto de {{ $participant->name }}"
                                                 class="w-32 h-32 object-cover rounded-lg border-2 border-gray-300">
                                        </div>
                                    @else
                                        <div class="w-32 h-32 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                            <span class="text-gray-500 dark:text-gray-400">Sin foto</span>
                                        </div>
                                    @endif
                                </div>

                                <div>
                                    <label for="photo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nueva Foto</label>
                                    <input type="file" name="photo" id="photo" accept="image/*"
                                           class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400
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
                            </div>
                        </div>

                        <!-- Números asignados -->
                        @if($participant->numbers->count() > 0)
                            <div class="mt-8">
                                <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Números Asignados</h4>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @foreach($participant->numbers as $number)
                                            <div class="bg-white dark:bg-gray-600 rounded-lg p-4 border border-gray-200 dark:border-gray-500">
                                                <div class="flex justify-between items-start">
                                                    <div class="flex-grow">
                                                        <div class="text-lg font-bold text-indigo-600 dark:text-indigo-400">
                                                            Número {{ $number->number }}
                                                        </div>
                                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                                            {{ $number->raffle->name }}
                                                        </div>
                                                        <div class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                                            Estado: {{ ucfirst($number->status) }}
                                                        </div>
                                                        @if($number->price)
                                                            <div class="text-xs text-green-600 dark:text-green-400 mt-1">
                                                                Precio: ${{ number_format($number->price, 2) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="ml-3">
                                                        <button type="button" 
                                                                onclick="releaseNumber({{ $number->id }}, '{{ $number->number }}', '{{ $number->raffle->name }}')"
                                                                class="inline-flex items-center px-2 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-md transition-colors duration-200">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                            </svg>
                                                            Liberar
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-end space-x-3 pt-6">
                            <a href="{{ route('admin.participants.index') }}"
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                Actualizar Participante
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 mb-4">
                    <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 text-center">Confirmar Liberación</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 text-center mb-6" id="confirmMessage"></p>
                <div class="flex justify-center space-x-3">
                    <button type="button" onclick="closeConfirmModal()" 
                            class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500">
                        Cancelar
                    </button>
                    <button type="button" onclick="confirmRelease()" 
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md">
                        Liberar Número
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentNumberId = null;
        let currentNumberText = '';
        let currentRaffleName = '';

        function releaseNumber(numberId, numberText, raffleName) {
            currentNumberId = numberId;
            currentNumberText = numberText;
            currentRaffleName = raffleName;
            
            document.getElementById('confirmMessage').textContent = 
                `¿Está seguro que desea liberar el número ${numberText} de la rifa "${raffleName}"?`;
            document.getElementById('confirmModal').classList.remove('hidden');
        }

        function closeConfirmModal() {
            document.getElementById('confirmModal').classList.add('hidden');
            currentNumberId = null;
            currentNumberText = '';
            currentRaffleName = '';
        }

        function confirmRelease() {
            if (!currentNumberId) return;

            fetch("{{ route('admin.participants.releaseNumber', $participant->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    number_id: currentNumberId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.success, 'success');
                    // Reload the page to refresh the numbers list
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showAlert(data.error || 'Error al liberar el número', 'error');
                }
                closeConfirmModal();
            })
            .catch(error => {
                showAlert('Error al procesar la solicitud', 'error');
                closeConfirmModal();
            });
        }

        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ${
                type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
            }`;
            alertDiv.textContent = message;

            document.body.appendChild(alertDiv);

            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }
    </script>
</x-app-layout>

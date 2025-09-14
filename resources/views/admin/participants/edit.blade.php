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
                                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                                                                @foreach($participant->numbers as $number)
                            <div class="bg-white dark:bg-gray-600 rounded-lg p-3 text-center relative">
                                <div class="text-lg font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ $number->number }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $number->raffle->name }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                    {{ ucfirst($number->status) }}
                                </div>
                                <button type="button"
                                        onclick="releaseNumber({{ $number->id }})"
                                        class="absolute top-1 right-1 bg-red-500 hover:bg-red-700 text-white text-xs px-2 py-1 rounded-full"
                                        title="Liberar número">
                                    ×
                                </button>
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

    @push('scripts')
    <script>
    function releaseNumber(numberId) {
        Swal.fire({
            title: '¿Liberar Número?',
            text: '¿Estás seguro de que quieres liberar este número? Esta acción hará que el número esté disponible nuevamente.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, Liberar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
            fetch(`{{ route('admin.participants.releaseNumber', $participant->id) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    number_id: numberId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar mensaje de éxito
                    showAlert(data.success, 'success');
                    // Recargar la página para actualizar la lista
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showAlert(data.error || 'Error al liberar el número', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error al procesar la solicitud', 'error');
            });
            }
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
    @endpush
</x-app-layout>

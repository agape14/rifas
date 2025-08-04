<x-public-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $raffle->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Banner y información principal -->
            <div class="text-center mb-8">
                @if($raffle->banner)
                    <img src="{{ asset('storage/' . $raffle->banner) }}" class="mx-auto rounded-lg mb-4 max-h-64 object-cover" alt="{{ $raffle->name }}">
                @endif
                <h1 class="text-3xl font-bold mb-2" style="color: {{ $raffle->theme_color ?? '#000' }}">{{ $raffle->name }}</h1>
                <p class="text-gray-600 dark:text-gray-400">
                    Fecha del sorteo: {{ \Carbon\Carbon::parse($raffle->draw_date)->format('d/m/Y') }}
                </p>
            </div>

            <!-- Premios -->
            @if($raffle->prizes->count() > 0)
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Premios</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($raffle->prizes as $prize)
                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                                @if($prize->image)
                                    <img src="{{ asset('storage/' . $prize->image) }}" class="w-full h-48 object-cover" alt="{{ $prize->name }}">
                                @endif
                                <div class="p-4">
                                    <h5 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ $prize->name }}</h5>
                                    <p class="text-gray-600 dark:text-gray-400">{{ $prize->description }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Números disponibles -->
            <div class="mb-8">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-6 text-center">Números disponibles</h3>

                <!-- Estadísticas de números -->
                <div class="flex flex-col sm:flex-row justify-center items-center space-y-2 sm:space-y-0 sm:space-x-8 mb-6">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-blue-500 rounded mr-2"></div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Disponibles: <span class="font-bold" id="disponibles-count">{{ $raffle->numbers->where('status', 'disponible')->count() }}</span>
                        </span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Vendidos: <span class="font-bold" id="vendidos-count">{{ $raffle->numbers->where('status', 'pagado')->count() }}</span>
                        </span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-yellow-500 rounded mr-2"></div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Reservados: <span class="font-bold" id="reservados-count">{{ $raffle->numbers->where('status', 'reservado')->count() }}</span>
                        </span>
                    </div>
                </div>

                <!-- Cuadrícula de números -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <div class="grid grid-cols-10 gap-3 max-w-4xl mx-auto">
                        @php
                            $numbers = $raffle->numbers->sortBy('number');
                        @endphp
                        @foreach($numbers as $number)
                            <div class="relative">
                                <button
                                    class="number-btn w-16 h-16 text-lg font-bold rounded-lg transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $number->status == 'disponible' ? 'bg-gradient-to-br from-green-400 to-green-600 text-white hover:from-green-500 hover:to-green-700 shadow-lg hover:shadow-xl' : ($number->status == 'pagado' ? 'bg-gradient-to-br from-red-400 to-red-600 text-white cursor-not-allowed' : 'bg-gradient-to-br from-yellow-400 to-yellow-600 text-white cursor-not-allowed') }}"
                                    data-id="{{ $number->id }}"
                                    data-status="{{ $number->status }}"
                                    {{ $number->status != 'disponible' ? 'disabled' : '' }}
                                    title="{{ $number->status == 'disponible' ? 'Click para seleccionar' : ($number->status == 'pagado' ? 'Número vendido - ' . ($number->participant ? $number->participant->name : '') : 'Número reservado') }}">
                                    {{ $number->number }}
                                </button>
                                @if($number->status != 'disponible' && $number->participant && auth()->check() && auth()->user()->is_admin)
                                    <button
                                        class="release-btn absolute -top-1 -right-1 bg-white text-red-500 text-xs w-5 h-5 rounded-full border border-red-500 hover:bg-red-500 hover:text-white flex items-center justify-center"
                                        data-number-id="{{ $number->id }}"
                                        title="Liberar número - {{ $number->participant->name }}"
                                        onclick="releaseNumberPublic({{ $number->id }})">
                                        ×
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Leyenda -->
                    <div class="mt-6 flex justify-center space-x-6 text-sm">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-gradient-to-br from-green-400 to-green-600 rounded mr-2"></div>
                            <span class="text-gray-600 dark:text-gray-400">Disponible</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-gradient-to-br from-red-400 to-red-600 rounded mr-2"></div>
                            <span class="text-gray-600 dark:text-gray-400">Vendido</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded mr-2"></div>
                            <span class="text-gray-600 dark:text-gray-400">Reservado</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para registrar participante -->
    <div id="numberModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Registrar Participante</h3>
                <form id="numberForm" class="space-y-4">
                    <input type="hidden" id="number_id" name="number_id">

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nombre *</label>
                        <input type="text" name="name" id="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Teléfono</label>
                        <input type="text" name="phone" id="phone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancelar
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        let selectedNumberId = null;

        // Event listeners para botones de números
        document.querySelectorAll('.number-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                if (!this.disabled) {
                    selectedNumberId = this.getAttribute('data-id');
                    document.getElementById('number_id').value = selectedNumberId;
                    openModal();
                }
            });
        });

        // Manejo del formulario
        document.getElementById('numberForm').addEventListener('submit', function(e) {
            e.preventDefault();

            let formData = new FormData(this);

            fetch("{{ route('public.raffle.selectNumber', $raffle->id) }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    // Captura validaciones Laravel (422) y otros errores
                    return response.json().then(err => {
                        console.error("Respuesta de error del servidor:", err);
                        return Promise.reject(err);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    closeModal();

                    let btn = document.querySelector(`.number-btn[data-id="${selectedNumberId}"]`);
                    btn.classList.remove('bg-gradient-to-br', 'from-green-400', 'to-green-600', 'hover:from-green-500', 'hover:to-green-700', 'shadow-lg', 'hover:shadow-xl');
                    btn.classList.add('bg-gradient-to-br', 'from-red-400', 'to-red-600', 'cursor-not-allowed');
                    btn.setAttribute('data-status', 'pagado');
                    btn.disabled = true;
                    btn.title = `Número vendido - ${data.participant_name || 'Participante'}`;

                    @if(auth()->check() && auth()->user()->is_admin)
                    let numberContainer = btn.parentElement;
                    let releaseBtn = document.createElement('button');
                    releaseBtn.className = 'release-btn absolute -top-1 -right-1 bg-white text-red-500 text-xs w-5 h-5 rounded-full border border-red-500 hover:bg-red-500 hover:text-white flex items-center justify-center';
                    releaseBtn.setAttribute('data-number-id', selectedNumberId);
                    releaseBtn.innerHTML = '×';
                    releaseBtn.title = `Liberar número - ${data.participant_name || 'Participante'}`;
                    releaseBtn.onclick = function() { releaseNumberPublic(selectedNumberId); };
                    numberContainer.appendChild(releaseBtn);
                    @endif

                    updateStatistics();

                    // Mostrar mensaje de éxito con SweetAlert2
                    let message = data.success;
                    if (data.participant_exists) {
                        message += ` - Participante existente: ${data.participant_name}`;
                    }

                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: message,
                        timer: 3000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                } else if (data.error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.error,
                        confirmButtonColor: '#d33'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);

                let message = 'Error al procesar la solicitud';
                if (error.errors) {
                    // Laravel ValidationException (422)
                    message = Object.values(error.errors).flat().join("\n");
                } else if (error.error) {
                    message = error.error;
                } else if (typeof error === 'string') {
                    message = error;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message,
                    confirmButtonColor: '#d33'
                });
            });
        });

        // Validación en tiempo real para email y teléfono
        const emailInput = document.getElementById('email');
        const phoneInput = document.getElementById('phone');
        const nameInput = document.getElementById('name');

        // Función para verificar participante existente
        function checkExistingParticipant() {
            const email = emailInput.value.trim();
            const phone = phoneInput.value.trim();

            if (email || phone) {
                fetch("{{ route('public.raffle.checkParticipant', $raffle->id) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ email, phone })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.exists) {
                        showParticipantInfo(data.participant);
                    } else {
                        hideParticipantInfo();
                    }
                })
                .catch(error => {
                    console.error('Error al verificar participante:', error);
                });
            } else {
                hideParticipantInfo();
            }
        }

        // Event listeners para verificación en tiempo real
        emailInput.addEventListener('blur', checkExistingParticipant);
        phoneInput.addEventListener('blur', checkExistingParticipant);

        function showParticipantInfo(participant) {
            let infoDiv = document.getElementById('participant-info');
            if (!infoDiv) {
                infoDiv = document.createElement('div');
                infoDiv.id = 'participant-info';
                infoDiv.className = 'mt-3 p-3 bg-blue-50 border border-blue-200 rounded-md';
                document.getElementById('numberForm').insertBefore(infoDiv, document.querySelector('.flex.justify-end'));
            }

            infoDiv.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-blue-800">Participante existente encontrado</p>
                        <p class="text-xs text-blue-600">Nombre: ${participant.name}</p>
                        ${participant.phone ? `<p class="text-xs text-blue-600">Teléfono: ${participant.phone}</p>` : ''}
                        ${participant.email ? `<p class="text-xs text-blue-600">Email: ${participant.email}</p>` : ''}
                        <p class="text-xs text-blue-600 mt-1">Números actuales: ${participant.numbers_count}</p>
                    </div>
                </div>
            `;
        }

        function hideParticipantInfo() {
            const infoDiv = document.getElementById('participant-info');
            if (infoDiv) {
                infoDiv.remove();
            }
        }
    });

    function openModal() {
        document.getElementById('numberModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('numberModal').classList.add('hidden');
        document.getElementById('numberForm').reset();
        hideParticipantInfo();
    }

    function showAlert(message, type) {
        // Usar SweetAlert2 si está disponible, sino fallback al sistema anterior
        if (typeof Swal !== 'undefined') {
            if (type === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: message,
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message,
                    confirmButtonColor: '#d33'
                });
            }
            return;
        }

        // Fallback al sistema anterior si SweetAlert2 no está disponible
        const alertDiv = document.createElement('div');
        alertDiv.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 transform transition-all duration-300 ${
            type === 'success' ? 'bg-green-500 text-white border-l-4 border-green-700' : 'bg-red-500 text-white border-l-4 border-red-700'
        }`;

        alertDiv.innerHTML = `
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    ${type === 'success'
                        ? '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>'
                        : '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>'
                    }
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">${message}</p>
                </div>
                <div class="ml-auto pl-3">
                    <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-white hover:text-gray-200">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(alertDiv);

        // Animación de entrada
        setTimeout(() => {
            alertDiv.style.transform = 'translateX(0)';
        }, 10);

        // Auto-remover después de 5 segundos
        setTimeout(() => {
            alertDiv.style.transform = 'translateX(400px)';
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 300);
        }, 5000);
    }

    function updateStatistics() {
        // Obtener estadísticas actualizadas del servidor
        fetch("{{ route('public.raffle.statistics', $raffle->id) }}")
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Actualizar contadores en el UI
                document.getElementById('disponibles-count').textContent = data.disponibles;
                document.getElementById('vendidos-count').textContent = data.vendidos;
                document.getElementById('reservados-count').textContent = data.reservados;

                console.log('Estadísticas actualizadas:', data);
            })
            .catch(error => {
                console.error('Error al obtener estadísticas:', error);
                // Fallback: contar localmente si falla la petición al servidor
                const disponibles = document.querySelectorAll('.number-btn[data-status="disponible"]').length;
                const vendidos = document.querySelectorAll('.number-btn[data-status="pagado"]').length;
                const reservados = document.querySelectorAll('.number-btn[data-status="reservado"]').length;

                document.getElementById('disponibles-count').textContent = disponibles;
                document.getElementById('vendidos-count').textContent = vendidos;
                document.getElementById('reservados-count').textContent = reservados;

                console.log('Estadísticas calculadas localmente - Disponibles:', disponibles, 'Vendidos:', vendidos, 'Reservados:', reservados);
            });
    }

    function releaseNumberPublic(numberId) {
        @if(!auth()->check())
        Swal.fire({
            icon: 'error',
            title: 'Acceso denegado',
            text: 'Debes iniciar sesión para liberar números',
            confirmButtonColor: '#d33'
        });
        return;
        @endif

        @if(!auth()->check())
            Swal.fire({
                icon: 'error',
                title: 'No autenticado',
                text: 'Debes iniciar sesión para liberar números',
                confirmButtonColor: '#d33'
            });
            return;
        @elseif(!auth()->user()->is_admin)
            Swal.fire({
                icon: 'error',
                title: 'Permisos insuficientes',
                text: 'No tienes permisos de administrador para liberar números',
                confirmButtonColor: '#d33'
            });
            return;
        @endif


        Swal.fire({
            title: '¿Estás seguro?',
            text: '¿Quieres liberar este número? Esta acción hará que el número esté disponible nuevamente.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, liberar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch("{{ route('public.raffle.releaseNumber', $raffle->id) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        number_id: numberId
                    })
                })
                .then(response => {
                    if (response.status === 401) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Acceso denegado',
                            text: 'Debes iniciar sesión para liberar números',
                            confirmButtonColor: '#d33'
                        });
                        return Promise.reject('Unauthorized');
                    }
                    if (response.status === 403) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Permisos insuficientes',
                            text: 'No tienes permisos de administrador para liberar números',
                            confirmButtonColor: '#d33'
                        });
                        return Promise.reject('Forbidden');
                    }
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(data => {
                    if (data && data.success) {
                        // Actualizar el botón del número
                        let numberContainer = document.querySelector(`.release-btn[data-number-id="${numberId}"]`).parentElement;
                        let btn = numberContainer.querySelector('.number-btn');

                        // Cambiar estilos del botón principal
                        btn.classList.remove('bg-gradient-to-br', 'from-red-400', 'to-red-600', 'cursor-not-allowed');
                        btn.classList.remove('from-yellow-400', 'to-yellow-600');
                        btn.classList.add('bg-gradient-to-br', 'from-green-400', 'to-green-600', 'hover:from-green-500', 'hover:to-green-700', 'shadow-lg', 'hover:shadow-xl');
                        btn.setAttribute('data-status', 'disponible');
                        btn.disabled = false;
                        btn.title = 'Click para seleccionar';

                        // Remover el botón de liberar
                        let releaseBtn = numberContainer.querySelector('.release-btn');
                        if (releaseBtn) {
                            releaseBtn.remove();
                        }

                        // Actualizar estadísticas
                        updateStatistics();

                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: data.success,
                            timer: 3000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    } else if (data && data.error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.error,
                            confirmButtonColor: '#d33'
                        });
                    }
                })
                .catch(error => {
                    if (error !== 'Unauthorized' && error !== 'Forbidden') {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.error || 'Error al procesar la solicitud',
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            }
        });
    }
    </script>
    @endpush
</x-public-layout>

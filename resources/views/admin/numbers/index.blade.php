<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Administración de Números
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.raffles.index') }}"
                               class="inline-flex items-center px-3 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                                ← Volver a Rifas
                            </a>
                            @if(isset($currentRaffle) && $currentRaffle)
                                <span class="text-sm text-gray-600 dark:text-gray-400">Mostrando números de: <strong>{{ $currentRaffle->name }}</strong></span>
                            @endif
                        </div>

                        <!-- Acciones masivas -->
                        <div id="bulk-actions" class="hidden">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    <span id="selected-count">0</span> seleccionados
                                </span>
                                <button id="clear-selection"
                                        class="px-3 py-2 bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-600"
                                        onclick="clearSelection()">
                                    Limpiar
                                </button>
                            </div>

                            <!-- Campos adicionales para marcar como pagados -->
                            <div id="payment-fields" class="hidden grid grid-cols-1 md:grid-cols-3 gap-3 mb-3 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                <div>
                                    <label for="bulk-amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Monto (opcional)
                                    </label>
                                    <input type="number" id="bulk-amount" step="0.01" min="0"
                                           class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-green-500 focus:ring-green-500"
                                           placeholder="0.00">
                                </div>
                                <div>
                                    <label for="bulk-notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Notas (opcional)
                                    </label>
                                    <input type="text" id="bulk-notes" maxlength="500"
                                           class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-green-500 focus:ring-green-500"
                                           placeholder="Notas adicionales...">
                                </div>
                                <div class="flex items-end gap-2">
                                    <button id="mark-paid-bulk"
                                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                            onclick="markSelectedAsPaid()">
                                        Marcar como Pagados
                                    </button>
                                </div>
                            </div>

                            <!-- Botón para liberar -->
                            <div class="flex items-center gap-2">
                                <button id="release-bulk"
                                        class="px-3 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                        onclick="releaseSelected()">
                                    Liberar Números
                                </button>
                            </div>
                        </div>
                        <form method="GET" action="{{ route('admin.numbers.index') }}" class="flex items-center gap-2">
                            @if(request('raffle_id'))
                                <input type="hidden" name="raffle_id" value="{{ request('raffle_id') }}">
                            @endif
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar número, estado, participante, rifa" class="w-72 px-3 py-2 rounded border dark:bg-gray-700 dark:border-gray-600" />
                            <button type="submit" class="px-3 py-2 bg-indigo-600 text-white rounded">Buscar</button>
                            @if(request('q'))
                                <a href="{{ route('admin.numbers.index', array_filter(['raffle_id' => request('raffle_id')])) }}" class="px-3 py-2 bg-gray-300 dark:bg-gray-700 dark:text-gray-100 text-gray-800 rounded">Limpiar</a>
                            @endif
                        </form>
                        <a href="{{ route('admin.numbers.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Nuevo Número
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <input type="checkbox" id="select-all" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" onchange="toggleAllCheckboxes(this)">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rifa</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Número</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Participante</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Precio</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($numbers as $number)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox"
                                               class="number-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                               value="{{ $number->id }}"
                                               data-status="{{ $number->status }}"
                                               onchange="updateSelection()">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $number->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $number->raffle->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $number->number }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $number->status === 'pagado' ? 'bg-green-100 text-green-800' :
                                               ($number->status === 'reservado' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ ucfirst($number->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $number->participant ? $number->participant->name : 'Sin asignar' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $number->price !== null ? 'S/.' . number_format($number->price, 2) : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <a href="{{ route('admin.numbers.edit', $number->id) }}"
                                           class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                            Editar
                                        </a>
                                        @if($number->status === 'reservado')
                                            <form action="{{ route('admin.numbers.markPaid', $number->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                                    Marcar pagado
                                                </button>
                                            </form>
                                        @endif
                                        @if($number->status !== 'disponible')
                                            <form action="{{ route('admin.numbers.release', $number->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300"
                                                        onclick="return confirmRelease(event)">
                                                    Liberar
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.numbers.destroy', $number->id) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                                    onclick="return confirmDelete(event)">
                                                Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($numbers->hasPages())
                        <div class="mt-6">
                            {{ $numbers->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario oculto para acciones masivas -->
    <form id="bulk-action-form" method="POST" action="{{ route('admin.numbers.bulkMarkPaid') }}" style="display: none;">
        @csrf
        <input type="hidden" name="number_ids" id="bulk-number-ids">
        @if(request('raffle_id'))
            <input type="hidden" name="raffle_id" value="{{ request('raffle_id') }}">
        @endif
        @if(request('q'))
            <input type="hidden" name="q" value="{{ request('q') }}">
        @endif
    </form>

    <!-- Formulario oculto para liberar números -->
    <form id="bulk-release-form" method="POST" action="{{ route('admin.numbers.bulkRelease') }}" style="display: none;">
        @csrf
        <input type="hidden" name="number_ids" id="bulk-release-ids">
        @if(request('raffle_id'))
            <input type="hidden" name="raffle_id" value="{{ request('raffle_id') }}">
        @endif
        @if(request('q'))
            <input type="hidden" name="q" value="{{ request('q') }}">
        @endif
    </form>

    <script>
        function toggleAllCheckboxes(selectAllCheckbox) {
            const checkboxes = document.querySelectorAll('.number-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            updateSelection();
        }

        function updateSelection() {
            const checkboxes = document.querySelectorAll('.number-checkbox:checked');
            const selectedCount = checkboxes.length;
            const bulkActions = document.getElementById('bulk-actions');
            const selectedCountSpan = document.getElementById('selected-count');
            const markPaidButton = document.getElementById('mark-paid-bulk');
            const releaseButton = document.getElementById('release-bulk');
            const paymentFields = document.getElementById('payment-fields');

            selectedCountSpan.textContent = selectedCount;

            if (selectedCount > 0) {
                bulkActions.classList.remove('hidden');

                // Verificar si hay números reservados seleccionados
                const reservedSelected = Array.from(checkboxes).some(checkbox =>
                    checkbox.dataset.status === 'reservado'
                );

                // Verificar si hay números no disponibles (reservados o pagados) seleccionados
                const nonAvailableSelected = Array.from(checkboxes).some(checkbox =>
                    checkbox.dataset.status !== 'disponible'
                );

                // Mostrar/ocultar campos de pago
                if (reservedSelected) {
                    paymentFields.classList.remove('hidden');
                } else {
                    paymentFields.classList.add('hidden');
                }

                markPaidButton.disabled = !reservedSelected;
                markPaidButton.classList.toggle('opacity-50', !reservedSelected);
                markPaidButton.classList.toggle('cursor-not-allowed', !reservedSelected);

                releaseButton.disabled = !nonAvailableSelected;
                releaseButton.classList.toggle('opacity-50', !nonAvailableSelected);
                releaseButton.classList.toggle('cursor-not-allowed', !nonAvailableSelected);
            } else {
                bulkActions.classList.add('hidden');
                paymentFields.classList.add('hidden');
            }

            // Actualizar estado del checkbox "Seleccionar todo"
            const selectAllCheckbox = document.getElementById('select-all');
            const totalCheckboxes = document.querySelectorAll('.number-checkbox').length;
            selectAllCheckbox.checked = selectedCount === totalCheckboxes && totalCheckboxes > 0;
            selectAllCheckbox.indeterminate = selectedCount > 0 && selectedCount < totalCheckboxes;
        }

        function clearSelection() {
            const checkboxes = document.querySelectorAll('.number-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            document.getElementById('select-all').checked = false;
            updateSelection();
        }

        function markSelectedAsPaid() {
            const checkboxes = document.querySelectorAll('.number-checkbox:checked');
            const reservedNumbers = Array.from(checkboxes).filter(checkbox =>
                checkbox.dataset.status === 'reservado'
            );

            if (reservedNumbers.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Sin números válidos',
                    text: 'No hay números reservados seleccionados para marcar como pagados.',
                    confirmButtonText: 'Entendido'
                });
                return;
            }

            Swal.fire({
                title: '¿Confirmar acción?',
                html: `¿Estás seguro de que quieres marcar <strong>${reservedNumbers.length}</strong> número(s) como pagados?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Sí, marcar como pagados',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const numberIds = reservedNumbers.map(checkbox => checkbox.value);
                    const amount = document.getElementById('bulk-amount').value;
                    const notes = document.getElementById('bulk-notes').value;

                    document.getElementById('bulk-number-ids').value = JSON.stringify(numberIds);

                    // Agregar campos adicionales al formulario
                    let form = document.getElementById('bulk-action-form');
                    if (amount) {
                        let amountInput = document.createElement('input');
                        amountInput.type = 'hidden';
                        amountInput.name = 'amount';
                        amountInput.value = amount;
                        form.appendChild(amountInput);
                    }
                    if (notes) {
                        let notesInput = document.createElement('input');
                        notesInput.type = 'hidden';
                        notesInput.name = 'notes';
                        notesInput.value = notes;
                        form.appendChild(notesInput);
                    }

                    form.submit();
                }
            });
        }

        function releaseSelected() {
            const checkboxes = document.querySelectorAll('.number-checkbox:checked');
            const nonAvailableNumbers = Array.from(checkboxes).filter(checkbox =>
                checkbox.dataset.status !== 'disponible'
            );

            if (nonAvailableNumbers.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Sin números válidos',
                    text: 'No hay números reservados o pagados seleccionados para liberar.',
                    confirmButtonText: 'Entendido'
                });
                return;
            }

            Swal.fire({
                title: '¿Confirmar liberación?',
                html: `¿Estás seguro de que quieres liberar <strong>${nonAvailableNumbers.length}</strong> número(s)?<br><small class="text-gray-500">Los números volverán al estado "disponible" y se desasignarán de los participantes.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f59e0b',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Sí, liberar números',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const numberIds = nonAvailableNumbers.map(checkbox => checkbox.value);
                    document.getElementById('bulk-release-ids').value = JSON.stringify(numberIds);
                    document.getElementById('bulk-release-form').submit();
                }
            });
        }

        function confirmRelease(event) {
            Swal.fire({
                title: '¿Liberar Número?',
                text: '¿Seguro que deseas liberar este número?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f59e0b',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, Liberar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.closest('form').submit();
                }
            });
            return false;
        }

        function confirmDelete(event) {
            Swal.fire({
                title: '¿Eliminar Número?',
                text: '¿Estás seguro de que quieres eliminar este número?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, Eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.closest('form').submit();
                }
            });
            return false;
        }
    </script>
</x-app-layout>

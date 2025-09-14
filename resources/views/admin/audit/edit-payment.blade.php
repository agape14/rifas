<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Actualizar Pago') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Navegación -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <a href="{{ route('admin.audit.payments') }}"
                               class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                                ← Volver a Reporte de Cobros
                            </a>
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            ID: {{ $audit->id }} | {{ $audit->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del pago -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Información del Pago</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-2">Datos de la Rifa</h4>
                            <div class="space-y-2">
                                <p><span class="font-medium">Rifa:</span> {{ $audit->raffle->name }}</p>
                                <p><span class="font-medium">Número:</span> <span class="text-blue-600 dark:text-blue-400 font-bold">{{ $audit->number->number }}</span></p>
                                <p><span class="font-medium">Participante:</span> {{ $audit->participant->name ?? 'N/A' }}</p>
                                @if($audit->participant)
                                    @if($audit->participant->phone)
                                        <p><span class="font-medium">Teléfono:</span> {{ $audit->participant->phone }}</p>
                                    @endif
                                    @if($audit->participant->email)
                                        <p><span class="font-medium">Email:</span> {{ $audit->participant->email }}</p>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-2">Estado del Pago</h4>
                            <div class="space-y-2">
                                <p><span class="font-medium">Cambio:</span> {{ $audit->status_change_description }}</p>
                                <p><span class="font-medium">Tipo:</span> {{ $audit->action_type_description }}</p>
                                <p><span class="font-medium">Usuario:</span> {{ $audit->changedBy->name }}</p>
                                <p><span class="font-medium">Estado:</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($audit->payment_status_color === 'green') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @elseif($audit->payment_status_color === 'yellow') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @endif">
                                        {{ $audit->payment_status }}
                                    </span>
                                </p>
                                @if($audit->payment_confirmed && $audit->paymentConfirmedBy)
                                    <p><span class="font-medium">Confirmado por:</span> {{ $audit->paymentConfirmedBy->name }}</p>
                                    <p><span class="font-medium">Fecha confirmación:</span> {{ $audit->payment_confirmed_at->format('d/m/Y H:i') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario de actualización -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Actualizar Información del Pago</h3>

                    <form method="POST" action="{{ route('admin.audit.payments.update', $audit) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Monto (S/.)
                                </label>
                                <input type="number" id="amount" name="amount" step="0.01" min="0"
                                       value="{{ old('amount', $audit->amount) }}"
                                       class="w-full px-3 py-2 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                @error('amount')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="payment_evidence" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Evidencia de Pago
                                </label>
                                <input type="file" id="payment_evidence" name="payment_evidence"
                                       accept=".jpg,.jpeg,.png,.pdf"
                                       class="w-full px-3 py-2 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Formatos permitidos: JPG, PNG, PDF (máx. 2MB)
                                </p>
                                @error('payment_evidence')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror

                                @if($audit->payment_evidence_path)
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Evidencia actual:</p>
                                        <a href="{{ route('admin.audit.payments.evidence', $audit) }}"
                                           class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Descargar evidencia actual
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Notas
                            </label>
                            <textarea id="notes" name="notes" rows="3" maxlength="500"
                                      class="w-full px-3 py-2 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                      placeholder="Notas adicionales sobre el pago...">{{ old('notes', $audit->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        @if(!$audit->payment_confirmed)
                            <div class="mt-6">
                                <label class="flex items-center">
                                    <input type="checkbox" name="payment_confirmed" value="1"
                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        Confirmar este pago (marcará como definitivamente pagado)
                                    </span>
                                </label>
                            </div>
                        @endif

                        <div class="mt-6 flex gap-4">
                            <button type="submit"
                                    class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                Actualizar Pago
                            </button>

                            @if(!$audit->payment_confirmed)
                                <form method="POST" action="{{ route('admin.audit.payments.confirm', $audit) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
                                            onclick="return confirmPayment()">
                                        Confirmar Pago
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('admin.audit.payments') }}"
                               class="px-6 py-2 bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-400 dark:hover:bg-gray-600">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmPayment() {
            Swal.fire({
                title: '¿Confirmar Pago?',
                text: '¿Estás seguro de que quieres confirmar este pago?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, Confirmar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Enviar el formulario
                    event.target.closest('form').submit();
                }
            });
            return false; // Prevenir envío automático
        }
    </script>
</x-app-layout>

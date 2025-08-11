<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Editar Propuesta Comercial
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if(session('success'))
                        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('admin.reports.proposal.update') }}" method="POST" id="proposalForm">
                        @csrf

                        <h3 class="text-lg font-semibold mb-2">Características</h3>
                        <div id="featuresList" class="space-y-2 mb-4">
                            @forelse($features as $i => $f)
                                <div class="flex gap-2">
                                    <input type="text" name="features[]" value="{{ $f }}" class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" />
                                    <button type="button" class="px-3 py-2 bg-red-600 text-white rounded" onclick="this.parentElement.remove()">Eliminar</button>
                                </div>
                            @empty
                                <div class="flex gap-2">
                                    <input type="text" name="features[]" class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" placeholder="Nueva característica" />
                                </div>
                            @endforelse
                        </div>
                        <button type="button" class="mb-6 px-3 py-2 bg-blue-600 text-white rounded" onclick="addFeature()">+ Agregar característica</button>

                        <h3 class="text-lg font-semibold mb-2">Tarifario</h3>
                        <div id="pricingTable" class="space-y-2">
                            <div class="grid grid-cols-6 gap-2 text-xs text-gray-500 dark:text-gray-400">
                                <div>Tipo de Cliente</div>
                                <div>Tamaño</div>
                                <div>Venta Estimada</div>
                                <div>Costo Fijo</div>
                                <div>Comisión %</div>
                                <div>Servicios Opcionales</div>
                            </div>
                            @forelse($pricing as $i => $row)
                                <div class="grid grid-cols-6 gap-2">
                                    <input type="text" name="pricing[{{ $i }}][segment]" value="{{ $row['segment'] ?? '' }}" class="px-2 py-1 rounded border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" />
                                    <input type="text" name="pricing[{{ $i }}][size]" value="{{ $row['size'] ?? '' }}" class="px-2 py-1 rounded border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" />
                                    <input type="number" name="pricing[{{ $i }}][estimate]" value="{{ $row['estimate'] ?? '' }}" step="0.01" class="px-2 py-1 rounded border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" />
                                    <input type="number" name="pricing[{{ $i }}][fixed]" value="{{ $row['fixed'] ?? '' }}" step="0.01" class="px-2 py-1 rounded border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" />
                                    <input type="number" name="pricing[{{ $i }}][fee_pct]" value="{{ $row['fee_pct'] ?? '' }}" step="0.01" class="px-2 py-1 rounded border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" />
                                    <div class="flex gap-2">
                                        <input type="text" name="pricing[{{ $i }}][optional]" value="{{ $row['optional'] ?? '' }}" class="w-full px-2 py-1 rounded border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" />
                                        <button type="button" class="px-2 py-1 bg-red-600 text-white rounded" onclick="this.closest('div.grid').remove()">X</button>
                                    </div>
                                    <input type="hidden" name="pricing[{{ $i }}][fee]" value="{{ $row['fee'] ?? 0 }}" />
                                </div>
                            @empty
                                <div class="grid grid-cols-6 gap-2">
                                    <input type="text" name="pricing[0][segment]" class="px-2 py-1 rounded border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" placeholder="Emprendedor / Empresa" />
                                    <input type="text" name="pricing[0][size]" class="px-2 py-1 rounded border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" placeholder="Pequeño / Mediano / Grande" />
                                    <input type="number" name="pricing[0][estimate]" step="0.01" class="px-2 py-1 rounded border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" placeholder="1000" />
                                    <input type="number" name="pricing[0][fixed]" step="0.01" class="px-2 py-1 rounded border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" placeholder="50" />
                                    <input type="number" name="pricing[0][fee_pct]" step="0.01" class="px-2 py-1 rounded border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" placeholder="5" />
                                    <div class="flex gap-2">
                                        <input type="text" name="pricing[0][optional]" class="w-full px-2 py-1 rounded border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" placeholder="Difusión redes + S/. 30" />
                                        <button type="button" class="px-2 py-1 bg-red-600 text-white rounded" onclick="this.closest('div.grid').remove()">X</button>
                                    </div>
                                    <input type="hidden" name="pricing[0][fee]" value="0" />
                                </div>
                            @endforelse
                        </div>
                        <button type="button" class="mt-2 px-3 py-2 bg-blue-600 text-white rounded" onclick="addPricingRow()">+ Agregar fila</button>

                        <div class="mt-6">
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Guardar cambios</button>
                            <a href="{{ route('admin.reports.proposal') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded ml-2">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function addFeature(){
            const wrap = document.getElementById('featuresList');
            const div = document.createElement('div');
            div.className = 'flex gap-2';
            div.innerHTML = `<input type="text" name="features[]" class="w-full px-3 py-2 rounded border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" placeholder="Nueva característica" />
                             <button type="button" class="px-3 py-2 bg-red-600 text-white rounded" onclick="this.parentElement.remove()">Eliminar</button>`;
            wrap.appendChild(div);
        }
        function addPricingRow(){
            const wrap = document.getElementById('pricingTable');
            const count = wrap.querySelectorAll('div.grid.grid-cols-6').length;
            const idx = count;
            const row = document.createElement('div');
            row.className = 'grid grid-cols-6 gap-2';
            row.innerHTML = `
                <input type="text" name="pricing[${idx}][segment]" class="px-2 py-1 rounded border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" />
                <input type="text" name="pricing[${idx}][size]" class="px-2 py-1 rounded border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" />
                <input type="number" name="pricing[${idx}][estimate]" step="0.01" class="px-2 py-1 rounded border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" />
                <input type="number" name="pricing[${idx}][fixed]" step="0.01" class="px-2 py-1 rounded border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" />
                <input type="number" name="pricing[${idx}][fee_pct]" step="0.01" class="px-2 py-1 rounded border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" />
                <div class="flex gap-2">
                    <input type="text" name="pricing[${idx}][optional]" class="w-full px-2 py-1 rounded border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" />
                    <button type="button" class="px-2 py-1 bg-red-600 text-white rounded" onclick="this.closest('div.grid').remove()">X</button>
                </div>
                <input type="hidden" name="pricing[${idx}][fee]" value="0" />`;
            wrap.appendChild(row);
        }
    </script>
    @endpush
</x-app-layout>

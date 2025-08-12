<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Participantes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
                        <div>
                            <h3 class="text-lg font-semibold">Lista de Participantes</h3>
                            @if(!empty(request('q')))
                                <p class="text-xs text-gray-500 dark:text-gray-400">Filtro: "{{ request('q') }}"</p>
                            @endif
                        </div>
                        <form method="GET" action="{{ route('admin.participants.index') }}" class="flex items-center gap-2">
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre, email, teléfono, número, rifa, estado" class="w-80 px-3 py-2 rounded border dark:bg-gray-700 dark:border-gray-600" />
                            <button type="submit" class="px-3 py-2 bg-indigo-600 text-white rounded">Buscar</button>
                            @if(request('q'))
                                <a href="{{ route('admin.participants.index') }}" class="px-3 py-2 bg-gray-300 dark:bg-gray-700 dark:text-gray-100 text-gray-800 rounded">Limpiar</a>
                            @endif
                        </form>
                        <a href="{{ route('admin.participants.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            Nuevo Participante
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Participante
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Contacto
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Números
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Total Invertido
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Fecha Registro
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($participants as $participant)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    @if($participant->photo)
                                                        <img class="h-10 w-10 rounded-full object-cover"
                                                             src="{{ asset('storage/' . $participant->photo) }}"
                                                             alt="{{ $participant->name }}">
                                                    @else
                                                        <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                            <span class="text-gray-500 dark:text-gray-400 text-sm font-medium">
                                                                {{ strtoupper(substr($participant->name, 0, 1)) }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {{ $participant->name }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                                @if($participant->phone)
                                                    <div>{{ $participant->phone }}</div>
                                                @endif
                                                @if($participant->email)
                                                    <div class="text-gray-500 dark:text-gray-400">{{ $participant->email }}</div>
                                                @endif
                                                @if(!$participant->phone && !$participant->email)
                                                    <span class="text-gray-400 dark:text-gray-500">Sin contacto</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                                <span class="font-medium">{{ $participant->numbers->count() }}</span> números
                                            </div>
                                            @if($participant->numbers->count() > 0)
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $participant->numbers->where('status', 'pagado')->count() }} pagados,
                                                    {{ $participant->numbers->where('status', 'reservado')->count() }} reservados
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                                S/. {{ number_format($participant->numbers->sum('price'), 0) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $participant->created_at->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('admin.participants.show', $participant->id) }}"
                                                   class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                    Ver
                                                </a>
                                                <a href="{{ route('admin.participants.edit', $participant->id) }}"
                                                   class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                                    Editar
                                                </a>
                                                <form action="{{ route('admin.participants.destroy', $participant->id) }}"
                                                      method="POST" class="inline"
                                                      onsubmit="return confirm('¿Estás seguro de que quieres eliminar este participante?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                            No hay participantes registrados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($participants->hasPages())
                        <div class="mt-6">
                            {{ $participants->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

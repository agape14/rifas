<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Usuarios</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if(session('success'))
                        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
                    @endif

                    <form class="mb-4 flex flex-wrap gap-2 items-end" method="GET">
                        <div>
                            <label class="block text-xs mb-1">Buscar</label>
                            <input type="text" name="q" value="{{ $q }}" placeholder="Nombre o email" class="px-3 py-2 rounded border dark:bg-gray-900 dark:border-gray-700" />
                        </div>
                        <div>
                            <label class="block text-xs mb-1">Rifa</label>
                            <select name="raffle_id" class="px-3 py-2 rounded border dark:bg-gray-900 dark:border-gray-700">
                                <option value="">Todas</option>
                                @foreach($raffles as $r)
                                    <option value="{{ $r->id }}" {{ (string)$raffleId === (string)$r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <button class="px-3 py-2 bg-indigo-600 text-white rounded">Filtrar</button>
                            <a href="{{ route('admin.users.index') }}" class="px-3 py-2 bg-gray-300 text-gray-800 rounded">Limpiar</a>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-2 text-left">Nombre</th>
                                    <th class="px-4 py-2 text-left">Email</th>
                                    <th class="px-4 py-2 text-left">Rol</th>
                                    <th class="px-4 py-2 text-left">Rifa asignada</th>
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($users as $u)
                                    <tr>
                                        <td class="px-4 py-2">{{ $u->name }}</td>
                                        <td class="px-4 py-2">{{ $u->email }}</td>
                                        <td class="px-4 py-2">{{ $u->is_admin ? 'Admin' : 'Gestor' }}</td>
                                        <td class="px-4 py-2">{{ $u->managedRaffle?->name ?? '-' }}</td>
                                        <td class="px-4 py-2 text-right">
                                            <a href="{{ route('admin.users.edit', $u) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $users->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

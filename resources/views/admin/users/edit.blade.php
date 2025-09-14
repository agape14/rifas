<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Editar Usuario</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-sm mb-1">Nombre</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full px-3 py-2 rounded border dark:bg-gray-900 dark:border-gray-700" />
                            @error('name')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div>
                            <label class="block text-sm mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-3 py-2 rounded border dark:bg-gray-900 dark:border-gray-700" />
                            @error('email')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div>
                            <label class="block text-sm mb-1">Nueva contraseña (opcional)</label>
                            <div class="relative">
                                <input type="password" name="password" id="password" class="w-full px-3 py-2 pr-10 rounded border dark:bg-gray-900 dark:border-gray-700" placeholder="********" />
                                <button type="button" class="absolute inset-y-0 right-0 px-3 text-sm text-gray-500" onclick="togglePw('password', this)">Ver</button>
                            </div>
                            @error('password')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div>
                            <label class="block text-sm mb-1">Confirmar contraseña</label>
                            <div class="relative">
                                <input type="password" name="password_confirmation" id="password_confirmation" class="w-full px-3 py-2 pr-10 rounded border dark:bg-gray-900 dark:border-gray-700" placeholder="********" />
                                <button type="button" class="absolute inset-y-0 right-0 px-3 text-sm text-gray-500" onclick="togglePw('password_confirmation', this)">Ver</button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm mb-1">Rol</label>
                            <select name="is_admin" class="w-full px-3 py-2 rounded border dark:bg-gray-900 dark:border-gray-700">
                                <option value="0" {{ old('is_admin', $user->is_admin ? 1 : 0) == 0 ? 'selected' : '' }}>Gestor</option>
                                <option value="1" {{ old('is_admin', $user->is_admin ? 1 : 0) == 1 ? 'selected' : '' }}>Administrador</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm mb-1">Rifa asignada (solo gestores)</label>
                            <div class="flex gap-2">
                                <select name="managed_raffle_id" class="w-full px-3 py-2 rounded border dark:bg-gray-900 dark:border-gray-700">
                                    <option value="">— Ninguna —</option>
                                    @foreach($raffles as $r)
                                        <option value="{{ $r->id }}" {{ (int)old('managed_raffle_id', $user->managed_raffle_id) === (int)$r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                                    @endforeach
                                </select>
                                @if($user->managed_raffle_id)
                                    <button type="submit" name="clear_assignment" value="1" class="px-3 py-2 bg-yellow-500 text-white rounded" onclick="return confirmClearAssignment(event)">Quitar</button>
                                @endif
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Guardar</button>
                            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded ml-2">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function togglePw(id, btn){
            const el = document.getElementById(id);
            if (!el) return;
            if (el.type === 'password') {
                el.type = 'text';
                btn.textContent = 'Ocultar';
            } else {
                el.type = 'password';
                btn.textContent = 'Ver';
            }
        }

        function confirmClearAssignment(event) {
            Swal.fire({
                title: '¿Quitar Rifa Asignada?',
                text: '¿Estás seguro de que quieres quitar la rifa asignada a este usuario?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f59e0b',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, Quitar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.closest('form').submit();
                }
            });
            return false;
        }
    </script>
    @endpush
</x-app-layout>

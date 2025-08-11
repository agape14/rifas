<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6 text-center px-2">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Inicia sesión</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Accede para gestionar tus rifas y sorteos</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4 sm:space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Correo electrónico" class="text-sm sm:text-base" />
            <div class="mt-1 relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 7.5l-9.75 6.5L2.25 7.5m19.5 0v9.75A2.25 2.25 0 0119.5 19.5h-15A2.25 2.25 0 012.25 17.25V7.5m19.5 0L12 14 2.25 7.5"/>
                    </svg>
                </span>
                <x-text-input id="email" class="block w-full pl-10 px-3 py-2 sm:py-3 text-sm sm:text-base" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Ingresa tu correo" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Contraseña" class="text-sm sm:text-base" />
            <div class="mt-1 relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V7.5a4.5 4.5 0 10-9 0v3m12 0H4.5m12 0a2.25 2.25 0 012.25 2.25v6A2.25 2.25 0 0116.5 21H7.5A2.25 2.25 0 015.25 18.75v-6A2.25 2.25 0 017.5 10.5h9z"/>
                    </svg>
                </span>
                <x-text-input id="password" class="block w-full pl-10 pr-10 px-3 py-2 sm:py-3 text-sm sm:text-base" type="password" name="password" required autocomplete="current-password" placeholder="Ingresa tu contraseña" />
                <button type="button" onclick="const i=document.getElementById('password'); i.type = i.type==='password' ? 'text' : 'password'" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-500" aria-label="Mostrar u ocultar contraseña">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12C3.51 7.65 7.5 4.5 12 4.5s8.49 3.15 9.75 7.5C20.49 16.35 16.5 19.5 12 19.5S3.51 16.35 2.25 12z"/>
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Recuérdame</span>
            </label>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-between mt-4 space-y-2 sm:space-y-0 sm:space-x-3">
            {{-- @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif--}}

            <x-primary-button class="w-full sm:w-auto px-4 py-2 sm:py-3 text-sm sm:text-base">
                Iniciar sesión
            </x-primary-button>
        </div>

        @if (Route::has('register'))
            <div class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
                ¿No tienes cuenta?
                <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">Regístrate</a>
            </div>
        @endif
    </form>
</x-guest-layout>

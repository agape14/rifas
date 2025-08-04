<x-public-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Rifas disponibles
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($raffles as $raffle)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        @if($raffle->banner)
                            <img src="{{ asset('storage/' . $raffle->banner) }}" class="w-full h-48 object-cover" alt="{{ $raffle->name }}">
                        @endif
                        <div class="p-6">
                            <h5 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ $raffle->name }}</h5>
                            <p class="text-gray-600 dark:text-gray-400 mb-3">{{ Str::limit($raffle->description, 80) }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                Sorteo: {{ \Carbon\Carbon::parse($raffle->draw_date)->format('d/m/Y') }}
                            </p>
                            <a href="{{ route('public.raffle.show', $raffle->id) }}"
                               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Ver detalles
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-public-layout>

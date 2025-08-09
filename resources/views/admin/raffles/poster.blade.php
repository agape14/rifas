<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Poster de la Rifa: {{ $raffle->name }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('admin.raffles.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded hover:bg-gray-300 dark:hover:bg-gray-600">← Volver a Rifas</a>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="text-center">
                    @if($raffle->banner)
                        <img src="{{ asset('storage/' . $raffle->banner) }}" class="mx-auto rounded mb-4 max-h-48 object-cover" alt="{{ $raffle->name }}">
                    @endif
                    <h1 class="text-2xl font-bold mb-2">{{ $raffle->name }}</h1>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Sorteo: {{ \Carbon\Carbon::parse($raffle->draw_date)->format('d/m/Y H:i') }}</p>
                    <div id="qrcode" class="mx-auto mb-4"></div>
                    <noscript>
                        <p class="mt-2">QR: <a href="{{ $url }}">{{ $url }}</a></p>
                    </noscript>
                    <p class="text-sm text-gray-500 dark:text-gray-400 break-all">{{ $url }}</p>
                </div>
                @if($raffle->prizes->count())
                    <div class="mt-6">
                        <h3 class="font-semibold mb-2">Premios</h3>
                        <ul class="list-disc list-inside text-gray-700 dark:text-gray-300">
                            @foreach($raffle->prizes as $prize)
                                <li>{{ $prize->name }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            try {
                if (window.QRCode) {
                    new QRCode(document.getElementById('qrcode'), {
                        text: @json($url),
                        width: 200,
                        height: 200,
                    });
                } else {
                    const img = document.createElement('img');
                    img.src = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + encodeURIComponent(@json($url));
                    img.alt = 'QR Code';
                    document.getElementById('qrcode').appendChild(img);
                }
            } catch (e) {
                const img = document.createElement('img');
                img.src = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + encodeURIComponent(@json($url));
                img.alt = 'QR Code';
                document.getElementById('qrcode').appendChild(img);
            }
        });
    </script>
    @endpush
</x-app-layout>

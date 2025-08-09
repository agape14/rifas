<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            QR de la Rifa: {{ $raffle->name }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('admin.raffles.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded hover:bg-gray-300 dark:hover:bg-gray-600">← Volver a Rifas</a>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center text-gray-900 dark:text-gray-100">
                    <p class="mb-4 text-gray-700 dark:text-gray-300">Escanea o comparte este QR para acceder a la rifa pública:</p>
                    <div id="qrcode" class="mx-auto"></div>
                    <noscript>
                        <p class="mt-4">QR: <a href="{{ $url }}">{{ $url }}</a></p>
                    </noscript>
                    <p class="mt-4 text-sm text-gray-600 dark:text-gray-400 break-all">{{ $url }}</p>
                    <a href="{{ $url }}" target="_blank" class="inline-block mt-4 px-4 py-2 bg-indigo-600 text-white rounded">Abrir enlace</a>
                </div>
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
                        width: 256,
                        height: 256,
                    });
                } else {
                    // Fallback: usar un generador de QR remoto
                    const img = document.createElement('img');
                    img.src = 'https://api.qrserver.com/v1/create-qr-code/?size=256x256&data=' + encodeURIComponent(@json($url));
                    img.alt = 'QR Code';
                    document.getElementById('qrcode').appendChild(img);
                }
            } catch (e) {
                const img = document.createElement('img');
                img.src = 'https://api.qrserver.com/v1/create-qr-code/?size=256x256&data=' + encodeURIComponent(@json($url));
                img.alt = 'QR Code';
                document.getElementById('qrcode').appendChild(img);
            }
        });
    </script>
    @endpush
</x-app-layout>

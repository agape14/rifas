<x-public-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Sorteo: {{ $raffle->name }}
        </h2>
    </x-slot>

    @push('styles')
    <style>
        .wheel-spin {
            animation: spin 0.1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .winner-glow {
            animation: glow 2s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from { text-shadow: 0 0 20px #10B981; }
            to { text-shadow: 0 0 30px #10B981, 0 0 40px #10B981; }
        }

                                /* Estilos para la ruleta */
        .wheel-container {
            position: relative;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            overflow: hidden;
            background: #2d3748;
        }

        .wheel-segment {
            position: absolute;
            width: 50%;
            height: 50%;
            transform-origin: 100% 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 16px;
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.8);
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid rgba(255,255,255,0.3);
        }

        .wheel-segment::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: inherit;
            clip-path: polygon(0 0, 100% 0, 100% 100%);
            z-index: -1;
        }

        .wheel-segment:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        }

        .wheel-segment.yellow {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        }

        .wheel-segment.red {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .wheel-segment.blue {
            background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
        }

        .wheel-segment.purple {
            background: linear-gradient(135deg, #a78bfa 0%, #8b5cf6 100%);
        }

        .wheel-segment.green {
            background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
        }

        .wheel-segment.orange {
            background: linear-gradient(135deg, #fb923c 0%, #f97316 100%);
        }

        /* Estilos para mejorar la legibilidad de los números */
        .wheel-number-text {
            filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.8));
            font-family: 'Arial', sans-serif;
        }

        /* Mejorar contraste para números pequeños */
        .wheel-number-text[font-size="8"],
        .wheel-number-text[font-size="9"],
        .wheel-number-text[font-size="10"] {
            filter: drop-shadow(1px 1px 2px rgba(0,0,0,1)) drop-shadow(0 0 1px rgba(255,255,255,0.3));
            font-weight: 900;
        }

        .wheel-spinning {
            animation: wheelSpin 0.05s linear infinite;
        }

        .wheel-spinning svg {
            animation: wheelSpin 0.05s linear infinite;
        }

        @keyframes wheelSpin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Estilos para el indicador/bache */
        .wheel-indicator {
            position: absolute;
            top: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 16px solid transparent;
            border-right: 16px solid transparent;
            border-bottom: 24px solid #dc2626;
            z-index: 30;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.5));
        }

        .wheel-indicator-dot {
            position: absolute;
            top: 2px;
            left: 50%;
            transform: translateX(-50%);
            width: 6px;
            height: 8px;
            background: #dc2626;
            border-radius: 50%;
            z-index: 30;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.5));
        }

        .wheel-indicator::after {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 12px solid transparent;
            border-right: 12px solid transparent;
            border-bottom: 18px solid #b91c1c;
            z-index: 31;
        }

                .winner-segment {
            animation: winnerPulse 1s ease-in-out infinite alternate;
            box-shadow: 0 0 20px #10B981;
        }

        @keyframes winnerPulse {
            from {
                transform: scale(1);
                box-shadow: 0 0 20px #10B981;
            }
            to {
                transform: scale(1.1);
                box-shadow: 0 0 30px #10B981, 0 0 40px #10B981;
            }
        }

        /* Efecto para números no ganadores */
        .wheel-segment-svg.loser {
            filter: grayscale(100%) brightness(0.3) !important;
            transition: filter 0.5s ease;
        }

        /* Efecto para el ganador */
        .wheel-segment-svg.winner {
            filter: drop-shadow(0 0 20px #10B981) !important;
            animation: winnerPulse 1s ease-in-out infinite alternate;
        }

        /* Estilos para el modal del ganador */
        .winner-modal {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: 3px solid #fff;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }

        .winner-number-display {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            border: 3px solid #fff;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        /* Estilos para la descripción con HTML */
        .prose {
            line-height: 1.6;
        }

        .prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
            font-weight: 600;
            margin-top: 1.5em;
            margin-bottom: 0.5em;
        }

        .prose h1 { font-size: 1.5em; }
        .prose h2 { font-size: 1.25em; }
        .prose h3 { font-size: 1.125em; }

        .prose p {
            margin-bottom: 1em;
        }

        .prose ul, .prose ol {
            margin-bottom: 1em;
            padding-left: 1.5em;
        }

        .prose li {
            margin-bottom: 0.25em;
        }

        .prose strong {
            font-weight: 600;
        }

        .prose em {
            font-style: italic;
        }

        .prose a {
            color: #3b82f6;
            text-decoration: underline;
        }

        .prose a:hover {
            color: #2563eb;
        }

        .prose blockquote {
            border-left: 4px solid #e5e7eb;
            padding-left: 1em;
            margin: 1em 0;
            font-style: italic;
        }

        .dark .prose blockquote {
            border-left-color: #4b5563;
        }

        /* Estilos para el modal de ganador */
        .winner-modal {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .winner-number-display {
            font-size: 4rem;
            font-weight: bold;
            color: #10B981;
            text-shadow: 0 0 20px #10B981;
        }

        /* Animaciones para el modal */
        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.8);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .animate-bounce {
            animation: bounce 1s infinite;
        }

        @keyframes bounce {
            0%, 20%, 53%, 80%, 100% {
                transform: translate3d(0,0,0);
            }
            40%, 43% {
                transform: translate3d(0, -30px, 0);
            }
            70% {
                transform: translate3d(0, -15px, 0);
            }
            90% {
                transform: translate3d(0, -4px, 0);
            }
        }
    </style>
    @endpush

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Información del sorteo -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold mb-4" style="color: {{ $raffle->theme_color ?? '#000' }}">
                    {{ $raffle->name }}
                </h1>
                <p class="text-xl text-gray-600 dark:text-gray-400 mb-4">
                    Fecha del sorteo: {{ \Carbon\Carbon::parse($raffle->draw_date)->format('d/m/Y H:i') }}
                </p>

                <!-- Descripción de la rifa -->
                @if($raffle->description)
                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg mx-4 sm:mx-0">
                        <div class="prose prose-sm sm:prose-base max-w-none text-gray-700 dark:text-gray-300">
                            {!! $raffle->description !!}
                        </div>
                    </div>
                @endif

                <!-- Estado de la rifa -->
                @if($raffle->status === 'finalizada')
                    <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                        <div class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="font-semibold">Esta rifa ya ha sido finalizada. No se puede realizar más sorteos.</span>
                        </div>
                    </div>
                @endif

                <!-- Estado del sorteo -->
                <div class="mb-6">
                    <span id="draw-status" class="px-4 py-2 rounded-full text-sm font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        <span id="status-text">Listo para comenzar</span>
                    </span>
                </div>
            </div>

            <!-- Premios -->
            @if($raffle->prizes->count() > 0)
                <div class="mb-8">
                    <h3 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-6 text-center">Premios</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                        @foreach($raffle->prizes as $prize)
                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg border-2 border-transparent prize-card mx-2 sm:mx-0" data-prize-id="{{ $prize->id }}">
                                @if($prize->image)
                                    <img src="{{ asset('storage/' . $prize->image) }}" class="w-full h-48 object-cover" alt="{{ $prize->name }}">
                                @endif
                                <div class="p-4 sm:p-6">
                                    <h5 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ $prize->name }}</h5>
                                    <p class="text-gray-600 dark:text-gray-400">{{ $prize->description }}</p>
                                    <div class="mt-3">
                                        <span class="prize-winner text-sm font-medium text-green-600 dark:text-green-400 hidden">
                                            Ganador: <span class="winner-info"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Ruleta del sorteo -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-4 sm:p-6 lg:p-8 mb-8 mx-2 sm:mx-0">
                <div class="text-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">Ruleta del Sorteo</h3>
                    <p class="text-gray-600 dark:text-gray-400">Números participantes: <span id="participant-count">{{ $raffle->numbers->where('status', 'pagado')->count() }}</span></p>

                    <!-- Controles de vista -->
                    <div class="mt-4 flex justify-center gap-2">
                        <button id="wheel-view-btn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                            Vista Ruleta
                        </button>
                        <button id="list-view-btn" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-sm">
                            Vista Lista
                        </button>
                    </div>
                </div>

                                                                <!-- Ruleta visual -->
                <div class="flex justify-center mb-8">
                    <div class="relative w-80 h-80 sm:w-96 sm:h-96">
                        <!-- Círculo exterior de la ruleta -->
                        <div class="absolute inset-0 border-8 border-gray-400 rounded-full bg-gray-200 shadow-xl"></div>

                        <!-- Contenedor de la ruleta -->
                        <div id="wheel-container" class="absolute inset-4 rounded-full overflow-hidden">
                            <!-- La ruleta se generará dinámicamente -->
                        </div>

                        <!-- Centro de la ruleta -->
                        <div class="absolute inset-1/4 bg-white dark:bg-gray-800 rounded-full border-4 border-indigo-600 dark:border-indigo-400 flex items-center justify-center shadow-lg z-20">
                            <div class="text-center">
                                <div id="current-number" class="text-3xl sm:text-5xl font-bold text-indigo-600 dark:text-indigo-400 mb-1">
                                    ?
                                </div>
                                <div id="current-participant" class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                                    Esperando sorteo...
                                </div>
                            </div>
                        </div>

                        <!-- Indicador/Bache para detener la ruleta -->
                        <div class="wheel-indicator"></div>
                        <div class="wheel-indicator-dot"></div>
                    </div>
                </div>

                <!-- Vista de lista de números (oculta por defecto) -->
                <div id="list-view" class="hidden">
                    <div class="max-h-96 overflow-y-auto border rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
                        <h4 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Números Participantes</h4>
                        <div id="numbers-grid" class="grid grid-cols-8 sm:grid-cols-10 md:grid-cols-12 lg:grid-cols-15 gap-2">
                            <!-- Los números se generarán dinámicamente -->
                        </div>
                    </div>
                </div>
                </div>

                <!-- Controles del sorteo -->
                <div class="flex flex-col sm:flex-row justify-center space-y-2 sm:space-y-0 sm:space-x-4 mb-6">
                    <button id="start-draw" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 sm:px-6 rounded-lg transition-colors duration-200 text-sm sm:text-base">
                        Iniciar Sorteo
                    </button>
                    <button id="stop-draw" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 sm:px-6 rounded-lg transition-colors duration-200 hidden text-sm sm:text-base">
                        Detener Sorteo
                    </button>
                    <button id="next-prize" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 sm:px-6 rounded-lg transition-colors duration-200 hidden text-sm sm:text-base">
                        Siguiente Premio
                    </button>
                    @auth
                        @if(Auth::user()->is_admin && $raffle->status !== 'finalizada')
                            <button id="manual-finish" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-4 sm:px-6 rounded-lg transition-colors duration-200 text-sm sm:text-base">
                                Finalizar Sorteo
                            </button>
                        @endif
                    @endauth
                </div>

                <!-- Progreso del sorteo -->
                <div class="mb-6">
                    <div class="flex flex-col sm:flex-row justify-between text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-2 space-y-1 sm:space-y-0">
                        <span>Premio actual: <span id="current-prize-name">-</span></span>
                        <span><span id="current-prize-index">0</span> de {{ $raffle->prizes->count() }}</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div id="progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>
            </div>

            <!-- Historial de ganadores -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 sm:p-6 mx-4 sm:mx-0">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Ganadores</h3>
                <div id="winners-list" class="space-y-3">
                    <!-- Los ganadores se mostrarán aquí dinámicamente -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de felicitaciones -->
    <div id="congratulations-modal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="winner-modal rounded-lg p-8 max-w-md mx-4 text-center text-white shadow-2xl">
            <div class="mb-6">
                <div class="text-6xl mb-4 animate-bounce">🎉</div>
                <h3 class="text-2xl font-bold mb-2">¡Felicitaciones!</h3>
                <p class="text-white/90 mb-4">Número ganador:</p>
                <div id="winner-number" class="winner-number-display mb-2"></div>
                <div id="winner-name" class="text-xl mb-4 text-white"></div>
                <div id="winner-prize" class="text-lg font-semibold text-yellow-300"></div>
            </div>
            <button id="close-congratulations" class="bg-white text-indigo-600 hover:bg-gray-100 font-bold py-3 px-8 rounded-lg transition-colors duration-200 shadow-lg">
                Continuar
            </button>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const raffle = @json($raffle);
        const prizes = @json($raffle->prizes);
        const participants = @json($participants);

        let currentPrizeIndex = 0;
        let isDrawing = false;
        let drawInterval;
        let winners = [];
        let availableNumbers = [...participants];

        // Elementos del DOM
        const startDrawBtn = document.getElementById('start-draw');
        const stopDrawBtn = document.getElementById('stop-draw');
        const nextPrizeBtn = document.getElementById('next-prize');
        const currentNumber = document.getElementById('current-number');
        const currentParticipant = document.getElementById('current-participant');
        const currentPrizeName = document.getElementById('current-prize-name');
        const currentPrizeIndexElement = document.getElementById('current-prize-index');
        const progressBar = document.getElementById('progress-bar');
        const statusText = document.getElementById('status-text');

        // Elementos para las vistas alternativas
        const wheelViewBtn = document.getElementById('wheel-view-btn');
        const listViewBtn = document.getElementById('list-view-btn');
        const wheelContainer = document.getElementById('wheel-container');
        const listView = document.getElementById('list-view');
        const numbersGrid = document.getElementById('numbers-grid');
        const drawStatus = document.getElementById('draw-status');
        const participantCount = document.getElementById('participant-count');
        const winnersList = document.getElementById('winners-list');

        // Inicializar
        updatePrizeInfo();
        updateProgress();
        createWheel();
        createNumbersList();

        // Sugerir vista de lista si hay muchos números
        if (availableNumbers.length > 50) {
            setTimeout(() => {
                showListView();
            }, 2000); // Cambiar automáticamente después de 2 segundos
        }

        // Event listeners
        startDrawBtn.addEventListener('click', startDraw);
        stopDrawBtn.addEventListener('click', stopDraw);
        nextPrizeBtn.addEventListener('click', nextPrize);

        // Event listeners para vistas alternativas
        wheelViewBtn.addEventListener('click', showWheelView);
        listViewBtn.addEventListener('click', showListView);
        document.getElementById('close-congratulations').addEventListener('click', closeCongratulations);

        // Botón de finalización manual (solo admin)
        const manualFinishBtn = document.getElementById('manual-finish');
        if (manualFinishBtn) {
            manualFinishBtn.addEventListener('click', () => {
                Swal.fire({
                    title: '¿Finalizar Sorteo?',
                    text: '¿Estás seguro de que quieres finalizar el sorteo manualmente? Esta acción no se puede deshacer.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#7c3aed',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Sí, Finalizar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        finishRaffle();
                    }
                });
            });
        }

        // Deshabilitar controles si la rifa está finalizada
        @if($raffle->status === 'finalizada')
        startDrawBtn.disabled = true;
        startDrawBtn.classList.add('opacity-50', 'cursor-not-allowed');
        startDrawBtn.textContent = 'Sorteo Finalizado';
        stopDrawBtn.disabled = true;
        stopDrawBtn.classList.add('opacity-50', 'cursor-not-allowed');
        nextPrizeBtn.disabled = true;
        nextPrizeBtn.classList.add('opacity-50', 'cursor-not-allowed');
        statusText.textContent = 'Sorteo Finalizado';
        drawStatus.className = 'px-4 py-2 rounded-full text-sm font-semibold bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
        currentNumber.textContent = '✓';
        currentParticipant.textContent = 'Sorteo completado';
        @endif

                        // Función para crear la ruleta visual
        function createWheel() {
            const wheelContainer = document.getElementById('wheel-container');
            if (!wheelContainer) {
                console.error('No se encontró el contenedor de la ruleta');
                return;
            }

            console.log('Creando ruleta con', availableNumbers.length, 'segmentos');
            wheelContainer.innerHTML = '';

            // Mostrar sugerencia si hay muchos números
            // Comentado: Sugerencia de vista lista (no se muestra)
            // if (availableNumbers.length > 50) {
            //     const suggestion = document.createElement('div');
            //     suggestion.className = 'absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 rounded-full z-10';
            //     suggestion.innerHTML = `
            //         <div class="text-center text-white p-4">
            //             <p class="text-sm font-semibold mb-2">Muchos números para mostrar</p>
            //             <p class="text-xs">Usa la "Vista Lista" para mejor legibilidad</p>
            //         </div>
            //     `;
            //     wheelContainer.appendChild(suggestion);
            // }

            const colors = ['yellow', 'red', 'blue', 'purple', 'green', 'orange'];
            const totalSegments = availableNumbers.length;
            const anglePerSegment = 360 / totalSegments;
            const radius = 150; // Radio del círculo

            // Crear SVG para la ruleta
            const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            svg.setAttribute('width', '100%');
            svg.setAttribute('height', '100%');
            svg.setAttribute('viewBox', '0 0 300 300');
            svg.style.transform = 'rotate(-90deg)'; // Rotar para que el primer segmento esté arriba

            // Agregar definiciones de gradientes
            const defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');

            const gradients = {
                yellow: ['#fbbf24', '#f59e0b'],
                red: ['#ef4444', '#dc2626'],
                blue: ['#60a5fa', '#3b82f6'],
                purple: ['#a78bfa', '#8b5cf6'],
                green: ['#34d399', '#10b981'],
                orange: ['#fb923c', '#f97316']
            };

            Object.keys(gradients).forEach(colorName => {
                const gradient = document.createElementNS('http://www.w3.org/2000/svg', 'linearGradient');
                gradient.setAttribute('id', colorName + 'Gradient');
                gradient.setAttribute('x1', '0%');
                gradient.setAttribute('y1', '0%');
                gradient.setAttribute('x2', '100%');
                gradient.setAttribute('y2', '100%');

                const stop1 = document.createElementNS('http://www.w3.org/2000/svg', 'stop');
                stop1.setAttribute('offset', '0%');
                stop1.setAttribute('stop-color', gradients[colorName][0]);

                const stop2 = document.createElementNS('http://www.w3.org/2000/svg', 'stop');
                stop2.setAttribute('offset', '100%');
                stop2.setAttribute('stop-color', gradients[colorName][1]);

                gradient.appendChild(stop1);
                gradient.appendChild(stop2);
                defs.appendChild(gradient);
            });

            svg.appendChild(defs);

            availableNumbers.forEach((participant, index) => {
                const startAngle = index * anglePerSegment;
                const endAngle = (index + 1) * anglePerSegment;
                const colorIndex = index % colors.length;
                const color = colors[colorIndex];

                // Calcular coordenadas del arco
                const startRad = (startAngle - 90) * Math.PI / 180;
                const endRad = (endAngle - 90) * Math.PI / 180;

                const x1 = 150 + radius * Math.cos(startRad);
                const y1 = 150 + radius * Math.sin(startRad);
                const x2 = 150 + radius * Math.cos(endRad);
                const y2 = 150 + radius * Math.sin(endRad);

                // Crear el segmento
                const largeArcFlag = anglePerSegment > 180 ? 1 : 0;
                const pathData = `M 150 150 L ${x1} ${y1} A ${radius} ${radius} 0 ${largeArcFlag} 1 ${x2} ${y2} Z`;

                const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                path.setAttribute('d', pathData);
                path.setAttribute('fill', `url(#${color}Gradient)`);
                path.setAttribute('stroke', 'rgba(255,255,255,0.3)');
                path.setAttribute('stroke-width', '2');
                path.dataset.number = participant.number;
                path.dataset.participant = participant.participant_name;
                path.dataset.index = index;
                path.classList.add('wheel-segment-svg');

                // Agregar texto al segmento
                const textAngle = (startAngle + endAngle) / 2;
                const textRad = (textAngle - 90) * Math.PI / 180;
                const textRadius = radius * 0.7;
                const textX = 150 + textRadius * Math.cos(textRad);
                const textY = 150 + textRadius * Math.sin(textRad);

                // Calcular tamaño de fuente dinámico basado en el número de segmentos
                let fontSize = 16;
                if (totalSegments > 50) {
                    fontSize = Math.max(8, 16 - (totalSegments - 50) * 0.15);
                } else if (totalSegments > 30) {
                    fontSize = Math.max(10, 16 - (totalSegments - 30) * 0.3);
                } else if (totalSegments > 20) {
                    fontSize = Math.max(12, 16 - (totalSegments - 20) * 0.4);
                }

                const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                text.setAttribute('x', textX);
                text.setAttribute('y', textY);
                text.setAttribute('text-anchor', 'middle');
                text.setAttribute('dominant-baseline', 'middle');
                text.setAttribute('fill', 'white');
                text.setAttribute('font-size', fontSize);
                text.setAttribute('font-weight', 'bold');
                text.setAttribute('text-shadow', '2px 2px 4px rgba(0,0,0,0.8)');
                text.setAttribute('class', 'wheel-number-text');
                text.textContent = participant.number;

                svg.appendChild(path);
                svg.appendChild(text);
            });

            wheelContainer.appendChild(svg);
            console.log('Ruleta creada con', availableNumbers.length, 'segmentos');
        }

        // Función para obtener el valor del color
        function getColorValue(colorName) {
            const colors = {
                yellow: 'url(#yellowGradient)',
                red: 'url(#redGradient)',
                blue: 'url(#blueGradient)',
                purple: 'url(#purpleGradient)',
                green: 'url(#greenGradient)',
                orange: 'url(#orangeGradient)'
            };
            return colors[colorName] || '#666';
        }

        // Función para resetear la ruleta
        function resetWheel() {
            const wheelContainer = document.getElementById('wheel-container');
            const svg = wheelContainer.querySelector('svg');
            if (svg) {
                svg.style.transition = 'none';
                svg.style.transform = 'rotate(-90deg)';
                // Forzar reflow
                svg.offsetHeight;
                svg.style.transition = '';
            }
        }

        // Función para refrescar participantes
        function refreshParticipants() {
            // Obtener números que ya han ganado
            const wonNumbers = winners.map(w => w.number);

            // Filtrar participantes que no han ganado
            availableNumbers = participants.filter(p => !wonNumbers.includes(p.number));

            console.log('Participantes refrescados:', availableNumbers.length);
            console.log('Números ganados:', wonNumbers);

            // Actualizar contador
            participantCount.textContent = availableNumbers.length;

            // Actualizar la lista de números
            createNumbersList();
        }

        // Función para crear la lista de números
        function createNumbersList() {
            if (!numbersGrid) return;

            numbersGrid.innerHTML = '';

            availableNumbers.forEach(participant => {
                const numberDiv = document.createElement('div');
                numberDiv.className = 'bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg p-2 text-center text-sm font-semibold text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors';
                numberDiv.textContent = participant.number;
                numberDiv.title = `Número ${participant.number} - ${participant.participant_name}`;
                numberDiv.dataset.number = participant.number;
                numberDiv.dataset.participant = participant.participant_name;
                numbersGrid.appendChild(numberDiv);
            });
        }

        // Función para mostrar vista de ruleta
        function showWheelView() {
            wheelContainer.parentElement.classList.remove('hidden');
            listView.classList.add('hidden');
            wheelViewBtn.classList.remove('bg-gray-600');
            wheelViewBtn.classList.add('bg-blue-600');
            listViewBtn.classList.remove('bg-blue-600');
            listViewBtn.classList.add('bg-gray-600');
        }

        // Función para mostrar vista de lista
        function showListView() {
            wheelContainer.parentElement.classList.add('hidden');
            listView.classList.remove('hidden');
            listViewBtn.classList.remove('bg-gray-600');
            listViewBtn.classList.add('bg-blue-600');
            wheelViewBtn.classList.remove('bg-blue-600');
            wheelViewBtn.classList.add('bg-gray-600');
        }

        // Función para destacar el ganador en la vista de lista
        function highlightWinnerInList(winnerNumber) {
            if (!numbersGrid) return;

            const numberElements = numbersGrid.querySelectorAll('[data-number]');
            numberElements.forEach(element => {
                if (element.dataset.number === winnerNumber.toString()) {
                    element.classList.remove('bg-white', 'dark:bg-gray-600', 'border-gray-300', 'dark:border-gray-500');
                    element.classList.add('bg-green-500', 'border-green-600', 'text-white', 'animate-pulse');
                    element.style.transform = 'scale(1.1)';
                    element.style.transition = 'all 0.3s ease';
                }
            });
        }

        // Función para obtener texto ordinal
        function getOrdinalText(num) {
            if (num === 1) return '1er';
            if (num === 2) return '2do';
            if (num === 3) return '3er';
            return num + 'to';
        }

        function startDraw() {
            // Verificar si la rifa está finalizada
            @if($raffle->status === 'finalizada')
            showAlert('Esta rifa ya ha sido finalizada. No se puede realizar más sorteos.', 'error');
            return;
            @endif

            if (availableNumbers.length === 0) {
                showAlert('No hay números disponibles para el sorteo', 'error');
                return;
            }

            if (currentPrizeIndex >= prizes.length) {
                showAlert('Todos los premios han sido sorteados', 'info');
                return;
            }

            isDrawing = true;
            startDrawBtn.classList.add('hidden');
            stopDrawBtn.classList.remove('hidden');
            statusText.textContent = 'Sorteando...';
            drawStatus.className = 'px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';

            // Resetear centro de la ruleta para nuevo sorteo
            currentNumber.textContent = '?';
            currentParticipant.textContent = 'Girando...';

            // Limpiar clases de efectos visuales
            const allSegments = document.querySelectorAll('.wheel-segment-svg');
            allSegments.forEach(segment => {
                segment.classList.remove('winner', 'loser', 'winner-segment');
            });

                        // Seleccionar ganador aleatorio con mejor aleatoriedad
            const timestamp = Date.now();
            const randomSeed = (timestamp % 1000000) / 1000000;
            const winnerIndex = Math.floor(randomSeed * availableNumbers.length);
            const winner = availableNumbers[winnerIndex];

            console.log('Números disponibles:', availableNumbers.length);
            console.log('Timestamp:', timestamp, 'Seed:', randomSeed);
            console.log('Ganador seleccionado:', winner.number, 'Índice:', winnerIndex);

            // Calcular ángulo de parada para que el ganador se detenga en el indicador
            const anglePerSegment = 360 / availableNumbers.length;
            const winnerAngle = winnerIndex * anglePerSegment;
            const stopAngle = 360 - winnerAngle; // El indicador está en la parte superior (0 grados)

            // Iniciar animación de la ruleta
            const wheelContainer = document.getElementById('wheel-container');
            const svg = wheelContainer.querySelector('svg');

            if (svg) {
                // Obtener la rotación actual
                const currentTransform = svg.style.transform;
                let currentRotation = 0;

                if (currentTransform) {
                    const match = currentTransform.match(/rotate\(([^)]+)deg\)/);
                    if (match) {
                        currentRotation = parseFloat(match[1]) || 0;
                    }
                }

                // Configurar la animación
                const additionalRotation = 360 * 5 + stopAngle; // 5 vueltas completas + ángulo de parada
                const newRotation = currentRotation + additionalRotation;
                const duration = 4000; // 4 segundos

                console.log('Rotación actual:', currentRotation, 'Nueva rotación:', newRotation, 'Ganador:', winner.number);

                svg.style.transition = `transform ${duration}ms cubic-bezier(0.25, 0.46, 0.45, 0.94)`;
                svg.style.transform = `rotate(-90deg) rotate(${newRotation}deg)`;

                console.log('Animación de ruleta iniciada');

                // Detener automáticamente después de la duración
                setTimeout(() => {
                    stopDraw();
                }, duration + 500);
            }
        }

        function stopDraw() {
            isDrawing = false;
            clearInterval(drawInterval);

            // Detener la animación de la ruleta
            const wheelContainer = document.getElementById('wheel-container');
            const svg = wheelContainer.querySelector('svg');
            if (svg) {
                svg.style.transition = '';
            }

            stopDrawBtn.classList.add('hidden');
            nextPrizeBtn.classList.remove('hidden');
            statusText.textContent = '¡Ganador seleccionado!';
            drawStatus.className = 'px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';

            // Usar el ganador predefinido del sorteo (no recalcular)
            const winnerIndex = Math.floor(Math.random() * availableNumbers.length);
            const winner = availableNumbers[winnerIndex];

            console.log('Ganador final:', winner.number);

            // Eliminar el número ganado de la lista disponible (SOLO UNA VEZ)
            availableNumbers.splice(winnerIndex, 1);
            console.log('Números restantes:', availableNumbers.length);

            // Efecto visual de celebración en la ruleta
            const winnerSegmentElement = document.querySelector(`[data-number="${winner.number}"]`);
            if (winnerSegmentElement) {
                winnerSegmentElement.classList.add('winner-segment', 'winner');
            }

            // Efecto visual: solo el ganador brilla
            const allSegments = document.querySelectorAll('.wheel-segment-svg');
            allSegments.forEach(segment => {
                if (segment.dataset.number !== winner.number) {
                    // Mantener colores originales, solo agregar clase para tracking
                    segment.classList.add('loser');
                }
            });

            // Efecto visual de celebración en el centro
            currentNumber.style.transform = 'scale(1.2)';
            currentNumber.style.transition = 'transform 0.3s ease';
            setTimeout(() => {
                currentNumber.style.transform = 'scale(1)';
            }, 300);

            // Mostrar ganador con efecto de destello
            currentNumber.textContent = winner.number;
            currentParticipant.textContent = winner.participant_name;
            currentNumber.style.color = '#10B981'; // Verde para el ganador
            currentNumber.classList.add('winner-glow');

            // Destacar ganador en la vista de lista
            highlightWinnerInList(winner.number);

            // Guardar ganador
            winners.push({
                number: winner.number,
                participant_name: winner.participant_name,
                participant_phone: winner.participant_phone,
                participant_email: winner.participant_email,
                prize: prizes[currentPrizeIndex]
            });

            // Guardar ganador en la base de datos
            saveWinnerToDatabase(winner, prizes[currentPrizeIndex]);

            // Mostrar modal de felicitaciones automáticamente
            setTimeout(() => {
                showCongratulations(winner, prizes[currentPrizeIndex]);
            }, 1000); // Mostrar modal después de 1 segundo

            // Actualizar contador de participantes
            participantCount.textContent = availableNumbers.length;

            // Actualizar tarjeta del premio
            updatePrizeCard(winner, prizes[currentPrizeIndex]);

            // Restaurar color después de 3 segundos
            setTimeout(() => {
                currentNumber.style.color = '';
                currentNumber.classList.remove('winner-glow');

                // Limpiar clases de efectos visuales
                const allSegments = document.querySelectorAll('.wheel-segment-svg');
                allSegments.forEach(segment => {
                    segment.classList.remove('winner', 'loser', 'winner-segment');
                });
            }, 3000);
        }

        function nextPrize() {
            currentPrizeIndex++;
            updatePrizeInfo();
            updateProgress();

            // Verificar si es el último premio
            if (currentPrizeIndex >= prizes.length) {
                // Sorteo completado
                statusText.textContent = '¡Sorteo completado!';
                drawStatus.className = 'px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                nextPrizeBtn.classList.add('hidden');
                startDrawBtn.classList.add('hidden');
                currentNumber.textContent = '✓';
                currentParticipant.textContent = 'Todos los premios han sido sorteados';

                // Finalizar la rifa cuando se complete el último premio
                finishRaffle();
                return;
            }

            // Refrescar participantes y recrear ruleta para el siguiente premio
            refreshParticipants();
            resetWheel();
            createWheel();

            // Resetear el centro de la ruleta
            currentNumber.textContent = '?';
            const ordinalText = getOrdinalText(currentPrizeIndex + 1);
            currentParticipant.textContent = `Esperando ${ordinalText} ganador...`;

            nextPrizeBtn.classList.add('hidden');
            startDrawBtn.classList.remove('hidden');
            statusText.textContent = 'Listo para el siguiente premio';
            drawStatus.className = 'px-4 py-2 rounded-full text-sm font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
        }

        function saveWinnerToDatabase(winner, prize) {
            fetch(`/raffle/${raffle.id}/save-winner`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    number: winner.number,
                    participant_name: winner.participant_name,
                    participant_phone: winner.participant_phone,
                    participant_email: winner.participant_email,
                    prize_id: prize.id
                })
            })
            .then(async (response) => {
                const contentType = response.headers.get('content-type') || '';
                let body = null;
                if (contentType.includes('application/json')) {
                    body = await response.json();
                } else {
                    const text = await response.text();
                    throw new Error(`Respuesta no válida del servidor: ${text.substring(0, 120)}...`);
                }

                if (!response.ok) {
                    const msg = body?.message || 'Error al guardar ganador';
                    throw new Error(msg);
                }

                console.log('Ganador guardado en BD:', body);
            })
            .catch(error => {
                console.error('Error al guardar ganador:', error);
                showAlert('Error al guardar ganador en la base de datos: ' + error.message, 'error');
            });
        }

        function finishRaffle() {
            fetch(`/raffle/${raffle.id}/finish`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'same-origin'
            })
            .then(async (response) => {
                const contentType = response.headers.get('content-type') || '';
                let body = null;
                if (contentType.includes('application/json')) {
                    body = await response.json();
                } else {
                    const text = await response.text();
                    throw new Error(`Respuesta no válida del servidor: ${text.substring(0, 120)}...`);
                }

                if (!response.ok) {
                    const msg = body?.message || 'Error al finalizar la rifa';
                    throw new Error(msg);
                }

                if (body.success) {
                    // Ocultar el botón de finalización manual
                    const manualFinishBtn = document.getElementById('manual-finish');
                    if (manualFinishBtn) {
                        manualFinishBtn.style.display = 'none';
                    }

                    showAlert('¡Rifa finalizada exitosamente! Ya no se pueden realizar más inscripciones.', 'success');
                } else {
                    throw new Error(body.message || 'Error al finalizar la rifa');
                }
            })
            .catch(error => {
                console.error('Error finishRaffle:', error);
                showAlert(error.message || 'Error al finalizar la rifa', 'error');
            });
        }

        function updatePrizeInfo() {
            if (currentPrizeIndex < prizes.length) {
                currentPrizeName.textContent = prizes[currentPrizeIndex].name;
                currentPrizeIndexElement.textContent = currentPrizeIndex + 1;
            } else {
                currentPrizeName.textContent = 'Sorteo completado';
                currentPrizeIndexElement.textContent = prizes.length;
            }
        }

        function updateProgress() {
            const progress = (currentPrizeIndex / prizes.length) * 100;
            progressBar.style.width = progress + '%';
        }

        function showCongratulations(winner, prize) {
            const modal = document.getElementById('congratulations-modal');
            const winnerNumber = document.getElementById('winner-number');
            const winnerName = document.getElementById('winner-name');
            const winnerPrize = document.getElementById('winner-prize');

            // Configurar contenido
            winnerNumber.textContent = winner.number;
            winnerName.textContent = winner.participant_name;
            winnerPrize.textContent = prize.name;

            // Mostrar modal con animación
            modal.classList.remove('hidden');
            modal.classList.add('animate-fadeIn');

            // Efecto de entrada para el número ganador
            winnerNumber.style.opacity = '0';
            winnerNumber.style.transform = 'scale(0.5)';

            setTimeout(() => {
                winnerNumber.style.transition = 'all 0.5s ease';
                winnerNumber.style.opacity = '1';
                winnerNumber.style.transform = 'scale(1)';
            }, 100);
        }

        function closeCongratulations() {
            document.getElementById('congratulations-modal').classList.add('hidden');
            updateWinnersList();
        }

        function updatePrizeCard(winner, prize) {
            const prizeCard = document.querySelector(`[data-prize-id="${prize.id}"]`);
            if (prizeCard) {
                const winnerElement = prizeCard.querySelector('.prize-winner');
                const winnerInfo = prizeCard.querySelector('.winner-info');

                winnerElement.classList.remove('hidden');
                winnerInfo.textContent = `Número ${winner.number} - ${winner.participant_name}`;

                prizeCard.classList.add('ring-2', 'ring-green-500');
            }
        }

        function updateWinnersList() {
            winnersList.innerHTML = '';
            winners.forEach((winner, index) => {
                const winnerElement = document.createElement('div');
                winnerElement.className = 'bg-gray-50 dark:bg-gray-700 rounded-lg p-4';
                winnerElement.innerHTML = `
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="font-semibold text-gray-900 dark:text-gray-100">${index + 1}. ${winner.prize.name}</span>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                Ganador: ${winner.participant_name} (Número ${winner.number})
                            </div>
                        </div>
                        <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                            ${winner.number}
                        </div>
                    </div>
                `;
                winnersList.appendChild(winnerElement);
            });
        }

        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ${
                type === 'error' ? 'bg-red-500 text-white' :
                type === 'success' ? 'bg-green-500 text-white' :
                'bg-blue-500 text-white'
            }`;
            alertDiv.textContent = message;

            document.body.appendChild(alertDiv);

            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }
    });
    </script>
    @endpush
</x-public-layout>

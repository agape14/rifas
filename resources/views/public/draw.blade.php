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
    </style>
    @endpush

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Información del sorteo -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold mb-4" style="color: {{ $raffle->theme_color ?? '#000' }}">
                    {{ $raffle->name }}
                </h1>
                <p class="text-xl text-gray-600 dark:text-gray-400 mb-6">
                    Fecha del sorteo: {{ \Carbon\Carbon::parse($raffle->draw_date)->format('d/m/Y H:i') }}
                </p>

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
                    <span id="draw-status" class="px-4 py-2 rounded-full text-sm font-semibold">
                        <span id="status-text">Listo para comenzar</span>
                    </span>
                </div>
            </div>

            <!-- Premios -->
            @if($raffle->prizes->count() > 0)
                <div class="mb-8">
                    <h3 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-6 text-center">Premios</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($raffle->prizes as $prize)
                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg border-2 border-transparent prize-card" data-prize-id="{{ $prize->id }}">
                                @if($prize->image)
                                    <img src="{{ asset('storage/' . $prize->image) }}" class="w-full h-48 object-cover" alt="{{ $prize->name }}">
                                @endif
                                <div class="p-4">
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
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-8 mb-8">
                <div class="text-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">Ruleta del Sorteo</h3>
                    <p class="text-gray-600 dark:text-gray-400">Números participantes: <span id="participant-count">{{ $raffle->numbers->where('status', 'pagado')->count() }}</span></p>
                </div>

                <!-- Número actual en la ruleta -->
                <div class="text-center mb-8">
                    <div id="current-number" class="text-8xl font-bold text-indigo-600 dark:text-indigo-400 mb-4">
                        ?
                    </div>
                    <div id="current-participant" class="text-xl text-gray-600 dark:text-gray-400">
                        Esperando sorteo...
                    </div>
                </div>

                <!-- Controles del sorteo -->
                <div class="flex justify-center space-x-4 mb-6">
                    <button id="start-draw" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors duration-200">
                        Iniciar Sorteo
                    </button>
                    <button id="stop-draw" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition-colors duration-200 hidden">
                        Detener Sorteo
                    </button>
                    <button id="next-prize" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-colors duration-200 hidden">
                        Siguiente Premio
                    </button>
                </div>

                <!-- Progreso del sorteo -->
                <div class="mb-6">
                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
                        <span>Premio actual: <span id="current-prize-name">-</span></span>
                        <span><span id="current-prize-index">0</span> de {{ $raffle->prizes->count() }}</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div id="progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>
            </div>

            <!-- Historial de ganadores -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Ganadores</h3>
                <div id="winners-list" class="space-y-3">
                    <!-- Los ganadores se mostrarán aquí dinámicamente -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de felicitaciones -->
    <div id="congratulations-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-8 max-w-md mx-4 text-center">
            <div class="mb-6">
                <div class="text-6xl mb-4">🎉</div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">¡Felicitaciones!</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">Número ganador:</p>
                <div id="winner-number" class="text-4xl font-bold text-indigo-600 dark:text-indigo-400 mb-2"></div>
                <div id="winner-name" class="text-xl text-gray-700 dark:text-gray-300 mb-4"></div>
                <div id="winner-prize" class="text-lg font-semibold text-green-600 dark:text-green-400"></div>
            </div>
            <button id="close-congratulations" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg transition-colors duration-200">
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
        const drawStatus = document.getElementById('draw-status');
        const participantCount = document.getElementById('participant-count');
        const winnersList = document.getElementById('winners-list');

        // Inicializar
        updatePrizeInfo();
        updateProgress();

        // Event listeners
        startDrawBtn.addEventListener('click', startDraw);
        stopDrawBtn.addEventListener('click', stopDraw);
        nextPrizeBtn.addEventListener('click', nextPrize);
        document.getElementById('close-congratulations').addEventListener('click', closeCongratulations);

        // Deshabilitar controles si la rifa está finalizada
        @if($raffle->status === 'finalizada')
        startDrawBtn.disabled = true;
        startDrawBtn.classList.add('opacity-50', 'cursor-not-allowed');
        startDrawBtn.textContent = 'Sorteo Finalizado';
        @endif

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

            // Iniciar animación de la ruleta con velocidad variable
            let speed = 50; // Velocidad inicial rápida
            let duration = 3000; // Duración total de la animación (3 segundos)
            let startTime = Date.now();

            // Agregar efecto de rotación
            currentNumber.classList.add('wheel-spin');

            drawInterval = setInterval(() => {
                const elapsed = Date.now() - startTime;
                const progress = elapsed / duration;

                // Aumentar la velocidad gradualmente para crear efecto de desaceleración
                speed = 50 + (progress * 200);

                const randomIndex = Math.floor(Math.random() * availableNumbers.length);
                const randomNumber = availableNumbers[randomIndex];

                currentNumber.textContent = randomNumber.number;
                currentParticipant.textContent = randomNumber.participant_name;

                // Detener automáticamente después de la duración especificada
                if (elapsed >= duration) {
                    clearInterval(drawInterval);
                    currentNumber.classList.remove('wheel-spin');
                    setTimeout(() => {
                        stopDraw();
                    }, 500); // Pequeña pausa antes de mostrar el ganador
                }
            }, speed);
        }

        function stopDraw() {
            isDrawing = false;
            clearInterval(drawInterval);

            stopDrawBtn.classList.add('hidden');
            nextPrizeBtn.classList.remove('hidden');
            statusText.textContent = '¡Ganador seleccionado!';
            drawStatus.className = 'px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';

            // Seleccionar ganador
            const winnerIndex = Math.floor(Math.random() * availableNumbers.length);
            const winner = availableNumbers[winnerIndex];

            // Efecto visual de celebración
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

            // Guardar ganador
            winners.push({
                number: winner.number,
                participant_name: winner.participant_name,
                participant_phone: winner.participant_phone,
                participant_email: winner.participant_email,
                prize: prizes[currentPrizeIndex]
            });

            // Mostrar modal de felicitaciones automáticamente
            setTimeout(() => {
                showCongratulations(winner, prizes[currentPrizeIndex]);
            }, 1000); // Mostrar modal después de 1 segundo

            // Remover número del array disponible
            availableNumbers.splice(winnerIndex, 1);
            participantCount.textContent = availableNumbers.length;

            // Actualizar tarjeta del premio
            updatePrizeCard(winner, prizes[currentPrizeIndex]);

            // Restaurar color después de 3 segundos
            setTimeout(() => {
                currentNumber.style.color = '';
                currentNumber.classList.remove('winner-glow');
            }, 3000);
        }

        function nextPrize() {
            currentPrizeIndex++;
            updatePrizeInfo();
            updateProgress();

            nextPrizeBtn.classList.add('hidden');
            startDrawBtn.classList.remove('hidden');
            statusText.textContent = 'Listo para el siguiente premio';
            drawStatus.className = 'px-4 py-2 rounded-full text-sm font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';

            if (currentPrizeIndex >= prizes.length) {
                statusText.textContent = 'Sorteo completado';
                drawStatus.className = 'px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                nextPrizeBtn.classList.add('hidden');
                startDrawBtn.classList.add('hidden');

                // Finalizar la rifa cuando se complete el último premio
                finishRaffle();
            }
        }

        function finishRaffle() {
            fetch(`/raffle/${raffle.id}/finish`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('¡Rifa finalizada exitosamente! Ya no se pueden realizar más inscripciones.', 'success');
                } else {
                    showAlert('Error al finalizar la rifa: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error al finalizar la rifa', 'error');
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
            document.getElementById('winner-number').textContent = winner.number;
            document.getElementById('winner-name').textContent = winner.participant_name;
            document.getElementById('winner-prize').textContent = prize.name;
            document.getElementById('congratulations-modal').classList.remove('hidden');
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

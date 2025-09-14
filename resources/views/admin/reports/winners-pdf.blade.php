<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ganadores - {{ $raffle->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            margin: 0 0 10px 0;
            color: #2563eb;
        }

        .header h2 {
            font-size: 18px;
            margin: 0 0 5px 0;
            color: #666;
        }

        .info-section {
            margin-bottom: 25px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: bold;
            color: #555;
        }

        .info-value {
            color: #333;
        }

        .stats-grid {
            margin-bottom: 25px;
        }

        .stat-card {
            background-color: #f1f5f9;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            border-left: 4px solid #2563eb;
            display: inline-block;
            width: 22%;
            margin: 0 1%;
            vertical-align: top;
        }

        .stat-number {
            font-size: 20px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
        }

        .winners-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .winners-table th,
        .winners-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .winners-table th {
            background-color: #2563eb;
            color: white;
            font-weight: bold;
            text-align: center;
        }

        .winners-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .winners-table tr:hover {
            background-color: #f5f5f5;
        }

        .winner-number {
            font-weight: bold;
            color: #2563eb;
            text-align: center;
        }

        .prize-name {
            font-weight: bold;
            color: #059669;
        }

        .participant-info {
            font-size: 11px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }

        .no-winners {
            text-align: center;
            color: #666;
            font-style: italic;
            margin: 40px 0;
        }

        .date-range {
            background-color: #e0f2fe;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
            color: #0277bd;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Ganadores</h1>
        <h2>{{ $raffle->name }}</h2>
        <p>Generado el {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Rifa:</span>
            <span class="info-value">{{ $raffle->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Fecha del Sorteo:</span>
            <span class="info-value">{{ \Carbon\Carbon::parse($raffle->draw_date)->format('d/m/Y H:i') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Total de Números:</span>
            <span class="info-value">{{ $raffle->numbers()->count() }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Total de Premios:</span>
            <span class="info-value">{{ $raffle->prizes()->count() }}</span>
        </div>
    </div>

    @if($dateFrom || $dateTo)
        <div class="date-range">
            @if($dateFrom && $dateTo)
                Período: {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}
            @elseif($dateFrom)
                Desde: {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }}
            @elseif($dateTo)
                Hasta: {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}
            @endif
        </div>
    @endif

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number">{{ $winners->count() }}</div>
            <div class="stat-label">Total Ganadores</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $winners->pluck('prize_id')->unique()->count() }}</div>
            <div class="stat-label">Premios Únicos</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $winners->pluck('participant_id')->unique()->count() }}</div>
            <div class="stat-label">Participantes Únicos</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $winners->max('drawn_at') ? $winners->max('drawn_at')->format('d/m') : 'N/A' }}</div>
            <div class="stat-label">Último Sorteo</div>
        </div>
    </div>

    @if($winners->count() > 0)
        <table class="winners-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Número Ganador</th>
                    <th>Participante</th>
                    <th>Contacto</th>
                    <th>Premio</th>
                    <th>Fecha de Sorteo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($winners as $index => $winner)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="winner-number">{{ $winner->number }}</td>
                        <td>
                            <strong>{{ $winner->participant->name }}</strong>
                        </td>
                        <td class="participant-info">
                            @if($winner->participant->phone)
                                📞 {{ $winner->participant->phone }}<br>
                            @endif
                            @if($winner->participant->email)
                                ✉️ {{ $winner->participant->email }}
                            @endif
                        </td>
                        <td class="prize-name">{{ $winner->prize->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($winner->drawn_at)->format('d/m/Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-winners">
            <h3>No se encontraron ganadores en el período seleccionado</h3>
            <p>No hay registros de ganadores que coincidan con los filtros aplicados.</p>
        </div>
    @endif

    <div class="footer">
        <p>Este reporte fue generado automáticamente por el Sistema de Rifas</p>
        <p>Fecha de generación: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>

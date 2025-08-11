<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Propuesta Comercial - Plataforma de Rifas y Sorteos</title>
    <style>
        @page { margin: 24px; }
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 12px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .brand { font-size: 18px; font-weight: 700; color: #111827; }
        .date { font-size: 11px; color: #6b7280; }
        .badge { display: inline-block; padding: 4px 8px; background: #312e81; color: #fff; border-radius: 6px; font-size: 11px; }
        .card { border: 1px solid #e5e7eb; border-radius: 8px; padding: 14px; margin-bottom: 12px; }
        .title { font-size: 14px; font-weight: 700; margin: 0 0 8px; color: #111827; }
        .muted { color: #6b7280; }
        ul { margin: 6px 0 0 16px; }
        li { margin: 4px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { background: #111827; color: #fff; padding: 8px; font-size: 12px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #e5e7eb; font-size: 12px; }
        tr:nth-child(even) td { background: #f9fafb; }
        .chip { padding: 3px 8px; background: #e0e7ff; color: #3730a3; border-radius: 9999px; font-size: 11px; }
        .footer { margin-top: 16px; font-size: 10px; color: #6b7280; }
        .accent { color: #2563eb; }
        .highlight { background: #ecfeff; border: 1px solid #a5f3fc; border-radius: 6px; padding: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="brand">Propuesta Comercial</div>
            <div class="muted">Plataforma de Rifas y Sorteos</div>
        </div>
        <div class="date">Fecha: {{ $date }}</div>
    </div>

    <div class="card">
        <div class="title">Resumen de características</div>
        <ul>
            @foreach($features as $f)
                <li>{{ $f }}</li>
            @endforeach
        </ul>
    </div>

    <div class="card">
        <div class="title">Tarifario sugerido <span class="badge">S/.</span></div>
        <table>
            <thead>
                <tr>
                    <th>Tipo de Cliente</th>
                    <th>Tamaño del Sorteo</th>
                    <th>Venta Estimada</th>
                    <th>Costo Fijo</th>
                    <th>Comisión</th>
                    <th>Servicios Opcionales</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pricing as $row)
                    <tr>
                        <td>{{ $row['segment'] }}</td>
                        <td><span class="chip">{{ $row['size'] }}</span></td>
                        <td>S/. {{ number_format($row['estimate'], 0) }}</td>
                        <td>S/. {{ number_format($row['fixed'], 0) }}</td>
                        <td>{{ $row['fee_pct'] }}% (S/. {{ number_format($row['estimate'] * $row['fee_pct'] / 100, 0) }})</td>
                        <td>{{ $row['optional'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="highlight" style="margin-top:8px;">
            Nota: Los precios son referenciales y pueden ajustarse según alcance, plazos y requerimientos (branding, campañas, landing, pagos, etc.).
        </div>
    </div>

    <div class="footer">
        © {{ date('Y') }} Plataforma de Rifas. Documento generado automáticamente.
    </div>
</body>
</html>

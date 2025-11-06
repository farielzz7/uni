<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura {{ $factura->numero_factura }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #1f2937;
            line-height: 1.5;
            font-size: 12px;
        }
        .header, .footer {
            text-align: center;
            margin-bottom: 20px;
        }
        .section {
            margin-bottom: 18px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f3f4f6;
        }
        .totals td {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Factura {{ $factura->numero_factura }}</h1>
        <p>Fecha de emisión: {{ \Carbon\Carbon::parse($factura->fecha_emision)->format('d/m/Y') }}</p>
    </div>

    <div class="section">
        <h2>Datos del cliente</h2>
        <p><strong>Nombre:</strong> {{ $factura->nombre_cliente }}</p>
        @if($factura->rfc_cliente)
            <p><strong>RFC:</strong> {{ $factura->rfc_cliente }}</p>
        @endif
        @if($factura->direccion_cliente)
            <p><strong>Dirección:</strong> {{ $factura->direccion_cliente }}</p>
        @endif
    </div>

    <div class="section">
        <h2>Detalle del pago</h2>
        <table>
            <thead>
                <tr>
                    <th>Concepto</th>
                    <th>Subtotal</th>
                    <th>IVA</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        Paquete: {{ optional(optional($factura->pago)->paquete)->nombre ?? 'N/A' }}<br>
                        Turista: {{ optional(optional($factura->pago)->turista)->nombre ?? 'N/A' }}
                    </td>
                    <td>${{ number_format($factura->subtotal, 2) }}</td>
                    <td>${{ number_format($factura->iva, 2) }}</td>
                    <td>${{ number_format($factura->total, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Reglas de viaje</h2>
        <p>{{ $reglasViaje }}</p>
    </div>

    <div class="section">
        <h2>Sugerencia de itinerario</h2>
        <p>{{ $sugerenciaItinerario }}</p>
    </div>

    <div class="footer">
        <p>Gracias por confiar en nosotros.</p>
    </div>
</body>
</html>


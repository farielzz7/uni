<!DOCTYPE html>
<html>
<head>
    <title>Contrato de Viaje</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #333; }
        .section { margin-bottom: 20px; }
        .section h2 { color: #555; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 10px; }
        .details p { margin: 5px 0; }
        .footer { text-align: center; margin-top: 50px; font-size: 0.9em; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Contrato de Viaje con {{ $empresa_nombre }}</h1>
        <p>{{ $empresa_direccion }} | {{ $empresa_telefono }} | {{ $empresa_email }}</p>
    </div>

    <div class="section">
        <h2>Información del Turista</h2>
        <div class="details">
            <p><strong>Nombre:</strong> {{ $turista->nombre }} {{ $turista->apellido }}</p>
            <p><strong>Email:</strong> {{ $turista->user->email }}</p>
            <p><strong>Nacionalidad:</strong> {{ $turista->nacionalidad }}</p>
            <p><strong>Edad:</strong> {{ $turista->edad }}</p>
            <p><strong>Teléfono:</strong> {{ $turista->telefono }}</p>
        </div>
    </div>

    <div class="section">
        <h2>Detalles del Paquete</h2>
        <div class="details">
            <p><strong>Nombre del Paquete:</strong> {{ $paquete->nombre }}</p>
            <p><strong>Descripción:</strong> {{ $paquete->descripcion }}</p>
            <p><strong>Precio:</strong> {{ number_format($paquete->precio, 2) }} MXN</p>
            <p><strong>Duración:</strong> {{ $paquete->duracion_dias }} días</p>
            <p><strong>Fechas:</strong> {{ \Carbon\Carbon::parse($paquete->fecha_inicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($paquete->fecha_fin)->format('d/m/Y') }}</p>
            <p><strong>Tipo de Paquete:</strong> {{ $paquete->tipoPaquete->nombre ?? 'N/A' }}</p>
            <p><strong>Destino:</strong> {{ $paquete->destino->nombre ?? 'N/A' }}</p>
        </div>
    </div>

    <div class="section">
        <h2>Términos y Condiciones</h2>
        <p>Este contrato establece los términos y condiciones del viaje contratado con {{ $empresa_nombre }}.</p>
        <ul>
            <li>El pago total del paquete debe realizarse antes de la fecha de inicio del viaje.</li>
            <li>Cualquier cancelación o modificación está sujeta a las políticas de cancelación de {{ $empresa_nombre }}.</li>
            <li>Es responsabilidad del turista contar con la documentación necesaria para el viaje (pasaporte, visas, etc.).</li>
            <li>{{ $empresa_nombre }} no se hace responsable por cambios en itinerarios debido a causas de fuerza mayor.</li>
            <li>Se recomienda adquirir un seguro de viaje.</li>
        </ul>
    </div>

    <div class="section">
        <h2>Sugerencia de Itinerario</h2>
        <p>Este es un itinerario sugerido y puede ser modificado según las preferencias y disponibilidad.</p>
        <p><strong>Día 1:</strong> Llegada al destino, traslado al hotel, check-in y tiempo libre.</p>
        <p><strong>Día 2:</strong> Tour por la ciudad, visita a sitios históricos y culturales.</p>
        <p><strong>Día 3:</strong> Actividad de aventura (ej. senderismo, buceo) o día de playa.</p>
        <p><strong>Día 4:</strong> Exploración de la gastronomía local y compras.</p>
        <p><strong>Día 5:</strong> Día libre o excursión opcional, cena de despedida.</p>
        <p><strong>D 6:</strong> Check-out del hotel y traslado al aeropuerto para el vuelo de regreso.</p>
    </div>

    <div class="footer">
        <p>Fecha del Contrato: {{ $fecha_contrato }}</p>
        <p>Firma del Turista: _________________________</p>
        <p>Firma de {{ $empresa_nombre }}: _________________________</p>
    </div>
</body>
</html>
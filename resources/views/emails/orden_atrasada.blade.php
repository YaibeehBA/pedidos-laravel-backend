<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu orden #{{ $orden->id }} está atrasada</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7fafc;
            margin: 0;
            padding: 0;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #dc3545;
            /* Rojo para indicar alerta */
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }

        .content {
            padding: 30px;
            color: #4a5568;
        }

        .content h2 {
            font-size: 20px;
            margin-bottom: 15px;
            color: #2d3748;
        }

        .content p {
            font-size: 16px;
            line-height: 1.6;
            margin: 10px 0;
        }

        .button {
            display: inline-block;
            margin: 20px 0;
            padding: 12px 24px;
            font-size: 16px;
            color: #ffffff;
            background-color: #dc3545;
            border-radius: 6px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #c82333;
        }

        .footer {
            text-align: center;
            padding: 20px;
            background-color: #f7fafc;
            color: #718096;
            font-size: 14px;
        }

        .footer a {
            color: #dc3545;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Encabezado -->
        <div class="header">
            <h1>⚠️ Tu orden #{{ $orden->id }} está atrasada</h1>
        </div>

        <!-- Contenido -->
        <div class="content">
            <h2>Hola {{ strtoupper($orden->usuario->nombre) }} {{ strtoupper($orden->usuario->apellido) }},</h2>

            <p>Lamentamos informarte que tu orden <strong>#{{ $orden->id }}</strong> ha sufrido un retraso en la entrega. Sabemos lo importante que es para ti recibir tu pedido a tiempo, y nos disculpamos sinceramente por este inconveniente.</p>

            <p><strong>Fecha estimada de entrega:</strong> {{ Carbon\Carbon::parse($orden->fecha_entrega)->format('j/n/Y') }}</p>

            <p>En <strong>New Blessings</strong>, estamos trabajando arduamente para resolver esta situación y asegurarnos de que tu pedido llegue lo antes posible. Valoramos tu paciencia y comprensión durante este proceso.</p>

            <!-- Botón de acción -->
            <a href="{{ url(env('FRONTEND_URL') . '/Pedidos') }}" class="button" style="color: white;">Ver el estado de mi pedido</a>

            <p>Si tienes alguna pregunta o necesitas asistencia, no dudes en contactarnos. Estamos aquí para ayudarte.</p>
        </div>

        <!-- Pie de página -->
        <div class="footer">
            <p>© {{ date('Y') }} <strong>New Blessings</strong>. Todos los derechos reservados.</p>
            <p><a href="{{ url(env('FRONTEND_URL')) }}">Visita nuestro sitio web</a></p>
        </div>
    </div>
</body>

</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Confirmado - Orden #{{ $orden->id }}</title>
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
            background-color: #28a745; /* Verde para indicar éxito */
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
            background-color: #28a745;
            border-radius: 6px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .button:hover {
            background-color: #218838;
        }
        .footer {
            text-align: center;
            padding: 20px;
            background-color: #f7fafc;
            color: #718096;
            font-size: 14px;
        }
        .footer a {
            color: #28a745;
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
            <h1>✅ Pago Confirmado - Orden #{{ $orden->id }}</h1>
        </div>

        <!-- Contenido -->
        <div class="content">
            <h2>Hola {{ $orden->usuario->nombre }} {{ $orden->usuario->apellido }},</h2>
            
            <p>Nos complace informarte que el pago de tu orden <strong>#{{ $orden->id }}</strong> ha sido confirmado exitosamente. ¡Gracias por confiar en <strong>New Blessings</strong>!</p>

            <p>En este momento, estamos preparando tu pedido con mucho cuidado para asegurarnos de que llegue en perfectas condiciones. Te mantendremos informado sobre el estado de tu entrega.</p>

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
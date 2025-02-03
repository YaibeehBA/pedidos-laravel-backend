<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación de Pedido Atrasado</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f1f1f1;
            color: #333;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            background-color: #f76b1c;
            padding: 15px;
            border-radius: 10px 10px 0 0;
            color: white;
        }

        .header h1 {
            font-size: 28px;
            margin: 0;
        }

        .body-content {
            padding: 20px;
            text-align: center;
        }

        .body-content p {
            font-size: 16px;
            line-height: 1.6;
            margin: 10px 0;
        }

        .cta-button {
            background-color: #f76b1c;
            color: white;
            padding: 15px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-size: 18px;
            font-weight: bold;
            display: inline-block;
            margin-top: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
        }

        .cta-button:hover {
            background-color: #d85a17;
            transform: scale(1.05);
            transition: all 0.3s ease-in-out;
        }

        .footer {
            text-align: center;
            padding: 20px;
            font-size: 14px;
            color: #777;
        }

        .footer a {
            color: #f76b1c;
            text-decoration: none;
        }

        .pattern {
            width: 100%;
            height: 15px;
            background: url('https://i.pinimg.com/736x/46/6a/01/466a01d030dd30ecb2656e7f33b303b4.jpg') repeat-x;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Encabezado -->
        <div class="header">
            <h1>Notificación de Pedido Atrasado</h1>
        </div>

        <!-- Contenido principal -->
        <div class="body-content">
            <p>Hola {{ $nombreUsuario }},</p>
            <p>Lamentamos informarte que tu pedido <strong>#{{ $orden_id }}</strong> se ha atrasado.</p>
            <p>La nueva fecha estimada de entrega es el <strong>{{ $fecha }}</strong>.</p>
            <p>Gracias por tu comprensión y paciencia.</p>
            <a href="{{ $urlPedidos }}" class="cta-button">Ver detalles del pedido</a>
        </div>

        <!-- Pie de página -->
        <div class="footer">
            <p>Si tienes alguna pregunta, no dudes en contactarnos.</p>
            <p>Visita nuestra tienda: <a href="http://localhost:5173/Login" target="_blank">New Blessing</a></p>
        </div>

        <!-- Detalle decorativo -->
        <div class="pattern"></div>
    </div>
</body>
</html>
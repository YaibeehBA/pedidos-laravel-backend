<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperación de Contraseña</title>
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

        /* Añadir detalles indígenas en patrones */
        .pattern {
            width: 100%;
            height: 15px;
            background: url('https://i.pinimg.com/736x/46/6a/01/466a01d030dd30ecb2656e7f33b303b4.jpg') repeat-x;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>¡Recupera tu Cuenta en New Blessing!</h1>
        </div>
        
        <div class="body-content">
            <p>¡Hola!</p>
            <p>Has solicitado restablecer tu contraseña para acceder a la tienda New Blessing. Si fue tú, solo haz clic en el botón abajo para crear una nueva contraseña.</p>

            <a href="{{ url('/reset-password?token=' . $token) }}" class="cta-button">Restablecer Contraseña</a>
            
            <p>Si no solicitaste este cambio, puedes ignorar este mensaje.</p>
        </div>
        
        <div class="footer">
            <p>Gracias por confiar en New Blessing, donde vestimos con tradición y estilo.</p>
            <p>Visita nuestra tienda para encontrar más.</p>
            <p><a href="http://localhost:5173/Login" target="_blank">New Blessing</a></p>

        </div>

        <div class="pattern"></div>
    </div>
</body>
</html>

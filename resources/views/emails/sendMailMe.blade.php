<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo mensaje de cliente: {{ $datosCorreo['name'] }}</title>
    <style>
        /* Estilos principales */
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background-color: #f1faf9;
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .header {
            background-color: #379aa3;
            padding: 30px 20px;
            text-align: center;
        }

        .logo {
            color: white;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .content {
            padding: 30px;
        }

        h1 {
            color: #379aa3;
            margin-top: 0;
            font-size: 24px;
        }

        p {
            margin-bottom: 20px;
            font-size: 16px;
        }

        .highlight {
            background-color: #daf3f3;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #59b9c0;
        }

        .footer {
            background-color: #f8fafc;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #666;
        }

        .social-icons {
            margin: 20px 0;
        }

        .social-icon {
            display: inline-block;
            margin: 0 10px;
        }

        /* Estilos responsivos */
        @media only screen and (max-width: 600px) {
            .container {
                border-radius: 0;
            }

            .content {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Encabezado -->
        <div class="header">
            <div class="logo">
                HanaByte <span style="margin-left: 8px;">花</span>
            </div>
            <p style="color: #daf3f3; margin: 0;">Donde la tecnología florece naturalmente</p>
        </div>

        <!-- Contenido principal -->
        <div class="content">
            <h1>Nuevo mensaje de cliente: {{ $datosCorreo['name'] }}</h1>

            <p>Hola,</p>

            <p>Has recibido un nuevo mensaje de <strong>{{ $datosCorreo['name'] }}</strong>.</p>
            <p>Correo cliente:<strong>{{ $datosCorreo['email'] }}</strong></p>

            <div class="highlight">
                <p><strong>Asunto:</strong> {{ $datosCorreo['asunto'] }}</p>
            </div>

            <p>El cliente ha solicitado asistencia con el siguiente asunto. Es importante que respondas a esta consulta
                a la mayor brevedad posible.</p>

            <p>Recuerda que si necesitas más detalles o deseas responder directamente, puedes hacerlo desde tu bandeja
                de entrada.</p>

            <p>Estamos aquí para ayudarte a gestionar las solicitudes de nuestros clientes de manera eficiente.</p>

            <div style="text-align: center;">
                <a href="https://hanabyte.vercel.app/" class="button">Visita nuestra plataforma</a>
            </div>

            <p>Gracias por tu compromiso en atender a nuestros clientes.</p>

            <p>Con cariño,</p>
            <p><strong>El equipo HanaByte</strong></p>

            <div class="social-icons">
                <p>Síguenos en nuestras redes:</p>
                <a href="[FACEBOOK_URL]" class="social-icon">
                    <img src="https://cdn-icons-png.flaticon.com/512/124/124010.png" alt="Facebook" width="24">
                </a>
                <a href="[INSTAGRAM_URL]" class="social-icon">
                    <img src="https://cdn-icons-png.flaticon.com/512/2111/2111463.png" alt="Instagram" width="24">
                </a>
                <a href="[GITHUB_URL]" class="social-icon">
                    <img src="https://cdn-icons-png.flaticon.com/512/25/25231.png" alt="GitHub" width="24">
                </a>
            </div>
        </div>

        <!-- Pie de página -->
        <div class="footer">
            <p>© 2025 HanaByte. Todos los derechos reservados.</p>
            <p>Ciudad de México, México</p>
            <p>
                <a href="[UNSUBSCRIBE_URL]" style="color: #379aa3;">Cancelar suscripción</a> |
                <a href="[PRIVACY_URL]" style="color: #379aa3;">Política de privacidad</a>
            </p>
        </div>
    </div>
</body>

</html>
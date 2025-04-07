<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a HanaByte</title>
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

        .button {
            display: inline-block;
            background-color: #379aa3;
            color: white !important;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
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
            <h1>¡Bienvenido a la familia HanaByte!</h1>

            <p>Hola {{$datosCorreo['name']}},</p>

            <p>Es un honor para nosotros darte la bienvenida a HanaByte, donde creamos experiencias digitales que
                florecen con elegancia y funcionalidad, como las flores de cerezo en primavera.</p>

            <div class="highlight">
                <p>En HanaByte, creemos en la <strong>belleza de lo simple</strong>, la <strong>fuerza de lo
                        auténtico</strong> y el <strong>poder de las conexiones humanas</strong> a través de la
                    tecnología.</p>
            </div>

            <p>Nuestro equipo está comprometido a cultivar soluciones digitales que se adapten perfectamente a tus
                necesidades, con el mismo cuidado y atención que dedicaríamos a un jardín.</p>

            <p>Estamos emocionados de comenzar este viaje contigo y ayudarte a hacer florecer tu presencia digital.</p>

            <div style="text-align: center;">
                <a href="https://hanabyte.vercel.app/" class="button">Explora tu nuevo espacio</a>
            </div>

            <p>Si tienes alguna pregunta o necesitas ayuda, no dudes en responder a este correo. Estamos aquí para
                ayudarte.</p>

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
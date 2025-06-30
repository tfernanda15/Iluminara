<?php
// ===============================================
// LÓGICA PHP PARA PROCESAR EL FORMULARIO DE CONTACTO
// (Se ejecuta al recibir una petición POST AJAX)
// ===============================================

// Verifica si la solicitud es POST y es una petición AJAX (XMLHttpRequest)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    // Establece el encabezado para indicar que la respuesta es JSON
    header('Content-Type: application/json');

    $response = ['success' => false, 'message' => ''];

    // =================================================================================
    // CONFIGURACIÓN DE LA BASE DE DATOS MySQL (¡VERIFICA Y REEMPLAZA ESTOS VALORES SI ES NECESARIO!)
    // =================================================================================
    $servername = "localhost"; // Generalmente 'localhost' para XAMPP/WAMP
    $username = "root";        // Tu nombre de usuario de MySQL (por defecto 'root' en XAMPP/WAMP)
    $password = "";            // Tu contraseña de MySQL (por defecto vacía en XAMPP/WAMP)
    $dbname = "iluminara_db";  // El nombre de la base de datos que ya tienes creada

    // Crea la conexión a la base de datos
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verifica si hay errores en la conexión a la base de datos
    if ($conn->connect_error) {
        // Registra el error en los logs del servidor para depuración, no lo muestres al usuario directamente
        error_log("Error de conexión a la base de datos: " . $conn->connect_error);
        $response['message'] = "Error de conexión con la base de datos. Por favor, inténtalo de nuevo más tarde.";
        echo json_encode($response);
        exit; // Termina la ejecución del script PHP
    }

    // ===============================================
    // Sanitiza y valida las entradas del formulario
    // ===============================================
    $name = isset($_POST['username']) ? htmlspecialchars(trim($_POST['username'])) : '';
    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
    $message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';

    // Validación básica de campos vacíos
    if (empty($name) || empty($email) || empty($message)) {
        $response['message'] = "Por favor, completa todos los campos para enviar tu pulso.";
        echo json_encode($response);
        $conn->close();
        exit;
    }

    // Validación de formato de email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = "Por favor, ingresa un formato de email válido.";
        echo json_encode($response);
        $conn->close();
        exit;
    }

    // ===============================================
    // Inserta los datos en la base de datos MySQL
    // ===============================================
    // Usamos sentencias preparadas para prevenir inyecciones SQL (¡MUY IMPORTANTE PARA LA SEGURIDAD!)
    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message, submission_time) VALUES (?, ?, ?, NOW())");
    
    // Verifica si la preparación de la consulta falló
    if ($stmt === false) {
        error_log("Error al preparar la consulta SQL: " . $conn->error);
        $response['message'] = "Error interno del sistema. Por favor, inténtalo de nuevo más tarde.";
        echo json_encode($response);
        $conn->close();
        exit;
    }

    // 'sss' indica que los tres parámetros son strings (cadenas de texto)
    $stmt->bind_param("sss", $name, $email, $message);

    $db_insert_success = false;
    if ($stmt->execute()) {
        $db_insert_success = true;
        $response['success'] = true;
        $response['message'] = "¡Tu pulso astral ha sido enviado con éxito!";
    } else {
        error_log("Error al guardar el mensaje en la base de datos: " . $stmt->error);
        $response['message'] = "Hubo un error al guardar tu pulso. Por favor, inténtalo de nuevo.";
    }

    $stmt->close();
    $conn->close(); // Cierra la conexión a la base de datos una vez que se ha usado

    // ===============================================
    // Lógica para enviar correos electrónicos
    // (Solo si la inserción en la DB fue exitosa)
    // ===============================================
    if ($db_insert_success) {
        // --- 1. Enviar correo a la empresa ---
        $company_email = "iluminaraofficial@gmail.com"; // ¡IMPORTANTE! Reemplaza esto con el correo de tu empresa
        $company_subject = "Nueva Sugerencia de Iluminara de: " . $name;
        $company_message = "Has recibido una nueva sugerencia a través del formulario de contacto:\n\n";
        $company_message .= "Nombre: " . $name . "\n";
        $company_message .= "Email: " . $email . "\n";
        $company_message .= "Mensaje: \n" . $message . "\n\n";
        $company_message .= "Hora de envío: " . date("Y-m-d H:i:s");
        
        $company_headers = "From: no-reply@iluminara.com\r\n"; // Remitente que verás en el correo de la empresa
        $company_headers .= "Reply-To: " . $email . "\r\n"; // Para poder responder directamente al usuario
        $company_headers .= "X-Mailer: PHP/" . phpversion();

        // Intenta enviar el correo a la empresa
        if (mail($company_email, $company_subject, $company_message, $company_headers)) {
            // No cambiamos el mensaje de éxito principal, pero podemos loguear
            error_log("Correo enviado a la empresa: " . $company_email);
        } else {
            error_log("Error al enviar correo a la empresa desde: " . $email . ". Detalles: " . error_get_last()['message']);
            // Puedes añadir esto al mensaje de respuesta si quieres, o solo loguearlo
            $response['message'] .= " Sin embargo, hubo un problema al notificar a la empresa por correo.";
        }

        // --- 2. Enviar respuesta automática al usuario ---
        $user_subject = "Confirmación: Tu Pulso Astral ha sido recibido en Iluminara";
        $user_message = "¡Hola " . $name . "!\n\n";
        $user_message .= "Gracias por contactar con Iluminara. Hemos recibido tu mensaje:\n\n";
        $user_message .= "Asunto: " . $message . "\n\n";
        $user_message .= "Un miembro de nuestro Oráculo revisará tu pulso astral y se pondrá en contacto contigo a la brevedad posible.\n\n";
        $user_message .= "Mientras tanto, te invitamos a explorar más sobre el Oráculo y sus misterios en nuestra página.\n\n";
        $user_message .= "Atentamente,\nEl Equipo de Iluminara\n";
        $user_message .= "Web: [Tu URL aquí, ej: http://localhost/iluminara/public/index.php]\n"; // ¡IMPORTANTE! Reemplaza con la URL real de tu sitio
        
        $user_headers = "From: iluminaraofficial@gmail.com\r\n"; // Correo que el usuario verá como remitente
        $user_headers .= "Reply-To: iluminaraofficial@gmail.com\r\n"; // Para que el usuario responda a esta dirección
        $user_headers .= "X-Mailer: PHP/" . phpversion();

        // Intenta enviar el correo de respuesta automática al usuario
        if (mail($email, $user_subject, $user_message, $user_headers)) {
            $response['message'] .= " Pronto recibirás una confirmación en tu correo.";
            error_log("Correo de respuesta automática enviado a: " . $email);
        } else {
            error_log("Error al enviar correo automático a: " . $email . ". Detalles: " . error_get_last()['message']);
            // Puedes añadir esto al mensaje de respuesta si quieres, o solo loguearlo
            $response['message'] .= " Sin embargo, hubo un problema al enviarte la confirmación por correo.";
        }
    }

    // Devuelve la respuesta JSON al cliente (navegador)
    echo json_encode($response);
    exit; // Termina la ejecución del script PHP
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto - Iluminara: Conecta con el Oráculo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@400;700;900&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        /* =============================================== */
        /* Variables de Color (exclusivas para esta página) */
        /* =============================================== */
        :root {
            --color-main-dark: #2a0050; /* Morado/Magenta Oscuro */
            --color-main-light: #c084fc; /* Morado Intenso */
            --color-accent-pink: #ff00ff; /* Rosa Fucsia Neón */
            --color-accent-yellow: #f2d325; /* Amarillo Neón Brillante (dorado sutil) */
            --color-accent-blue: #00ffff; /* Cian Neón */
            --color-magenta--fav: #de0064; /* Magenta Fuerte */
            
            /* Colores para el formulario y elementos inspirados en la imagen */
            --color-form-bg: rgba(78, 0, 134, 0.7); /* Fondo morado semitransparente para la tarjeta principal */
            --color-input-bg: #e0e0eb; /* Gris azulado claro para inputs */
            --color-input-border: #c0c0d8; /* Borde de input más oscuro */
            --color-submit-btn-bg: #8e2de2; /* Morado/magenta vibrante para el botón submit */
            --color-submit-btn-hover: #4a0070; /* Morado oscuro para hover */
            --color-social-icon-bg: #f0f0f5; /* Fondo de icono social claro */
            --color-social-icon-hover: #6a00a1; /* Morado intenso para hover */
            --color-text-form: #333333; /* Texto oscuro para el formulario */
            --color-text-placeholder: #888888; /* Texto placeholder */
            
            /* Colores RGB para sombras y transparencia */
            --color-main-dark-rgb: 42, 0, 80;
            --color-main-light-rgb: 192, 132, 252;
            --color-accent-pink-rgb: 255, 0, 255;
            --color-accent-yellow-rgb: 242, 211, 37;
            --color-accent-blue-rgb: 0, 255, 255;
            --color-magenta--fav-rgb: 222, 0, 100;
            --color-white-rgb: 255, 255, 255;
        }

        /* =============================================== */
        /* ESTILOS GLOBALES DE LA PÁGINA */
        /* =============================================== */
        body {
            margin: 0;
            font-family: 'Montserrat', sans-serif;
            color: var(--color-text-form); /* Color de texto predeterminado para la página */
            line-height: 1.6;
            overflow-x: hidden;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center; /* Centra el contenido verticalmente */
            align-items: center; /* Centra el contenido horizontalmente */

            /* Fondo de imagen mística */
            background-image: url('images/fondo.png'); /* ¡IMPORTANTE! Reemplazar con tu URL de imagen */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed; /* Mantiene el fondo fijo al hacer scroll */
        }

        /* Header Minimalista: Un solo botón creativo */
        .minimal-header {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 100;
        }

        .home-button {
            padding: 12px 25px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            color: white;
            background: linear-gradient(90deg, var(--color-submit-btn-bg), var(--color-magenta--fav)); /* Degradado morado/magenta */
            border: none;
            border-radius: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative; /* Cambiado de absolute para el botón flotante en general, pero lo mantengo fixed en el header */
        }

        .home-button:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 8px 20px rgba(0,0,0,0.4);
            background: linear-gradient(90deg, var(--color-magenta--fav), var(--color-submit-btn-bg)); /* Invertir degradado o tono más oscuro */
        }

        .home-button i {
            margin-right: 8px;
            font-size: 1.1rem;
            color: var(--color-accent-yellow); /* Pequeño toque dorado */
        }

        /* Contenedor principal del formulario */
        .contact-form-container {
            background-color: var(--color-form-bg); /* Fondo blanco casi puro de la tarjeta */
            border-radius: 40px; /* Bordes muy redondeados */
            padding: 50px;
            box-shadow: 
            0 0 3px #de0064,
            0 0 5px #d9e22b,
            0 0 20px #ff9100,
            0 0 30px #ffd500;
            display: flex;
            max-width: 900px; /* Ancho máximo de la tarjeta */
            width: 90%;
            overflow: hidden; /* Para elementos internos */
            position: relative;
            z-index: 10;
            border: 2px solid rgb(226, 147, 0); /* Borde sutil */
            height: 75vh;
            margin-top: 80px; /* Espacio para el header fijo */
            margin-bottom: 20px; /* Espacio inferior */

            /* Animación de entrada de la tarjeta */
            transform: scale(0.9);
            opacity: 1;
            animation: fadeInScale 0.8s ease-out forwards;
        }

        @keyframes fadeInScale {
            to { transform: scale(1); opacity: 1; }
        }

        /* Contenido izquierdo (Teléfono y detalles) */
        .contact-left {
            flex: 1;
            padding-right: 40px;
            display: flex;
            flex-direction: column;
            align-items: center; /* Centrar elementos */
            justify-content: center;
            position: relative;
        }

        .phone-icon-wrapper {
            position: relative;
            width: 200px; /* Tamaño del contenedor del icono de teléfono */
            height: 200px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--color-accent-blue), var(--color-main-light)); /* Degradado azul/morado */
            border-radius: 50%;
            box-shadow: 0 0 25px rgba(var(--color-accent-blue-rgb),0.5), 0 0 40px rgba(var(--color-main-light-rgb),0.3);
            animation: orbFloat 4s infinite ease-in-out alternate;
            overflow: hidden; /* Para contener la imagen del teléfono */
        }
        .phone-icon-wrapper img {
            width: 150%; /* Tamaño de la imagen del teléfono dentro del círculo */
            height: 150%;
            object-fit: contain;
            filter: drop-shadow(0 0 5px rgba(0,0,0,0.2));
        }

        @keyframes orbFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        /* Detalles de contacto (Número y Email) */
        .contact-details {
            text-align: center;
            margin-bottom: 40px;
        }
        .contact-details p {
            margin: 10px 0;
            font-size: 1.1rem;
            color: white;
            text-shadow: 0 0 2px #de0064,
            0 0 5px #d9e22b,
            0 0 10px #ffd500;
        }
        .contact-details a {
            color: white; /* Morado vibrante */
            text-decoration: none;
            transition: color 0.3s ease;
            text-shadow:none;
        }
        .contact-details a:hover {
            color: var(--color-main-dark);
            text-decoration: underline;
        }

        /* Redes Sociales */
        .social-links-contact {
            display: flex;
            justify-content: center;
            gap: 20px;
        }
        .social-links-contact a {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: var(--color-social-icon-bg); /* Fondo claro */
            color: rgb(226, 147, 0); /* Morado vibrante */
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .social-links-contact a:hover {
            background-color: rgba(255, 182, 193, 0.9); /* Morado intenso */
            color: white;
            transform: translateY(-5px) scale(1.1);
            box-shadow: 0 0 10px rgba(255, 182, 193, 0.9),
               0 0 20px rgba(255, 182, 193, 0.7),
               0 0 30px rgba(255, 182, 193, 0.5);
        }

        /* Contenido derecho (Formulario) */
        .contact-right {
            flex: 1;
        }

        .contact-right h2 {
            text-shadow: 0 0 10px #de0064,
               0 0 20px #de0064,
               0 0 30px rgba(255, 182, 193, 0.9);            
            font-family: 'Cinzel Decorative', serif;
            text-align: center;
            font-size: 2rem;
            font-weight: 700;
            color: white; /* Morado oscuro para el título del form */
            margin-top:-1.2rem;
            line-height: 1;
        }

        .form-group {
            margin-bottom: 20px;
            margin-top:-.2rem;
        }

        .form-group label {
            display: block;
            font-size: 0.9rem;
            color: white;
            margin-bottom: 8px;
            font-weight: 500;
            text-shadow: 0 0 2px #de0064,
            0 0 5px #d9e22b,
            0 0 10px #ffd500;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid rgb(226, 147, 0); /* Borde claro */
            background-color: var(--color-input-bg); /* Fondo de input gris azulado */
            border-radius: 10px; /* Bordes redondeados */
            font-size: 1rem;
            color: grey;
            transition: all 0.3s ease;
            resize: vertical;
            box-sizing: border-box; /* Incluir padding y border en el ancho/alto */
            box-shadow: 
            0 0 2px #de0064,
            0 0 5px #d9e22b,
            0 0 10px #ffd500;
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: var(--color-text-placeholder);
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--color-submit-btn-bg); /* Borde de enfoque morado */
            box-shadow: 0 0 0 3px rgba(var(--color-submit-btn-bg-rgb), 0.2); /* Sombra de enfoque */
        }

        .submit-btn {
            width: 100%;
            padding: 15px;
            font-size: 1.1rem;
            font-weight: 700;
            color: white;
            background: rgb(226, 147, 0); /* Morado vibrante */
            border: none;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 
            0 0 5px #de0064,
            0 0 5px #d9e22b,
            0 0 10px #ffd500;            
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            background: rgba(255, 182, 193, 0.9); /* Morado oscuro al hover */
            transform: translateY(-2px);
            box-shadow: 0 0 10px rgba(255, 182, 193, 0.9),
               0 0 20px rgba(255, 182, 193, 0.7),
               0 0 30px rgba(255, 182, 193, 0.5);
        }

        .submit-btn:disabled {
            background-color: #cccccc;
            box-shadow: none;
            cursor: not-allowed;
            opacity: 0.7;
        }

        .form-feedback-message {
            margin-top: 20px;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 0.95rem;
            text-align: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            color: var(--color-text-form);
        }

        .form-feedback-message.success {
            background-color: #d4edda; /* Verde claro */
            color: #155724; /* Verde oscuro */
            border: 1px solid #c3e6cb;
            opacity: 1;
        }
        .form-feedback-message.error {
            background-color: #f8d7da; /* Rojo claro */
            color: #721c24; /* Rojo oscuro */
            border: 1px solid #f5c6cb;
            opacity: 1;
        }

        /* =============================================== */
        /* Media Queries para Responsividad */
        /* =============================================== */
        @media (max-width: 992px) {
            .contact-form-container {
                flex-direction: column;
                padding: 40px;
                width: 95%;
                height: auto; /* Permite que el contenedor se ajuste a la altura en pantallas pequeñas */
            }
            .contact-left {
                padding-right: 0;
                margin-bottom: 40px;
                border-bottom: 1px solid rgba(var(--color-input-border), 0.5); /* Separador en móvil */
                padding-bottom: 40px;
            }
            .phone-icon-wrapper {
                width: 150px;
                height: 150px;
            }
            .contact-details p {
                font-size: 1rem;
            }
            .social-links-contact a {
                width: 45px;
                height: 45px;
                font-size: 1.6rem;
            }
            .contact-right h2 {
                font-size: 2rem;
                text-align: center;
            }
        }

        @media (max-width: 767px) {
            .contact-form-container {
                padding: 30px;
            }
            .contact-left {
                margin-bottom: 30px;
                padding-bottom: 30px;
            }
            .phone-icon-wrapper {
                width: 120px;
                height: 120px;
                margin-bottom: 20px;
            }
            .contact-details p {
                font-size: 0.95rem;
            }
            .social-links-contact {
                gap: 15px;
            }
            .social-links-contact a {
                width: 40px;
                height: 40px;
                font-size: 1.4rem;
            }
            .contact-right h2 {
                font-size: 1.8rem;
            }
            .form-group input,
            .form-group textarea {
                padding: 10px 12px;
                font-size: 0.95rem;
            }
            .submit-btn {
                padding: 12px;
                font-size: 1rem;
            }
            .minimal-header {
                top: 10px;
                left: 10px;
            }
            .home-button {
                padding: 10px 20px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <!-- HEADER Minimalista: Solo un botón creativo para ir al inicio -->
    <header class="minimal-header">
        <a href="index.php" class="home-button" aria-label="Volver al Inicio">
            <i class="fas fa-arrow-left"></i> Inicio
        </a>
    </header>

    <main class="contactanos-main-content">
        <div class="contact-form-container">
            <div class="contact-left">
                <div class="phone-icon-wrapper">
                    <!-- Imagen de teléfono flotante. ¡IMPORTANTE! Reemplazar con tu URL de imagen PNG transparente de teléfono -->
                    <img src="images/contactanos.png" alt="Icono de Teléfono">
                </div>
                <div class="contact-details">
                    <p>
                        <i class="fas fa-phone-alt"></i> Número: 
                        <a href="tel:+573207182402">+57 3207182402</a>
                    </p>
                    <p>
                        <i class="fas fa-envelope"></i> Correo: 
                        <a href="mailto:iluminaraofficial@gmail.com">iluminaraofficial@gmail.com</a>
                    </p>
                </div>
                <div class="social-links-contact">
                    <a href="#" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" target="_blank" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" target="_blank" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                    <!-- Puedes añadir más iconos si es necesario -->
                </div>
            </div>
            <div class="contact-right">
                <h2>Contacta con el Oráculo</h2>
                <form id="contactForm" method="POST">
                    <div class="form-group">
                        <label for="username">Tu Nombre Cósmico:</label>
                        <input type="text" id="username" name="username" placeholder="Usuario" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Tu Email Galáctico:</label>
                        <input type="email" id="email" name="email" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Tu Mensaje al Cosmos:</label>
                        <textarea id="message" name="message" rows="5" placeholder="Mensaje..." required></textarea>
                    </div>
                    <button type="submit" class="submit-btn">
                        Enviar Pulso Astral <i class="fas fa-arrow-right"></i>
                    </button>
                </form>
                <div id="formMessage" class="form-feedback-message"></div>
            </div>
        </div>
    </main>

    <!-- AOS Animate On Scroll Library JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            offset: 50
        });

        document.getElementById('contactForm').addEventListener('submit', async function(event) {
            event.preventDefault(); // Evita el envío tradicional del formulario

            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const message = document.getElementById('message').value.trim();
            const formMessage = document.getElementById('formMessage');
            const submitBtn = this.querySelector('.submit-btn');

            // Limpia mensajes anteriores y resetea clases
            formMessage.textContent = '';
            formMessage.className = 'form-feedback-message'; // Clase base
            formMessage.style.opacity = 0; // Oculta el mensaje inicialmente

            // Validaciones básicas de cliente (front-end)
            if (!username || !email || !message) {
                formMessage.textContent = 'Por favor, completa todos los campos para enviar tu pulso.';
                formMessage.classList.add('error');
                formMessage.style.opacity = 1;
                return;
            }

            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                formMessage.textContent = 'Por favor, ingresa un formato de email válido.';
                formMessage.classList.add('error');
                formMessage.style.opacity = 1;
                return;
            }

            // Deshabilita el botón y muestra spinner
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Enviando... <i class="fas fa-spinner fa-spin"></i>';

            // Crea un objeto FormData para enviar los datos del formulario
            const formData = new FormData();
            formData.append('username', username);
            formData.append('email', email);
            formData.append('message', message);

            try {
                // Envía la petición AJAX al mismo archivo PHP
                const response = await fetch('contactanos.php', { 
                    method: 'POST',
                    headers: {
                        // Indica al servidor que es una petición AJAX
                        'X-Requested-With': 'XMLHttpRequest' 
                    },
                    body: formData
                });

                const result = await response.json(); // Espera la respuesta JSON del PHP

                if (result.success) {
                    formMessage.textContent = result.message;
                    formMessage.classList.add('success');
                    this.reset(); // Limpia el formulario solo si el envío fue exitoso
                } else {
                    formMessage.textContent = result.message;
                    formMessage.classList.add('error');
                }
            } catch (error) {
                console.error("Error al enviar el formulario:", error);
                formMessage.textContent = 'Hubo un error inesperado al conectar con el Oráculo. Por favor, inténtalo de nuevo más tarde.';
                formMessage.classList.add('error');
            } finally {
                formMessage.style.opacity = 1; // Muestra el mensaje de feedback (ya sea éxito o error)
                submitBtn.disabled = false; // Habilita el botón
                submitBtn.innerHTML = 'Enviar Pulso Astral <i class="fas fa-arrow-right"></i>'; // Restaura el texto del botón
            }
        });
    </script>
</body>
</html>

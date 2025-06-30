<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nosotros - Iluminara: Tu Santuario Místico</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/nosotros.css">
    <!-- AOS Animate On Scroll Library (para animaciones al hacer scroll) -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Google Fonts: Playfair Display para títulos, Montserrat para texto general -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@400;700;900&display=swap" rel="stylesheet">


    
</head>
<body>


<div class="stars">
            <script>
document.addEventListener('DOMContentLoaded', function() {
    const starsContainer = document.querySelector('.stars');
    const numberOfStars = 50; // Número total de estrellas

    if (starsContainer) {
        for (let i = 0; i < numberOfStars; i++) {
            let stars = document.createElement('i');
            stars.classList.add('fas', 'fa-star'); // Icono de estrella de Font Awesome

            // Tamaños aleatorios
            let size = Math.random() * 2 + 0.5 + 'rem'; // Entre 0.5rem y 2.5rem
            stars.style.fontSize = size;

            // Posiciones aleatorias en la pantalla
            stars.style.left = Math.random() * 100 + 'vw'; // Posición horizontal aleatoria
            stars.style.animationDelay = Math.random() * 8 + 's'; // Retraso aleatorio
            stars.style.animationDuration = Math.random() * 8 + 8 + 's'; // Duración aleatoria

            starsContainer.appendChild(stars);
        }
    }
});
            </script>
        </div>
    <!-- HEADER de la página (Estilos independientes) -->
    <header class="page-header">

        <nav class="minimal-nav">
                <a href="index.php#productGrid" class="btn btn-secondary back-to-products-btn">
                    <i class="fas fa-arrow-left"></i> Volver a Productos
                </a>
            </nav>
      
    </header>

    <main class="nosotros-main-content">
        <section class="nosotros-hero-section">
            <h1 class="hero-title-main" data-aos="fade-down" style="font-family: 'Cinzel Decorative', serif; font-size: 3.5rem; text-shadow: 
            0 0 8px #de0064,
            0 0 15px #d9e22b,
            0 0 30px #ff9100,
            0 0 50px #ffd500; z-index: 2;">Ilumina Tu Camino en el Cosmos</h1>
            <p class="hero-phrase" data-aos="fade-down" data-aos-delay="200">
                En Iluminara, cada producto es un faro que te guía a través de las constelaciones de tu vida, revelando tu propia magia.
            </p>

            <div class="sky-elements-container">
                <!-- Nubes como en la imagen -->
                <div class="cloud-element cloud-1"></div>
                <div class="cloud-element cloud-2"></div>
                <div class="cloud-element cloud-3"></div>
                <div class="cloud-element cloud-4"></div> 

                <!-- Faro -->
                <!-- ¡IMPORTANTE! Sustituye esta URL por la de tu imagen de faro con fondo transparente -->
                <img src="images/faro.png" alt="Faro Místico de Iluminara" class="lighthouse-img" data-aos="fade-left" data-aos-delay="400">
                
                <!-- Detalles Místicos Flotantes -->
                <!-- ¡IMPORTANTE! Sustituye estas URLs por tus imágenes PNG transparentes de Hadas, Gatos, Fuentes, Cristales, etc. -->
                <div class="detail-element detail-fairy" style="background-image: url('images/Hada.png'); width: 400px; height: 400px; top:40%; left:-3rem;"></div>
                <div class="detail-element detail-cat" style="background-image: url('https://raw.githubusercontent.com/google/gemini-generative-models/main/images/assets/cat_placehold.png');"></div>
                <div class="detail-element detail-fountain" style="background-image: url('https://raw.githubusercontent.com/google/gemini-generative-models/main/images/assets/fountain_placehold.png');"></div>
                <div class="detail-element detail-crystal"></div> 
            </div>
        </section>

        <section class="content-blocks-section">
            <h2 class="block-header" data-aos="fade-up" style="font-family: 'Cinzel Decorative', serif;">Nuestra Misión y Visión de Luz</h2>
            
            <div class="content-block" data-aos="fade-right" data-aos-offset="150">
                <div class="block-text">
                    <h3 style="font-family: 'Cinzel Decorative', serif;">Nuestra Misión</h3>
                    <p>En Iluminara, nuestra misión es encender la chispa de la inspiración en cada persona, ofreciendo productos únicos que conecten con la magia de su ser interior y el vasto universo que nos rodea. Queremos que cada compra sea un paso más en tu viaje de autodescubrimiento y empoderamiento.</p>
                </div>
                <div class="block-circle">
                    <!-- ¡IMPORTANTE! Sustituye esta URL por tu imagen mística para la Misión (hada, portal, etc.) -->
                    <img src="images/sagittarius.png" alt="Esencia de nuestra Misión" >
                </div>
            </div>

            <div class="content-block" data-aos="fade-left" data-aos-offset="150">
                <div class="block-text">
                    <h3 style="font-family: 'Cinzel Decorative', serif;">Nuestra Visión</h3>
                    <p>Visualizamos un futuro donde la mística y la modernidad convergen, creando una comunidad vibrante donde cada alma encuentra su luz. Buscamos ser el faro que guía hacia el bienestar holístico y la conexión espiritual a través de la belleza y la energía de nuestros productos.</p>
                </div>
                <div class="block-circle">
                    <!-- ¡IMPORTANTE! Sustituye esta URL por tu imagen mística para la Visión (hada, estrella guía, etc.) -->
                    <img src="images/Mision.png" alt="Visión del Futuro de Iluminara"> 
                </div>
            </div>

            <div class="content-block" data-aos="fade-right" data-aos-offset="150">
                <div class="block-text">
                    <h3 style="font-family: 'Cinzel Decorative', serif;">Nuestros Valores</h3>
                    <p>Nos cimentamos en la autenticidad, la transparencia y el amor por lo esotérico. Creemos en la energía positiva, la innovación con propósito y el respeto profundo por cada camino individual. Estos valores son el latido de Iluminara y guían cada una de nuestras acciones.</p>
                </div>
                <div class="block-circle">
                    <!-- ¡IMPORTANTE! Sustituye esta URL por tu imagen mística para los Valores (cristal, símbolo ancestral, etc.) -->
                    <img src="images/arbol.png" alt="Valores Fundamentales de Iluminara">
                </div>
            </div>

            <div class="content-block" data-aos="fade-left" data-aos-offset="150">
                <div class="block-text">
                    <h3 style="font-family: 'Cinzel Decorative', serif;">Nuestra Historia</h3>
                    <p>Desde una chispa de inspiración en el corazón del cosmos, Iluminara ha crecido con el propósito de traer la magia a tu vida. Cada paso en nuestro camino ha sido guiado por la búsqueda de la belleza y la energía que reside en cada ser y cada objeto.</p>
                </div>
                <div class="block-circle">
                    <!-- ¡IMPORTANTE! Sustituye esta URL por tu imagen mística para la Historia (pergamino, constelación, etc.) -->
                    <img src="images/nuestrahistoria.png" alt="Historia de Iluminara">
                </div>
            </div>

        </section>

        <!-- Nueva Sección: Cuadros con Punta (como en la imagen) -->
        <section class="specialization-section">
            <h2 class="block-header" data-aos="fade-up" style="font-family: 'Cinzel Decorative', serif;">En qué nos Especializamos</h2>
            <div class="pointed-cards-grid">
                <div class="pointed-card" data-aos="zoom-in" data-aos-delay="100" data-aos-offset="100">
                    <div class="pointed-card-circle">
                        <!-- Imagen para Cartas Natales (tarot, astrología, símbolo) -->
                        <img src="images/carta.png" alt="Imagen Carta Natal">
                    </div>
                    <h3 class="pointed-card-title" style="font-family: 'Cinzel Decorative', serif;">Cartas Natales</h3>
                    <p class="pointed-card-description">Descubre los mapas estelares de tu nacimiento y desbloquea tu potencial único en este viaje astral.</p>
                </div>
                <div class="pointed-card" data-aos="zoom-in" data-aos-delay="300" data-aos-offset="100">
                    <div class="pointed-card-circle">
                        <!-- Imagen para Compatibilidad (manos, conexión, símbolos) -->
                        <img src="images/compatibilidad.png" alt="Imagen Compatibilidad">
                    </div>
                    <h3 class="pointed-card-title" style="font-family: 'Cinzel Decorative', serif;">Compatibilidad Cósmica</h3>
                    <p class="pointed-card-description">Explora las energías y alineaciones de tus relaciones a través de una conexión astral profunda.</p>
                </div>
                <div class="pointed-card" data-aos="zoom-in" data-aos-delay="500" data-aos-offset="100">
                    <div class="pointed-card-circle">
                        <!-- Imagen para Astrología Infantil (niño, estrella, futuro) -->
                        <img src="images/astrologia.png" alt="Imagen Astrología Infantil">
                    </div>
                    <h3 class="pointed-card-title" style="font-family: 'Cinzel Decorative', serif;">Astrología Infantil</h3>
                    <p class="pointed-card-description">Guía a las pequeñas estrellas a comprender su propósito y su destino desde el inicio de su viaje.</p>
                </div>
            </div>
        </section>

    </main>

    <!-- FOOTER de la página (Estilos independientes) -->
    <footer class="page-footer">
        <p>&copy; <?php echo date("Y"); ?> Iluminara. Todos los derechos reservados. Un viaje astral para tu alma.</p>
        <div class="social-links">
            <a href="#" target="_blank"><i class="fab fa-facebook-f"></i></a>
            <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>
            <a href="#" target="_blank"><i class="fab fa-twitter"></i></a>
        </div>
    </footer>

    <!-- AOS Animate On Scroll Library JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1200, 
            once: true,    
            offset: 80     
        });
    </script>
</body>
</html>

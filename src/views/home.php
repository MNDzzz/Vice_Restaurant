<div class="vice-landing">
    <style>
        .vice-landing {
            background-color: var(--color-bg);
            color: var(--color-text);
            width: 100%;
            max-width: 100% !important;
            height: auto !important;
        }

        /* ========================================
           Estilos Intro Animada
           ======================================== */

        /* Fondo inicial fijo */
        #hero-background-full {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            z-index: 40;
            /* Debajo de la máscara */
            /* Zoom inicial suave */
            transform: scale(1.15);
            transform-origin: center center;
            pointer-events: none;
        }

        #hero-background-full img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Máscara SVG */
        #logo-mask {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            z-index: 50;
            /* Aplica la forma del SVG */
            /* Fondo transparente */
            background: rgba(255, 255, 255, 0);
            mask-image: url('assets/img/common/vice-logo-mask.svg');
            -webkit-mask-image: url('assets/img/common/vice-logo-mask.svg');
            /* Posición inicial en la I */
            mask-position: 31.5% 53%;
            -webkit-mask-position: 31.5% 53%;
            mask-repeat: no-repeat;
            -webkit-mask-repeat: no-repeat;
            /* Tamaño inicial gigante */
            mask-size: 6000%;
            -webkit-mask-size: 6000%;
            pointer-events: none;
        }

        /* Imagen recortada */
        #hero-key {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            /* Zoom sincronizado */
            transform: scale(1.15);
            transform-origin: center center;
        }

        #hero-key-background {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Logo flotante */
        #hero-logo-overlay {
            position: fixed;
            top: 2rem;
            /* Alineado con menú */
            left: 50%;
            transform: translateX(-50%);
            z-index: 110;
            width: 350px;
            filter: drop-shadow(0 0 30px rgba(255, 0, 222, 0.8));
            pointer-events: none;
        }

        /* Texto ayuda */
        #hero-scroll-indicator {
            position: fixed;
            bottom: 8%;
            left: 50%;
            transform: translateX(-50%);
            z-index: 110;
            text-align: center;
            color: #fff;
            text-shadow: 0 0 10px rgba(0, 0, 0, 0.8);
            pointer-events: none;
            animation: bounce-pulse 2s ease-in-out infinite;
        }

        @keyframes bounce-pulse {

            0%,
            100% {
                transform: translateX(-50%) translateY(0);
                opacity: 0.8;
            }

            50% {
                transform: translateX(-50%) translateY(-8px);
                opacity: 1;
            }
        }

        #hero-scroll-indicator span {
            display: block;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 5px;
            font-weight: 500;
        }

        /* Espacio vacío para permitir scroll */
        .hero-scroll-spacer {
            height: 150vh;
            pointer-events: none;
        }

        /* ========================================
           Sección Principal
           ======================================== */
        .hero-logo-reveal-section {
            position: relative;
            width: 100%;
            min-height: 100vh;
            background: linear-gradient(180deg, #1C1829 0%, #0f0d1a 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
            overflow: hidden;
            z-index: 5;
        }

        .logo-large {
            max-width: 500px;
            width: 70%;
            height: auto;
            margin-bottom: 50px;
            filter: drop-shadow(0 0 40px rgba(255, 0, 222, 0.5));
            opacity: 0;
            transform: scale(0.7) translateY(-50px);
        }

        .hero-text-block {
            text-align: center;
            opacity: 0;
            transform: translateY(30px);
            background: linear-gradient(180deg, #00FFFF 0%, #FF00FF 33%, #FFAA00 66%, #D400FF 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .hero-title-main {
            font-family: var(--font-body);
            font-weight: 600;
            font-size: 2.5rem;
            color: inherit;
            margin: 0 0 10px 0;
            line-height: 1.3;
        }

        .hero-subtitle-main {
            font-family: var(--font-body);
            font-weight: 700;
            font-size: 3rem;
            background: linear-gradient(90deg, #00FFFF, #FF00FF, #FFAA00, #D400FF);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            line-height: 1.2;
            margin-bottom: 40px;
        }

        .hero-buttons {
            display: flex;
            gap: 20px;
            margin-top: 20px;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
        }

        .btn-pill-white {
            background: #FFF;
            color: #000;
            border-radius: 50px;
            padding: 14px 45px;
            font-weight: 700;
            font-size: 1rem;
            text-transform: uppercase;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease, background-color 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            z-index: 10;
            backface-visibility: hidden;
            display: inline-block;
        }

        .btn-pill-white:hover {
            transform: scale(1.08);
            background: #F5F5F5;
            box-shadow: 0 6px 20px rgba(255, 255, 255, 0.2);
        }

        /* Fondo Ciudad */
        .city-banner-section {
            width: 100%;
            height: 100vh;
            min-height: 600px;
            background: url('assets/img/home/city-banner.webp') no-repeat center center;
            background-size: cover;
            background-attachment: fixed;
            position: relative;
            z-index: 2;
            overflow: hidden;
        }

        /* Corte diagonal */
        .angled-section {
            position: relative;
            z-index: 3;
            background: var(--color-bg);
            /* Forma inclinada */
            clip-path: polygon(0 50px, 100% 0, 100% 100%, 0 100%);
            margin-top: -60px;
            padding-top: 100px;
            padding-bottom: 0px;
            overflow: hidden;
        }

        /* Estilos Mapa */
        .location-section {
            text-align: center;
            padding-bottom: 50px;
        }

        .section-header-text {
            font-family: var(--font-body);
            font-weight: 800;
            font-size: 1.5rem;
            letter-spacing: 2px;
            color: #FFB0C4;
            text-transform: uppercase;
            margin-bottom: 30px;
            display: inline-block;
            border-bottom: 2px solid transparent;
        }

        .map-full-width {
            width: 100%;
            height: auto;
            display: block;
        }

        /* Banner Personajes */
        .vibe-banner-section {
            width: 100%;
            height: 100vh;
            min-height: 700px;
            position: relative;
            margin-top: -5px;
            overflow: hidden;
        }

        .vibe-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            max-width: 100% !important;
        }

        /* Sección Redes */
        .posts-section-angled {
            position: relative;
            background: var(--color-bg);
            margin-top: -50px;
            padding-top: 100px;
            padding-bottom: 50px;
            clip-path: polygon(0 50px, 100% 0, 100% 100%, 0 100%);
            z-index: 4;
            overflow: hidden;
        }

        .posts-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 50px;
            flex-wrap: wrap;
        }

        .its-a-vice-neon {
            max-width: 300px;
            filter: drop-shadow(0 0 10px #FF00FF);
            transform: rotate(-10deg);
        }

        .posts-row {
            display: flex;
            gap: 15px;
        }

        .post-thumb {
            width: 150px;
            height: 180px;
            object-fit: cover;
            border: 2px solid #FFF;
            transform: rotate(2deg);
        }

        .post-thumb:nth-child(2) {
            transform: rotate(-2deg);
        }

        .post-thumb:nth-child(3) {
            transform: rotate(1deg);
        }

        /* Banner Eventos */
        .char-car-section {
            width: 100%;
            height: 100vh;
            min-height: 700px;
            margin-top: -50px;
            position: relative;
            z-index: 1;
            overflow: hidden;
        }

        .char-car-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            max-width: 100% !important;
        }

        /* Lista de Eventos */
        .eventos-section-angled {
            background: var(--color-bg);
            margin-top: -80px;
            padding-top: 120px;
            position: relative;
            z-index: 5;
            clip-path: polygon(0 50px, 100% 0, 100% 100%, 0 100%);
            padding-bottom: 80px;
            padding-bottom: 80px;
            text-align: center;
            overflow: hidden;
        }

        .events-grid {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
            margin-top: 40px;
        }

        .event-flyer {
            width: 400px;
            border-radius: 4px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            transition: transform 0.3s;
        }

        .event-flyer:hover {
            transform: translateY(-10px);
        }
    </style>

    <!-- Fondo inicial completo -->
    <div id="hero-background-full">
        <img src="assets/img/home/hero-building.webp" alt="Fondo Inicial">
    </div>

    <!-- Animación Intro -->
    <div id="logo-mask">
        <!-- Imagen recortada -->
        <div id="hero-key">
            <img id="hero-key-background" src="assets/img/home/hero-building.webp" alt="Vice Restaurant">
        </div>
    </div>

    <!-- Elementos flotantes -->
    <img id="hero-logo-overlay" src="assets/img/common/vice-logo.svg" alt="Vice Logo">
    <div id="hero-scroll-indicator">
        <span>Desliza hacia abajo</span>
    </div>

    <!-- Espaciador -->
    <div class="hero-scroll-spacer"></div>

    <!-- Contenido Principal -->
    <div class="hero-logo-reveal-section">
        <img src="assets/img/common/vice-logo.svg" alt="Vice Logo" class="logo-large">
        <div class="hero-text-block">
            <h2 class="hero-title-main">Un Paraíso Neón.</h2>
            <h1 class="hero-subtitle-main">Exclusivo y<br>Frente al Mar.</h1>
            <div class="hero-buttons">
                <a href="index.php?view=menu" class="btn-pill-white">Carta</a>
                <a href="index.php?view=pedir" class="btn-pill-white">Pedir</a>
            </div>
        </div>
    </div>

    <!-- Separador -->
    <div class="city-banner-section reveal-scroll"></div>

    <!-- Ubicación -->
    <div class="angled-section" id="ubicacion">
        <div class="location-section reveal-scroll">
            <h3 class="section-header-text">UBICACIÓN</h3>
            <div class="location-map-wrapper">
                <img src="assets/img/home/location-map.webp" alt="Map" class="map-full-width">
            </div>
        </div>
    </div>

    <!-- Promociones -->
    <div class="offers-section reveal-scroll">
        <div class="container py-5">
            <h3 class="section-header-text text-center w-100 mb-5">OFERTAS SEMANALES</h3>
            <div class="row g-4 justify-content-center">
                <!-- Oferta Lunes-Miércoles -->
                <div class="col-md-6">
                    <a href="index.php?view=menu" class="text-decoration-none">
                        <div class="offer-card chill-mode">
                            <div class="offer-content">
                                <span class="offer-badge mb-2">LUN - MIÉ (TODO EL DÍA)</span>
                                <h2 class="offer-title">CHILL WEEK</h2>
                                <p class="offer-desc">50% DTO. EN TODOS LOS COCKTAILS</p>
                                <span class="btn btn-outline-light mt-3">VER CARTA</span>
                            </div>
                        </div>
                    </a>
                </div>
                <!-- Oferta Jueves-Domingo -->
                <div class="col-md-6">
                    <a href="index.php?view=pedir" class="text-decoration-none">
                        <div class="offer-card party-mode">
                            <div class="offer-content">
                                <span class="offer-badge mb-2">JUE - DOM (20:00 - 23:00)</span>
                                <h2 class="offer-title">HAPPY WEEKEND</h2>
                                <p class="offer-desc">25% DTO. EN TU CENA + COCKTAIL</p>
                                <span class="btn btn-outline-light mt-3">HACER PEDIDO</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        .offers-section {
            background-color: var(--color-bg);
            padding-bottom: 80px;
            padding-bottom: 80px;
            position: relative;
            z-index: 3;
            overflow: hidden;
            /* Ocultar sobrantes */
        }

        .offer-card {
            border-radius: 15px;
            overflow: hidden;
            position: relative;
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .offer-card:hover {
            transform: translateY(-5px);
        }

        .chill-mode {
            background: linear-gradient(45deg, rgba(0, 255, 255, 0.1), rgba(0, 0, 139, 0.4));
            border-color: #0ff;
            box-shadow: 0 0 15px rgba(0, 255, 255, 0.3);
        }

        .chill-mode:hover {
            box-shadow: 0 0 30px rgba(0, 255, 255, 0.6);
        }

        .party-mode {
            background: linear-gradient(45deg, rgba(255, 0, 255, 0.1), rgba(139, 0, 139, 0.4));
            border-color: #f0f;
            box-shadow: 0 0 15px rgba(255, 0, 255, 0.3);
        }

        .party-mode:hover {
            box-shadow: 0 0 30px rgba(255, 0, 255, 0.6);
        }

        .offer-content {
            background: rgba(0, 0, 0, 0.6);
            padding: 30px;
            backdrop-filter: blur(5px);
            border-radius: 10px;
            width: 80%;
        }

        .offer-badge {
            display: inline-block;
            background: #fff;
            color: #000;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 800;
            font-size: 0.9rem;
        }

        .offer-title {
            font-family: var(--font-display, sans-serif);
            font-size: 2.5rem;
            color: #fff;
            text-transform: uppercase;
            margin: 10px 0;
            text-shadow: 0 0 10px currentColor;
        }

        .chill-mode .offer-title {
            color: #0ff;
            text-shadow: 0 0 10px #0ff;
        }

        .party-mode .offer-title {
            color: #f0f;
            text-shadow: 0 0 10px #f0f;
        }

        .offer-desc {
            color: #fff;
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 0;
        }
    </style>

    <!-- Banner Personajes -->
    <div class="vibe-banner-section reveal-scroll">
        <img src="assets/img/home/vibe-characters.webp" alt="Vibe" class="vibe-img">
    </div>

    <!-- Sección Redes -->
    <div class="posts-section-angled reveal-scroll" id="posts">
        <div class="posts-container">
            <div class="posts-left">
                <img src="assets/img/home/its-a-vice-square.webp" alt="#ItsAVice" class="its-a-vice-neon">
            </div>
            <div class="posts-right">
                <div class="section-header-text" style="display:block; text-align:left; margin-left: 10px;">POSTS</div>
                <div class="posts-row">
                    <!-- Ejemplos -->
                    <img src="assets/img/home/social-post-1.webp" class="post-thumb">
                    <img src="assets/img/home/social-post-1.webp" class="post-thumb">
                    <img src="assets/img/home/social-post-1.webp" class="post-thumb">
                </div>
            </div>
        </div>
    </div>

    <!-- Banner Eventos -->
    <div class="char-car-section reveal-scroll">
        <img src="assets/img/home/event-character.webp" alt="Character" class="char-car-img">
    </div>

    <!-- 7. EVENTOS -->
    <div class="eventos-section-angled reveal-scroll" id="eventos">
        <h3 class="section-header-text">EVENTOS</h3>
        <div class="events-grid">
            <img src="assets/img/home/event-flyer-1.webp" class="event-flyer">
            <img src="assets/img/home/event-flyer-2.webp" class="event-flyer">
        </div>
    </div>

</div>

<!-- ANIMACIONES CON GSAP -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        gsap.registerPlugin(ScrollTrigger);

        // ===== Animación Intro =====
        // Sincronizar con scroll
        const tl = gsap.timeline({
            scrollTrigger: {
                trigger: "body", // Usamos el body o el contenedor principal como trigger global
                start: "top top",
                end: "+=150%", // Coincide con la altura del spacer
                scrub: 1
                // markers: true // Usar para debug si es necesario
            }
        });

        // Animación doble capa

        // Calcular tamaño final máscara
        const finalMaskSize = window.innerWidth * 0.7 > 500 ? "500px" : "70vw";

        tl
            // 0. Ocultar fondo inicial
            .to("#hero-background-full", { opacity: 0, duration: 0.1, ease: "power1.out" })

            // 1. Alejar imágenes
            .to(["#hero-key", "#hero-background-full"], { scale: 1, duration: 1, ease: "power2.out" }, "<")

            // 2. Ocultar elementos flotantes
            .to(["#hero-logo-overlay", "#hero-scroll-indicator"], { opacity: 0, duration: 0.2 }, "<")

            // 3. Cerrar y centrar máscara
            // De la I al centro
            .to("#logo-mask", {
                maskSize: finalMaskSize,
                webkitMaskSize: finalMaskSize,
                maskPosition: "50% 50%",
                webkitMaskPosition: "50% 50%",
                duration: 1,
                ease: "power2.inOut"
            }, "<")

            // 4. Ocultar máscara
            .to("#logo-mask", {
                opacity: 0,
                display: "none",
                duration: 0.2,
                ease: "power1.out"
            })

            // Limpiar elementos
            .set(["#hero-logo-overlay", "#hero-scroll-indicator", "#hero-background-full"], { display: "none" });


        // ===== Logo Sección 2 =====
        gsap.to('.logo-large', {
            scrollTrigger: {
                trigger: '.hero-logo-reveal-section',
                start: "top 80%",
                end: "top 30%",
                scrub: 1,
            },
            opacity: 1,
            scale: 1,
            y: 0,
            ease: "power2.out"
        });

        // ===== Texto Sección 2 =====
        gsap.to('.hero-text-block', {
            scrollTrigger: {
                trigger: '.hero-logo-reveal-section',
                start: "top 60%",
                end: "top 20%",
                scrub: 1
            },
            opacity: 1,
            y: 0,
            ease: "power2.out"
        });

        // ===== Botones Sección 2 =====
        gsap.from('.hero-buttons a', {
            scrollTrigger: {
                trigger: '.hero-buttons',
                start: "top 80%",
                toggleActions: "play none none reverse"
            },
            opacity: 0,
            y: 20,
            scale: 0.95,
            duration: 0.6,
            stagger: 0.15,
            ease: "back.out(1.5)"
        });

        // ===== Parallax Ciudad =====
        gsap.to('.city-banner-section', {
            backgroundPosition: "50% 100%",
            ease: "none",
            scrollTrigger: {
                trigger: ".city-banner-section",
                start: "top bottom",
                end: "bottom top",
                scrub: 1
            }
        });

        // ===== Aparición Secciones =====
        // Animación subida
        gsap.utils.toArray('.reveal-scroll').forEach((section, index) => {
            gsap.from(section, {
                scrollTrigger: {
                    trigger: section,
                    start: "top 85%",
                    end: "top 50%",
                    toggleActions: "play none none reverse",
                    // markers: true // Descomentar para debugging
                },
                y: 80,
                opacity: 0,
                duration: 1.2,
                ease: "power3.out"
            });
        });

        // ===== Escalar Personajes =====
        gsap.utils.toArray(['.vibe-img', '.char-car-img']).forEach(img => {
            gsap.from(img, {
                scrollTrigger: {
                    trigger: img,
                    start: "top 80%",
                    end: "top 30%",
                    scrub: 1
                },
                scale: 1.1,
                ease: "none"
            });
        });

        // ===== Mapa Ubicación =====
        gsap.from('.map-full-width', {
            scrollTrigger: {
                trigger: '.map-full-width',
                start: "top 80%",
                toggleActions: "play none none reverse"
            },
            opacity: 0,
            scale: 0.95,
            duration: 1,
            ease: "power2.out"
        });

        // ===== Posts =====
        // Animación Logo Neón
        gsap.from('.its-a-vice-neon', {
            scrollTrigger: {
                trigger: '.its-a-vice-neon',
                start: "top 80%",
                toggleActions: "play none none reverse"
            },
            opacity: 0,
            rotation: -20,
            scale: 0.8,
            duration: 1.2,
            ease: "back.out(1.5)"
        });

        // Animación Posts
        gsap.from('.post-thumb', {
            scrollTrigger: {
                trigger: '.posts-row',
                start: "top 75%",
                toggleActions: "play none none reverse"
            },
            opacity: 0,
            y: 50,
            rotation: (index) => index % 2 === 0 ? -5 : 5,
            duration: 0.8,
            stagger: 0.2,
            ease: "power2.out"
        });

        // ===== Eventos =====
        // Animación Flyers
        gsap.from('.event-flyer', {
            scrollTrigger: {
                trigger: '.events-grid',
                start: "top 75%",
                toggleActions: "play none none reverse"
            },
            opacity: 0,
            scale: 0.9,
            y: 60,
            duration: 1,
            stagger: 0.25,
            ease: "power3.out"
        });

        // ===== Títulos =====
        // Animar encabezados
        gsap.utils.toArray('.section-header-text').forEach(header => {
            gsap.from(header, {
                scrollTrigger: {
                    trigger: header,
                    start: "top 85%",
                    toggleActions: "play none none reverse"
                },
                opacity: 0,
                x: -50,
                duration: 0.8,
                ease: "power2.out"
            });
        });

        // ===== Scroll Suave (Opcional) =====
        // Descomentar para activar
        /*
        ScrollTrigger.normalizeScroll(true);
        ScrollTrigger.config({
            autoRefreshEvents: "visibilitychange,DOMContentLoaded,load"
        });
        */

        console.log('Animaciones GSAP inicializadas');
    });
</script>
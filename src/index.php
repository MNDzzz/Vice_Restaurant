<?php
session_start();


// Routing simple para la aplicación
$view = isset($_GET['view']) ? $_GET['view'] : 'home';

// Whitelist de vistas permitidas
// Defino una lista blanca de vistas permitidas por seguridad
$allowed_views = ['home', 'menu', 'pedir', 'admin', 'login', 'register', 'carrito', 'checkout', 'mis-pedidos', 'perfil'];

if (!in_array($view, $allowed_views)) {
    $view = 'home';
}

// Verifico la seguridad para el acceso al panel de administración
if ($view === 'admin') {
    $userRole = $_SESSION['user_role'] ?? '';
    if ($userRole !== 'admin' && $userRole !== 'superadmin') {
        header('Location: index.php?view=login&error=unauthorized');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VICE - Miami Vibes</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <!-- GSAP -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
</head>

<body>

    <!-- Inicio la barra de navegación -->
    <!-- Configuro la barra de navegación transparente -->
    <nav class="vice-navbar">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <!-- Coloco el logo tipo script a la izquierda -->
            <a class="navbar-brand-script" href="index.php?view=home">
                <img src="assets/img/common/vice-logo.svg" alt="Vice">
            </a>

            <!-- Botón del menú "hamburguesa" -->
            <button class="nav-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#viceMenuSidebar">
                <div class="hamburger-line"></div>
                <div class="hamburger-line"></div>

            </button>
        </div>
    </nav>

    <!-- Implemento el menú lateral (Offcanvas) -->
    <div class="offcanvas offcanvas-end vice-sidebar" tabindex="-1" id="viceMenuSidebar">

        <!-- LOGO GRANDE EN EL FONDO (Izquierda) -->
        <div class="sidebar-backdrop-logo">
            <img src="assets/img/common/vice-logo.svg" alt="Vice Logo">
        </div>
        <div class="offcanvas-header">

            <!-- 1. Pestañas de navegación (Izquierda) -->
            <div class="sidebar-tabs-header">
                <button class="sidebar-tab active" data-tab="inicio">Inicio</button>
                <button class="sidebar-tab" data-tab="menu">Menu</button>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <button class="sidebar-tab" data-tab="pedir">Pedir</button>
                    <button class="sidebar-tab" data-tab="mis-pedidos">Mis Pedidos</button>
                <?php else: ?>
                    <button class="sidebar-tab" data-tab="login-required-pedir">Pedir</button>
                    <button class="sidebar-tab" data-tab="login-required-pedidos">Mis Pedidos</button>
                <?php endif; ?>
            </div>

            <!-- 2. Grupo Derecha: Iconos + Cerrar -->
            <div class="d-flex align-items-center gap-4">
                <!-- Iconos de usuario y carrito -->
                <div class="header-icons d-flex gap-4 align-items-center">
                    <a href="index.php?view=<?php echo isset($_SESSION['user_id']) ? 'perfil' : 'login'; ?>"
                        class="nav-icon-link">
                        <?php readfile('assets/icons/heroicons/outline/user.svg'); ?>
                    </a>
                    <a href="index.php?view=carrito" class="nav-icon-link position-relative">
                        <?php readfile('assets/icons/heroicons/outline/shopping-bag.svg'); ?>
                        <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                            <span
                                class="badge bg-primary rounded-circle position-absolute top-0 start-100 translate-middle p-1"
                                style="font-size:0.6rem;">
                                <?php echo count($_SESSION['cart']); ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </div>

                <!-- Botón de cierre -->
                <button type="button" class="btn-close-custom" data-bs-dismiss="offcanvas" aria-label="Close">
                    <?php readfile('assets/icons/heroicons/outline/x-mark.svg'); ?>
                </button>
            </div>
        </div>

        <div class="offcanvas-body d-flex flex-column">
            <!-- Defino el área de contenido principal del sidebar -->
            <div class="sidebar-content flex-grow-1 mt-5">
                <!-- Muestro el contenido de la pestaña Inicio -->
                <div class="tab-content active" data-content="inicio">
                    <a href="index.php?view=home#ubicacion" class="sidebar-link">Ubicación</a>
                    <a href="index.php?view=home#posts" class="sidebar-link">Posts</a>
                    <a href="index.php?view=home#eventos" class="sidebar-link">Eventos</a>
                </div>

                <!-- Muestro el contenido de la pestaña Menú -->
                <div class="tab-content" data-content="menu">
                    <a href="index.php?view=menu#entrantes" class="sidebar-link">Entrantes</a>
                    <a href="index.php?view=menu#principales" class="sidebar-link">Principales</a>
                    <a href="index.php?view=menu#cocktails" class="sidebar-link">Cocktails</a>
                    <a href="index.php?view=menu#extras" class="sidebar-link">Extras</a>
                </div>

                <!-- Muestro opciones de pedir si el usuario ha iniciado sesión -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="tab-content" data-content="pedir">
                        <p class="sidebar-redirect-msg">Redirigiendo a Pedir...</p>
                    </div>
                    <div class="tab-content" data-content="mis-pedidos">
                        <p class="sidebar-redirect-msg">Redirigiendo a Mis Pedidos...</p>
                    </div>
                <?php else: ?>
                    <div class="tab-content" data-content="login-required-pedir">
                        <p class="sidebar-redirect-msg">Redirigiendo a Login...</p>
                    </div>
                    <div class="tab-content" data-content="login-required-pedidos">
                        <p class="sidebar-redirect-msg">Redirigiendo a Login...</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="sidebar-footer">
                <!-- Fila 1: Botones (Izquierda) - Imagen (Derecha) -->
                <div class="d-flex w-100 justify-content-between align-items-center mb-5">

                    <!-- Columna 1: Botones Auth -->
                    <div class="footer-col-auth">
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <div class="sidebar-auth-buttons mb-0">
                                <a href="index.php?view=login" class="btn-sidebar-auth">Login</a>
                                <a href="index.php?view=register" class="btn-sidebar-auth">Registrarse</a>
                            </div>
                        <?php elseif ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'superadmin'): ?>
                            <div class="sidebar-auth-buttons mb-0">
                                <a href="index.php?view=admin" class="btn-sidebar-auth btn-admin">Panel Admin</a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Columna 2: Logo Vice (Derecha) -->
                    <div class="footer-col-logo text-end">
                        <img src="assets/img/common/its-a-vice.webp" alt="#ItsAVice" class="sidebar-neon-img">
                    </div>
                </div>

                <!-- Fila 2: Iconos (Izquierda) - Copyright (Centro Absoluto) -->
                <div class="d-flex w-100 position-relative align-items-center">
                    <!-- Iconos a la izquierda -->
                    <div class="sidebar-footer-icons mb-0" style="z-index: 2;">
                        <a href="#" class="footer-icon-small">
                            <?php readfile('assets/icons/heroicons/outline/phone.svg'); ?>
                        </a>
                        <a href="#" class="footer-icon-small">
                            <?php readfile('assets/icons/heroicons/outline/map-pin.svg'); ?>
                        </a>
                        <a href="#" class="footer-icon-small">
                            <?php readfile('assets/icons/heroicons/outline/globe-alt.svg'); ?>
                        </a>
                    </div>

                    <!-- Copyright centrado absolutamente -->
                    <div class="position-absolute w-100 text-center"
                        style="left: 0; top: 50%; transform: translateY(-50%); z-index: 1;">
                        <p class="sidebar-copyright m-0">© <?php echo date('Y'); ?> VICE Restaurant.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Renderizo el contenido principal de la página -->
    <main style="padding-top: <?php echo ($view === 'home') ? '0' : '80px'; ?>;">
        <?php
        $view_path = "views/{$view}.php";
        if (file_exists($view_path)) {
            include $view_path;
        } else {
            echo "<div class='container text-center mt-5'><h1>Error 404</h1><p>Página no encontrada.</p></div>";
        }
        ?>
    </main>

    <!-- Footer -->
    <footer>
        <div class="vice-footer-container">
            <!-- Rating a la izquierda -->
            <div class="footer-left">
                <img src="assets/img/common/rating-pending-footer.webp" alt="ESRB Rating" class="footer-rating-img">
            </div>

            <!-- Botones centrales y enlaces legales -->
            <div class="footer-center">
                <div class="footer-buttons">
                    <a href="index.php?view=menu" class="btn-pill-white">Carta</a>
                    <a href="index.php?view=pedir" class="btn-pill-white">Pedir</a>
                </div>
                <div class="footer-links">
                    <a href="#">Información corporativa</a>
                    <a href="#">Privacidad</a>
                    <a href="#">Ajustes de cookies</a>
                    <a href="#">Política de cookies</a>
                    <a href="#">Legal</a>
                    <a href="#">No vender ni compartir mis datos personales</a>
                </div>
            </div>

            <!-- Logo y iconos a la derecha -->
            <div class="footer-right">
                <img src="assets/img/home/its-a-vice-square.webp" alt="#ItsAVice" class="footer-neon-logo">
                <div class="footer-icons">
                    <!-- Iconos para teléfono, ubicación y web -->
                    <a href="#" class="footer-icon"><?php readfile('assets/icons/heroicons/outline/phone.svg'); ?></a>
                    <a href="#" class="footer-icon"><?php readfile('assets/icons/heroicons/outline/map-pin.svg'); ?></a>
                    <a href="#"
                        class="footer-icon"><?php readfile('assets/icons/heroicons/outline/globe-alt.svg'); ?></a>
                </div>
            </div>
        </div>
        <!-- Copyright -->
        <div class="footer-copy text-center">
            <p class="" style="font-size: 0.8rem; color:#FFF9CB;">&copy; <?php echo date('Y'); ?> VICE Restaurant. Miami
                Vibes Only.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Carrito -->
    <script src="assets/js/cart.js"></script>

    <!-- Inicializo las animaciones GSAP -->
    <script>
        document.addEventListener("DOMContentLoaded", (event) => {
            gsap.registerPlugin();

            // Animo los títulos H1
            gsap.from("h1", {
                duration: 1.2,
                y: 50,
                opacity: 0,
                ease: "power3.out",
                stagger: 0.2
            });

            // Animo las tarjetas para que aparezcan al hacer scroll
            gsap.utils.toArray('.card').forEach(card => {
                gsap.from(card, {
                    scrollTrigger: card,
                    duration: 0.8,
                    y: 30,
                    opacity: 0,
                    delay: 0.1
                });
            });
        });
    </script>
    <!-- Sidebar -->
    <script src="assets/js/sidebar.js"></script>
</body>

</html>
<?php
session_start();

// Defino el enrutamiento simple para la aplicación
$view = isset($_GET['view']) ? $_GET['view'] : 'home';

// Defino una lista blanca de vistas permitidas por seguridad
$allowed_views = ['home', 'menu', 'pedir', 'admin', 'login', 'register', 'carrito', 'checkout', 'mis-pedidos'];

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
        <div class="container-fluid d-flex justify-content-between align-items-center px-4 py-3">
            <!-- Coloco el logo tipo script a la izquierda -->
            <a class="navbar-brand-script" href="index.php?view=home">
                <img src="assets/img/common/vice-logo.png" alt="Vice" style="height: 40px;">
            </a>

            <!-- Añado los iconos a la derecha -->
            <div class="d-flex align-items-center gap-4">
                <!-- Muestro el icono del carrito de compras -->
                <a href="index.php?view=carrito" class="nav-icon-link position-relative">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                    </svg>
                    <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                        <span class="badge bg-primary rounded-circle position-absolute top-0 start-100 translate-middle p-1"
                            style="font-size:0.6rem;">
                            <?php echo count($_SESSION['cart']); ?>
                        </span>
                    <?php endif; ?>
                </a>

                <!-- Añado el botón para desplegar el menú -->
                <button class="nav-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#viceMenuSidebar">
                    <div class="hamburger-line"></div>
                    <div class="hamburger-line"></div>
                </button>
            </div>
        </div>
    </nav>

    <!-- Implemento el menú lateral (Offcanvas) -->
    <div class="offcanvas offcanvas-end vice-sidebar" tabindex="-1" id="viceMenuSidebar">
        <div class="offcanvas-header">
            <button type="button" class="btn-close-custom" data-bs-dismiss="offcanvas" aria-label="Close">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <div class="offcanvas-body d-flex flex-column">
            <!-- Muestro las pestañas de navegación superiores -->
            <div class="sidebar-tabs">
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

            <!-- Defino el área de contenido principal del sidebar -->
            <div class="sidebar-content flex-grow-1">
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

            <!-- Añado la sección inferior con botones de autenticación y logo -->
            <div class="sidebar-footer">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <div class="sidebar-auth-buttons">
                        <a href="index.php?view=login" class="btn-sidebar-auth">Login</a>
                        <a href="index.php?view=register" class="btn-sidebar-auth">Registrarse</a>
                    </div>
                <?php elseif ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'superadmin'): ?>
                    <div class="sidebar-auth-buttons">
                        <a href="index.php?view=admin" class="btn-sidebar-auth btn-admin">Panel Admin</a>
                    </div>
                <?php endif; ?>

                <!-- Muestro el logo en el sidebar -->
                <div class="sidebar-logo">
                    <img src="assets/img/common/its-a-vice.png" alt="#ItsAVice" class="sidebar-neon-img">
                </div>

                <!-- Añado iconos de pie de página y derechos de autor -->
                <div class="sidebar-footer-icons">
                    <a href="#" class="footer-icon-small">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path
                                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                            </path>
                        </svg>
                    </a>
                    <a href="#" class="footer-icon-small">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                    </a>
                    <a href="#" class="footer-icon-small">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="2" y1="12" x2="22" y2="12"></line>
                            <path
                                d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z">
                            </path>
                        </svg>
                    </a>
                </div>
                <p class="sidebar-copyright">© <?php echo date('Y'); ?> VICE Restaurant. Miami Vibes Only.</p>
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
                <img src="assets/img/common/rating-pending-footer.png" alt="ESRB Rating" class="footer-rating-img">
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
                <img src="assets/img/home/its-a-vice-square.png" alt="#ItsAVice" class="footer-neon-logo">
                <div class="footer-icons">
                    <!-- Iconos para teléfono, ubicación y web -->
                    <a href="#" class="footer-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path
                                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                            </path>
                        </svg></a>
                    <a href="#" class="footer-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg></a>
                    <a href="#" class="footer-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="2" y1="12" x2="22" y2="12"></line>
                            <path
                                d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z">
                            </path>
                        </svg></a>
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
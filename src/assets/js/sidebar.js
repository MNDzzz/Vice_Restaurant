// Lógica de cambio de pestañas de la barra lateral
document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('.sidebar-tab');
    const contents = document.querySelectorAll('.tab-content');
    const sidebar = document.getElementById('viceMenuSidebar');
    const navbar = document.querySelector('.vice-navbar');

    // Oculto/Muestro la navbar cuando la barra lateral se abre/cierra
    if (sidebar && navbar) {
        sidebar.addEventListener('show.bs.offcanvas', () => {
            navbar.style.opacity = '0';
            navbar.style.pointerEvents = 'none';
        });

        sidebar.addEventListener('hide.bs.offcanvas', () => {
            navbar.style.opacity = '1';
            navbar.style.pointerEvents = 'auto';
        });
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const targetTab = tab.getAttribute('data-tab');

            // Manejo redirecciones para Pedir y Mis Pedidos
            if (targetTab === 'pedir') {
                window.location.href = 'index.php?view=pedir';
                return;
            }
            if (targetTab === 'mis-pedidos') {
                window.location.href = 'index.php?view=mis-pedidos';
                return;
            }
            if (targetTab === 'login-required-pedir' || targetTab === 'login-required-pedidos') {
                window.location.href = 'index.php?view=login';
                return;
            }
            if (targetTab === 'perfil') {
                window.location.href = 'index.php?view=perfil';
                return;
            }

            // Elimino la clase active de todas las pestañas y contenidos
            tabs.forEach(t => t.classList.remove('active'));
            contents.forEach(c => c.classList.remove('active'));

            // Añado la clase active a la pestaña clicada y su contenido correspondiente
            tab.classList.add('active');
            const targetContent = document.querySelector(`[data-content="${targetTab}"]`);
            if (targetContent) {
                targetContent.classList.add('active');
            }
        });
    });

    // CIERRE AUTOMÁTICO Y SCROLL AL HACER CLIC EN UN ENLACE
    const links = document.querySelectorAll('.sidebar-link');
    links.forEach(link => {
        link.addEventListener('click', (e) => {
            const href = link.getAttribute('href');

            // Si es un enlace de anclaje en la misma página (ej: #ubicacion)
            if (href.includes('#')) {
                const targetId = href.split('#')[1];
                const targetElement = document.getElementById(targetId);

                // Cerramos el menú
                const bsOffcanvas = bootstrap.Offcanvas.getInstance(sidebar);
                if (bsOffcanvas) {
                    bsOffcanvas.hide();
                }

                // Si el elemento existe en la página actual, esperamos a que cierre y hacemos scroll
                if (targetElement) {

                    // Pequeño timeout para dejar que el offcanvas desaparezca y el body recupere el scroll
                    setTimeout(() => {
                        targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        // Actualizamos la URL sin recargar
                        history.pushState(null, null, `#${targetId}`);
                    }, 350); // 350ms (transición de bootstrap es 300ms)
                }
            } else {
                // Navegación normal, cerramos por estética
                const bsOffcanvas = bootstrap.Offcanvas.getInstance(sidebar);
                if (bsOffcanvas) {
                    bsOffcanvas.hide();
                }
            }
        });
    });

    // SCROLL AUTOMÁTICO AL CARGAR LA PÁGINA (Si hay hash en la URL)
    if (window.location.hash) {
        setTimeout(() => {
            const targetElement = document.querySelector(window.location.hash);
            if (targetElement) {
                targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }, 500); // 500ms para asegurar que las imágenes/layout estén listos
    }
});

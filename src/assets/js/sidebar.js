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
});

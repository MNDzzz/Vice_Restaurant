<?php
// Actualizador simple de navegación - ejecuto una vez
$indexFile = 'c:/xampp/htdocs/Vice/index.php';
$content = file_get_contents($indexFile);

// Reemplazo el nombre de la vista de carta a menu/pedir
$content = str_replace("href=\"index.php?view=carta\">Carta</a>", "href=\"index.php?view=menu\">Menú</a>\n                    </li>\n                    <li class=\"nav-item\">\n                        <a class=\"nav-link <?php echo \$view == 'pedir' ? 'active' : ''; ?>\"\n                            href=\"index.php?view=pedir\">Pedir</a>", $content);

$content = str_replace("\$view == 'carta'", "\$view == 'menu'", $content);

// Añado el enlace de Mis Pedidos
$content = str_replace(
    "<?php if (isset(\$_SESSION['user_id'])): ?>\n                        <li class=\"nav-item\">",
    "<?php if (isset(\$_SESSION['user_id'])): ?>\n                        <li class=\"nav-item\">\n                            <a class=\"nav-link <?php echo \$view == 'mis-pedidos' ? 'active' : ''; ?>\"\n                               href=\"index.php?view=mis-pedidos\">Mis Pedidos</a>\n                        </li>\n                        <li class=\"nav-item\">",
    $content
);

file_put_contents($indexFile, $content);
echo "Navbar updated successfully!\n";
?>
<?php

/**
 * Autoloader PSR-4 manual para el proyecto Vice.
 * Registra la función de carga automática de clases
 * para eliminar los require_once manuales de los controladores.
 */
spl_autoload_register(function (string $clase): void {
    // Defino el directorio raíz de la aplicación
    $directorioBase = __DIR__ . '/..';

    // Mapa de namespaces a directorios
    $mapaNombreEspacio = [
        'controllers\\' => $directorioBase . '/controllers/',
        'dao\\'         => $directorioBase . '/dao/',
        'models\\'      => $directorioBase . '/models/',
        'services\\'    => $directorioBase . '/services/',
        'core\\'        => $directorioBase . '/core/',
    ];

    foreach ($mapaNombreEspacio as $prefijo => $directorio) {
        if (str_starts_with($clase, $prefijo)) {
            $archivo = $directorio . substr($clase, strlen($prefijo)) . '.php';
            if (file_exists($archivo)) {
                require_once $archivo;
            }
            return;
        }
    }

    // Búsqueda plana: si no hay prefijo de namespace, busca en todos los directorios
    $directoriosBusqueda = [
        $directorioBase . '/controllers/',
        $directorioBase . '/dao/',
        $directorioBase . '/models/',
        $directorioBase . '/services/',
        $directorioBase . '/config/',
    ];

    foreach ($directoriosBusqueda as $directorio) {
        $archivo = $directorio . $clase . '.php';
        if (file_exists($archivo)) {
            require_once $archivo;
            return;
        }
    }
});

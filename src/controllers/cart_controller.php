<?php

/**
 * Punto de entrada del carrito de compra.
 * Delega toda la lógica a CartController vía Router.
 */
require_once __DIR__ . '/../core/Router.php';
Router::despacharCarrito();
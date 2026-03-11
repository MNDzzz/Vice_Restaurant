<?php

/**
 * Punto de entrada de acciones de pedido (mis-pedidos.js).
 * Delega toda la lógica a OrderController vía Router.
 */
require_once __DIR__ . '/../core/Router.php';
Router::despacharAccionesPedido();
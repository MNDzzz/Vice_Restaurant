<?php

/**
 * Punto de entrada de autenticación.
 * Delega toda la lógica a AuthController vía Router.
 */
require_once __DIR__ . '/../core/Router.php';
Router::despacharAuth();
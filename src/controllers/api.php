<?php

/**
 * Punto de entrada de la API JSON (admin.js).
 * Delega toda la lógica a los controladores vía Router.
 */
require_once __DIR__ . '/../core/Router.php';
Router::despacharApi();
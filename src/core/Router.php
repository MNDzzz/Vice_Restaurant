<?php

/**
 * Router central del proyecto Vice.
 * Mapea las peticiones HTTP a los métodos correctos
 * de cada controlador.
 */
class Router
{
    /**
     * Despacha una petición de tipo API JSON.
     * Utilizado por api.php (admin.js).
     */
    public static function despacharApi(): void
    {
        require_once __DIR__ . '/../config/DB.php';
        require_once __DIR__ . '/../core/Autoloader.php';

        header('Content-Type: application/json');
        session_start();

        $action = $_GET['action'] ?? '';

        // Mapa de acciones a [Controlador, método]
        $rutas = [
            // Productos
            'get_products'        => ['ProductController', 'getAll'],
            'get_product'         => ['ProductController', 'getById'],
            'create_product'      => ['ProductController', 'store'],
            'update_product'      => ['ProductController', 'update'],
            'delete_product'      => ['ProductController', 'destroy'],
            // Categorías
            'get_categories'      => ['ProductController', 'getCategories'],
            // Usuarios
            'get_users'           => ['UserController', 'getAll'],
            'get_user'            => ['UserController', 'getById'],
            'create_user'         => ['UserController', 'store'],
            'update_user'         => ['UserController', 'update'],
            'delete_user'         => ['UserController', 'destroy'],
            'toggle_admin'        => ['UserController', 'toggleAdmin'],
            'toggle_active'       => ['UserController', 'toggleActive'],
            // Pedidos
            'get_orders'          => ['OrderController', 'getAll'],
            'get_order'           => ['OrderController', 'getById'],
            'update_order_status' => ['OrderController', 'updateStatus'],
            'delete_order'        => ['OrderController', 'destroy'],
            // Estadísticas
            'get_stats'           => ['OrderController', 'getStats'],
        ];

        if (isset($rutas[$action])) {
            [$clase, $metodo] = $rutas[$action];
            $controlador = new $clase();
            $controlador->$metodo();
        } else {
            echo json_encode(['error' => 'Acción no válida']);
        }
    }

    /**
     * Despacha acciones de pedido para el usuario autenticado.
     * Utilizado por order_actions.php (mis-pedidos.js).
     */
    public static function despacharAccionesPedido(): void
    {
        require_once __DIR__ . '/../config/DB.php';
        require_once __DIR__ . '/../core/Autoloader.php';

        header('Content-Type: application/json');
        session_start();

        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $action = $input['action'] ?? '';

        $controlador = new OrderController();

        switch ($action) {
            case 'reorder':
                $controlador->reorder($input);
                break;
            case 'cancel_order':
                $controlador->cancel($input);
                break;
            case 'update_delivery':
                $controlador->updateDelivery($input);
                break;
            default:
                echo json_encode(['success' => false, 'error' => 'Acción no válida']);
        }
    }

    /**
     * Despacha acciones del carrito.
     * Utilizado por cart_controller.php (vistas HTML + cart.js).
     */
    public static function despacharCarrito(): void
    {
        require_once __DIR__ . '/../config/DB.php';
        require_once __DIR__ . '/../core/Autoloader.php';

        session_start();

        $action = $_POST['action'] ?? $_GET['action'] ?? '';
        $controlador = new CartController();

        switch ($action) {
            case 'add':
                $controlador->add();
                break;
            case 'remove':
                $controlador->remove();
                break;
            case 'checkout':
                $controlador->checkout();
                break;
            default:
                header('Location: ../index.php?view=home');
        }
    }

    /**
     * Despacha acciones de autenticación.
     * Utilizado por auth.php (vistas HTML).
     */
    public static function despacharAuth(): void
    {
        require_once __DIR__ . '/../config/DB.php';
        require_once __DIR__ . '/../core/Autoloader.php';

        session_start();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../index.php?view=home');
            exit;
        }

        $action = $_POST['action'] ?? '';
        $controlador = new AuthController();

        switch ($action) {
            case 'login':
                $controlador->login();
                break;
            case 'register':
                $controlador->register();
                break;
            case 'logout':
                $controlador->logout();
                break;
            case 'update_profile':
                $controlador->updateProfile();
                break;
            default:
                header('Location: ../index.php?view=home');
                exit;
        }
    }
}

<?php
session_start();
require_once __DIR__ . '/../config/DB.php';
require_once __DIR__ . '/../dao/UserDAO.php';
require_once __DIR__ . '/../dao/ProductDAO.php';
require_once __DIR__ . '/../dao/CategoryDAO.php';
require_once __DIR__ . '/../dao/OrderDAO.php';

header('Content-Type: application/json');

// Obtengo el rol del usuario actual desde la sesión
$currentUserRole = $_SESSION['user_role'] ?? 'guest';
$currentUserId = $_SESSION['user_id'] ?? 0;
$action = $_GET['action'] ?? '';


// Funciones para verificar que el usuario puede gestionar a otro usuario
// ============================================
function canManageUser($currentRole, $targetRole)
{
    if ($currentRole === 'superadmin') {
        return true; // El superadministrador puede gestionar a todos
    }
    if ($currentRole === 'admin' && $targetRole === 'user') {
        return true; // El administrador solo puede gestionar usuarios normales
    }
    return false;
}

// ============================================
// ENDPOINTS DE PRODUCTOS
// ============================================

if ($action === 'get_products') {
    try {
        $productDAO = new ProductDAO();
        $products = $productDAO->getAll();

        $result = array_map(function ($product) {
            return $product->toArray();
        }, $products);

        echo json_encode($result);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'get_product') {
    try {
        $id = $_GET['id'] ?? 0;
        $productDAO = new ProductDAO();
        $product = $productDAO->getById($id);

        echo json_encode($product ? $product->toArray() : null);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'create_product' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['name'], $data['price'])) {
        try {
            $product = new Product([
                'name' => $data['name'],
                'description' => $data['description'] ?? '',
                'price' => $data['price'],
                'image' => $data['image'] ?? 'img/default-product.jpg',
                'category_id' => $data['category_id'] ?? null
            ]);

            $productDAO = new ProductDAO();
            $createdProduct = $productDAO->create($product);

            echo json_encode(['success' => true, 'id' => $createdProduct->getId()]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    }
    exit;
}

if ($action === 'update_product' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['id'], $data['name'], $data['price'])) {
        try {
            $product = new Product([
                'id' => $data['id'],
                'name' => $data['name'],
                'description' => $data['description'] ?? '',
                'price' => $data['price'],
                'image' => $data['image'] ?? 'img/default-product.jpg',
                'category_id' => !empty($data['category_id']) ? $data['category_id'] : null
            ]);

            $productDAO = new ProductDAO();
            $success = $productDAO->update($product);

            echo json_encode(['success' => $success]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    }
    exit;
}

if ($action === 'delete_product' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['id'])) {
        try {
            $productDAO = new ProductDAO();
            $success = $productDAO->delete($data['id']);

            echo json_encode(['success' => $success]);
        } catch (Exception $e) {
            if ($e->getCode() == '23000' && strpos($e->getMessage(), '1451') !== false) {
                echo json_encode(['success' => false, 'error' => 'No se puede eliminar: Este producto está en pedidos realizados.']);
            } else {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Missing ID']);
    }
    exit;
}

// ============================================
// ENDPOINTS DE CATEGORÍAS
// ============================================

if ($action === 'get_categories') {
    try {
        $categoryDAO = new CategoryDAO();
        $categories = $categoryDAO->getAll();

        $result = array_map(function ($category) {
            return $category->toArray();
        }, $categories);

        echo json_encode($result);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// ============================================
// ENDPOINTS DE USUARIOS
// ============================================

if ($action === 'get_users') {
    try {
        $userDAO = new UserDAO();

        // Filtro los usuarios basándome en el rol del usuario actual
        if ($currentUserRole === 'superadmin') {
            $users = $userDAO->getAll();
        } elseif ($currentUserRole === 'admin') {
            $users = $userDAO->getAll('user'); // Solo usuarios normales
        } else {
            echo json_encode([]);
            exit;
        }

        $result = array_map(function ($user) {
            return $user->toArray();
        }, $users);

        echo json_encode($result);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'get_user') {
    try {
        $id = $_GET['id'] ?? 0;
        $userDAO = new UserDAO();
        $user = $userDAO->getById($id);

        // Verifico los permisos
        if ($user && canManageUser($currentUserRole, $user->getRole())) {
            echo json_encode($user->toArray());
        } else {
            echo json_encode(['error' => 'Unauthorized']);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'create_user' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['name'], $data['email'], $data['password'])) {
        $role = $data['role'] ?? 'user';

        // Verifico si el usuario actual puede crear usuarios con este rol
        if ($currentUserRole === 'admin' && $role !== 'user') {
            echo json_encode(['success' => false, 'error' => 'No tienes permisos para crear administradores']);
            exit;
        }

        try {
            $user = new User([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'role' => $role,
                'is_active' => $data['is_active'] ?? 1
            ]);

            $userDAO = new UserDAO();
            $createdUser = $userDAO->create($user);

            echo json_encode(['success' => true, 'id' => $createdUser->getId()]);
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate') !== false) {
                echo json_encode(['success' => false, 'error' => 'El email ya está registrado']);
            } else {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Faltan campos requeridos']);
    }
    exit;
}

if ($action === 'update_user' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['id'], $data['name'], $data['email'])) {
        try {
            $userDAO = new UserDAO();
            $targetUser = $userDAO->getById($data['id']);

            if (!$targetUser || !canManageUser($currentUserRole, $targetUser->getRole())) {
                echo json_encode(['success' => false, 'error' => 'No tienes permisos para editar este usuario']);
                exit;
            }

            // Evito que un admin cree otros admins
            $newRole = $data['role'] ?? $targetUser->getRole();
            if ($currentUserRole === 'admin' && $newRole !== 'user') {
                $newRole = 'user';
            }

            $user = new User([
                'id' => $data['id'],
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => !empty($data['password']) ? password_hash($data['password'], PASSWORD_DEFAULT) : null,
                'role' => $newRole,
                'is_active' => $data['is_active'] ?? 1
            ]);

            $success = $userDAO->update($user);

            echo json_encode(['success' => $success]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Faltan campos requeridos']);
    }
    exit;
}

if ($action === 'delete_user' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['id'])) {
        // Evito que el usuario se elimine a sí mismo
        if ($data['id'] == $currentUserId) {
            echo json_encode(['success' => false, 'error' => 'No puedes eliminarte a ti mismo']);
            exit;
        }

        try {
            $userDAO = new UserDAO();
            $targetUser = $userDAO->getById($data['id']);

            if (!$targetUser || !canManageUser($currentUserRole, $targetUser->getRole())) {
                echo json_encode(['success' => false, 'error' => 'No tienes permisos para eliminar este usuario']);
                exit;
            }

            $success = $userDAO->delete($data['id']);

            echo json_encode(['success' => $success]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Falta ID']);
    }
    exit;
}

if ($action === 'toggle_admin' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['id'])) {
        // Solo el superadmin tiene poder para cambiar roles
        if ($currentUserRole !== 'superadmin') {
            echo json_encode(['success' => false, 'error' => 'No tienes permisos para cambiar roles']);
            exit;
        }

        try {
            $userDAO = new UserDAO();
            $user = $userDAO->getById($data['id']);

            if ($user) {
                $newRole = $user->getRole() === 'admin' ? 'user' : 'admin';
                $userDAO->updateRole($data['id'], $newRole);

                echo json_encode(['success' => true, 'newRole' => $newRole]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Falta ID']);
    }
    exit;
}

if ($action === 'toggle_active' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['id'])) {
        try {
            $userDAO = new UserDAO();
            $user = $userDAO->getById($data['id']);

            if (!$user || !canManageUser($currentUserRole, $user->getRole())) {
                echo json_encode(['success' => false, 'error' => 'No tienes permisos']);
                exit;
            }

            $userDAO->toggleActive($data['id']);
            $updatedUser = $userDAO->getById($data['id']);

            echo json_encode(['success' => true, 'is_active' => $updatedUser->isActive()]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Falta ID']);
    }
    exit;
}

// ============================================
// ENDPOINTS DE PEDIDOS
// ============================================

if ($action === 'get_orders') {
    try {
        $orderDAO = new OrderDAO();
        $orders = $orderDAO->getAll();

        $result = array_map(function ($order) {
            return $order->toArray();
        }, $orders);

        echo json_encode($result);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'get_order') {
    try {
        $id = $_GET['id'] ?? 0;
        $orderDAO = new OrderDAO();
        $order = $orderDAO->getById($id);

        echo json_encode($order ? $order->toArray() : null);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'update_order_status' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['id'], $data['status'])) {
        $validStatuses = ['pending', 'completed', 'cancelled'];
        if (!in_array($data['status'], $validStatuses)) {
            echo json_encode(['success' => false, 'error' => 'Estado inválido']);
            exit;
        }

        try {
            $orderDAO = new OrderDAO();
            $success = $orderDAO->updateStatus($data['id'], $data['status']);

            echo json_encode(['success' => $success]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Faltan campos requeridos']);
    }
    exit;
}

if ($action === 'delete_order' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['id'])) {
        try {
            $orderDAO = new OrderDAO();
            $success = $orderDAO->delete($data['id']);

            echo json_encode(['success' => $success]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Falta ID']);
    }
    exit;
}

// ============================================
// ESTADÍSTICAS DEL DASHBOARD
// ============================================

if ($action === 'get_stats') {
    try {
        $db = DB::getInstance()->getConnection();
        $stats = [];

        // Total de usuarios
        $stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'");
        $stats['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        // Total de pedidos
        $stmt = $db->query("SELECT COUNT(*) as count FROM orders");
        $stats['total_orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        // Pedidos pendientes
        $stmt = $db->query("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'");
        $stats['pending_orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        // Total de productos
        $stmt = $db->query("SELECT COUNT(*) as count FROM products");
        $stats['total_products'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        // Ingresos totales
        $stmt = $db->query("SELECT COALESCE(SUM(total), 0) as total FROM orders WHERE status = 'completed'");
        $stats['total_revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        echo json_encode($stats);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// Respuesta por defecto
echo json_encode(['error' => 'Invalid action']);
?>
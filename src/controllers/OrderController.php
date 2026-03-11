<?php

/**
 * OrderController
 * Gestiona el CRUD de pedidos para el panel de administración
 * y las acciones de usuario (reorder, cancelar, actualizar entrega).
 */
class OrderController
{
    /**
     * Devuelve todos los pedidos (acceso administrador).
     */
    public function getAll(): void
    {
        try {
            $orderDAO = new OrderDAO();
            $pedidos  = $orderDAO->getAll();

            $resultado = array_map(fn($p) => $p->toArray(), $pedidos);
            echo json_encode($resultado);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Devuelve un pedido por su ID.
     */
    public function getById(): void
    {
        try {
            $id       = $_GET['id'] ?? 0;
            $orderDAO = new OrderDAO();
            $pedido   = $orderDAO->getById($id);

            echo json_encode($pedido ? $pedido->toArray() : null);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Actualiza el estado de un pedido (admin).
     */
    public function updateStatus(): void
    {
        $datos = json_decode(file_get_contents('php://input'), true);

        if (!isset($datos['id'], $datos['status'])) {
            echo json_encode(['success' => false, 'error' => 'Faltan campos requeridos']);
            return;
        }

        $estadosValidos = ['pending', 'completed', 'cancelled'];
        if (!in_array($datos['status'], $estadosValidos)) {
            echo json_encode(['success' => false, 'error' => 'Estado inválido']);
            return;
        }

        try {
            $orderDAO = new OrderDAO();
            $exito    = $orderDAO->updateStatus($datos['id'], $datos['status']);

            echo json_encode(['success' => $exito]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Elimina un pedido (admin).
     */
    public function destroy(): void
    {
        $datos = json_decode(file_get_contents('php://input'), true);

        if (!isset($datos['id'])) {
            echo json_encode(['success' => false, 'error' => 'Falta el ID']);
            return;
        }

        try {
            $orderDAO = new OrderDAO();
            $exito    = $orderDAO->delete($datos['id']);

            echo json_encode(['success' => $exito]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Devuelve las estadísticas del dashboard de administración.
     */
    public function getStats(): void
    {
        try {
            $db         = DB::getInstance()->getConnection();
            $estadisticas = [];

            // Total de usuarios
            $stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'");
            $estadisticas['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            // Total de pedidos
            $stmt = $db->query("SELECT COUNT(*) as count FROM orders");
            $estadisticas['total_orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            // Pedidos pendientes
            $stmt = $db->query("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'");
            $estadisticas['pending_orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            // Total de productos
            $stmt = $db->query("SELECT COUNT(*) as count FROM products");
            $estadisticas['total_products'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            // Ingresos totales de pedidos completados
            $stmt = $db->query("SELECT COALESCE(SUM(total), 0) as total FROM orders WHERE status = 'completed'");
            $estadisticas['total_revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            echo json_encode($estadisticas);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // =========================================================
    // Acciones de usuario (mis-pedidos.js → order_actions.php)
    // =========================================================

    /**
     * Vuelve a añadir los productos de un pedido anterior al carrito.
     */
    public function reorder(array $input): void
    {
        $userId  = $_SESSION['user_id'] ?? null;
        $orderId = $input['order_id'] ?? 0;

        if (!$userId) {
            echo json_encode(['success' => false, 'error' => 'No autorizado']);
            return;
        }

        try {
            $orderDAO = new OrderDAO();
            $pedido   = $orderDAO->getById($orderId);

            if (!$pedido || $pedido->getUserId() != $userId) {
                echo json_encode(['success' => false, 'error' => 'Pedido no encontrado']);
                return;
            }

            $items = $pedido->getItems();
            if (count($items) === 0) {
                echo json_encode(['success' => false, 'error' => 'No hay productos en este pedido']);
                return;
            }

            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            foreach ($items as $item) {
                if ($item->getProductId()) {
                    $productoId = $item->getProductId();
                    $encontrado = false;

                    foreach ($_SESSION['cart'] as &$cartItem) {
                        if ($cartItem['id'] == $productoId) {
                            $cartItem['quantity'] += $item->getQuantity();
                            $encontrado = true;
                            break;
                        }
                    }

                    if (!$encontrado) {
                        $_SESSION['cart'][] = [
                            'id'       => $productoId,
                            'name'     => $item->getProductName(),
                            'price'    => $item->getPrice(),
                            'quantity' => $item->getQuantity(),
                        ];
                    }
                }
            }

            echo json_encode(['success' => true, 'message' => 'Productos añadidos al carrito']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Error del servidor: ' . $e->getMessage()]);
        }
    }

    /**
     * Cancela un pedido pendiente del usuario en sesión.
     */
    public function cancel(array $input): void
    {
        $userId  = $_SESSION['user_id'] ?? null;
        $orderId = $input['order_id'] ?? 0;

        if (!$userId) {
            echo json_encode(['success' => false, 'error' => 'No autorizado']);
            return;
        }

        try {
            $orderDAO = new OrderDAO();
            $pedido   = $orderDAO->getById($orderId);

            if (!$pedido || $pedido->getUserId() != $userId) {
                echo json_encode(['success' => false, 'error' => 'Pedido no encontrado']);
                return;
            }

            if (!$pedido->isPending()) {
                echo json_encode(['success' => false, 'error' => 'Solo se pueden cancelar pedidos pendientes']);
                return;
            }

            $orderDAO->updateStatus($orderId, 'cancelled');
            echo json_encode(['success' => true, 'message' => 'Pedido cancelado']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Error del servidor: ' . $e->getMessage()]);
        }
    }

    /**
     * Actualiza la información de entrega de un pedido pendiente.
     */
    public function updateDelivery(array $input): void
    {
        $userId  = $_SESSION['user_id'] ?? null;
        $orderId = $input['order_id'] ?? 0;

        if (!$userId) {
            echo json_encode(['success' => false, 'error' => 'No autorizado']);
            return;
        }

        try {
            $orderDAO = new OrderDAO();
            $pedido   = $orderDAO->getById($orderId);

            if (!$pedido || $pedido->getUserId() != $userId) {
                echo json_encode(['success' => false, 'error' => 'Pedido no encontrado']);
                return;
            }

            if (!$pedido->isPending()) {
                echo json_encode(['success' => false, 'error' => 'Solo se puede editar información de pedidos pendientes']);
                return;
            }

            $datosEntrega = [
                'delivery_name'        => $input['delivery_name'] ?? '',
                'delivery_phone'       => $input['delivery_phone'] ?? '',
                'delivery_address'     => $input['delivery_address'] ?? '',
                'delivery_city'        => $input['delivery_city'] ?? '',
                'delivery_postal_code' => $input['delivery_postal_code'] ?? '',
                'delivery_notes'       => $input['delivery_notes'] ?? '',
            ];

            $orderDAO->updateDeliveryInfo($orderId, $datosEntrega);
            echo json_encode(['success' => true, 'message' => 'Información de entrega actualizada']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Error del servidor: ' . $e->getMessage()]);
        }
    }
}

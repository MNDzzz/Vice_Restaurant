<?php
/**
 * Acciones de Pedido
 * Controlador para volver a pedir, cancelar y actualizar la entrega
 */

session_start();
require_once __DIR__ . '/../config/DB.php';
require_once __DIR__ . '/../dao/OrderDAO.php';
require_once __DIR__ . '/../dao/ProductDAO.php';

header('Content-Type: application/json');

// Verifico si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

$userId = $_SESSION['user_id'];

// Obtengo la entrada JSON
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$orderId = $input['order_id'] ?? 0;

try {
    $orderDAO = new OrderDAO();

    switch ($action) {
        case 'reorder':
            // Obtengo el pedido y verifico la propiedad
            $order = $orderDAO->getById($orderId);

            if (!$order || $order->getUserId() != $userId) {
                echo json_encode(['success' => false, 'error' => 'Pedido no encontrado']);
                exit;
            }

            // Obtengo los artículos del pedido
            $items = $order->getItems();

            if (count($items) === 0) {
                echo json_encode(['success' => false, 'error' => 'No hay productos en este pedido']);
                exit;
            }

            // Añado los artículos al carrito
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            foreach ($items as $item) {
                if ($item->getProductId()) {
                    $productId = $item->getProductId();

                    // Verifico si el producto ya está en el carrito
                    $found = false;
                    foreach ($_SESSION['cart'] as &$cartItem) {
                        if ($cartItem['id'] == $productId) {
                            $cartItem['quantity'] += $item->getQuantity();
                            $found = true;
                            break;
                        }
                    }

                    // Añado el nuevo artículo si no se encuentra
                    if (!$found) {
                        $_SESSION['cart'][] = [
                            'id' => $productId,
                            'name' => $item->getProductName(),
                            'price' => $item->getPrice(),
                            'quantity' => $item->getQuantity()
                        ];
                    }
                }
            }

            echo json_encode(['success' => true, 'message' => 'Productos añadidos al carrito']);
            break;

        case 'cancel_order':
            $order = $orderDAO->getById($orderId);

            if (!$order || $order->getUserId() != $userId) {
                echo json_encode(['success' => false, 'error' => 'Pedido no encontrado']);
                exit;
            }

            if (!$order->isPending()) {
                echo json_encode(['success' => false, 'error' => 'Solo se pueden cancelar pedidos pendientes']);
                exit;
            }

            // Actualizo el estado a cancelado
            $orderDAO->updateStatus($orderId, 'cancelled');

            echo json_encode(['success' => true, 'message' => 'Pedido cancelado']);
            break;

        case 'update_delivery':
            $order = $orderDAO->getById($orderId);

            if (!$order || $order->getUserId() != $userId) {
                echo json_encode(['success' => false, 'error' => 'Pedido no encontrado']);
                exit;
            }

            if (!$order->isPending()) {
                echo json_encode(['success' => false, 'error' => 'Solo se puede editar información de pedidos pendientes']);
                exit;
            }

            // Obtengo los datos de entrega
            $deliveryData = [
                'delivery_name' => $input['delivery_name'] ?? '',
                'delivery_phone' => $input['delivery_phone'] ?? '',
                'delivery_address' => $input['delivery_address'] ?? '',
                'delivery_city' => $input['delivery_city'] ?? '',
                'delivery_postal_code' => $input['delivery_postal_code'] ?? '',
                'delivery_notes' => $input['delivery_notes'] ?? ''
            ];

            // Actualizo la información de entrega
            $orderDAO->updateDeliveryInfo($orderId, $deliveryData);

            echo json_encode(['success' => true, 'message' => 'Información de entrega actualizada']);
            break;

        default:
            echo json_encode(['success' => false, 'error' => 'Acción no válida']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error del servidor: ' . $e->getMessage()]);
}
?>
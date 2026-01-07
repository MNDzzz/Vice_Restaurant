<?php
session_start();
require_once __DIR__ . '/../config/DB.php';
require_once __DIR__ . '/../dao/ProductDAO.php';
require_once __DIR__ . '/../dao/OrderDAO.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($action === 'add') {
    $id = $_POST['product_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $quantity = 1;

    // Verifico si el artículo ya existe en el carrito
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $id) {
            $item['quantity']++;
            $found = true;
            break;
        }
    }

    if (!$found) {
        $_SESSION['cart'][] = [
            'id' => $id,
            'name' => $name,
            'price' => $price,
            'quantity' => $quantity
        ];
    }

    header('Location: ../index.php?view=pedir');

} elseif ($action === 'remove') {
    $id = $_POST['product_id'];

    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $id) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }
    // Reindexo el array
    $_SESSION['cart'] = array_values($_SESSION['cart']);
    header('Location: ../index.php?view=carrito');

} elseif ($action === 'checkout') {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../index.php?view=login');
        exit;
    }

    $user_id = $_SESSION['user_id'];
    require_once __DIR__ . '/../services/DiscountService.php';

    // Calculamos totales usando el servicio centralizado
    $totals = DiscountService::calculateDetails($_SESSION['cart']);
    $finalTotal = $totals['finalTotal'];

    // Obtengo la información de entrega
    $delivery_name = $_POST['delivery_name'] ?? '';
    $delivery_phone = $_POST['delivery_phone'] ?? '';
    $delivery_address = $_POST['delivery_address'] ?? '';
    $delivery_city = $_POST['delivery_city'] ?? '';
    $delivery_postal_code = $_POST['delivery_postal_code'] ?? '';
    $delivery_notes = $_POST['delivery_notes'] ?? '';

    try {
        $orderDAO = new OrderDAO();
        $orderDAO->beginTransaction();

        // Creo el pedido con la información de entrega
        $order = new Order([
            'user_id' => $user_id,
            'total' => $finalTotal, // Uso el total final con descuentos aplicados
            'status' => 'pending',
            'delivery_name' => $delivery_name,
            'delivery_phone' => $delivery_phone,
            'delivery_address' => $delivery_address,
            'delivery_city' => $delivery_city,
            'delivery_postal_code' => $delivery_postal_code,
            'delivery_notes' => $delivery_notes
        ]);

        // Preparo los artículos del pedido
        $orderItems = [];
        foreach ($_SESSION['cart'] as $item) {
            $orderItems[] = new OrderItem([
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ]);
        }
        $order->setItems($orderItems);

        // Creo el pedido en la base de datos
        $orderDAO->create($order);

        $orderDAO->commit();

        // Vacío el carrito
        $_SESSION['cart'] = [];

        // Redirijo a Mis Pedidos con un mensaje de éxito
        header('Location: ../index.php?view=mis-pedidos&success=order_placed');

    } catch (Exception $e) {
        $orderDAO->rollback();
        header('Location: ../index.php?view=checkout&error=order_failed');
    }
}
?>
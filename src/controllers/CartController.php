<?php

/**
 * CartController
 * Gestiona las acciones del carrito de compra:
 * añadir, eliminar y finalizar pedido (checkout).
 */
class CartController
{
    /**
     * Añade un producto al carrito de sesión.
     */
    public function add(): void
    {
        $id       = $_POST['product_id'];
        $name     = $_POST['name'];
        $price    = $_POST['price'];

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Verifico si el artículo ya existe en el carrito
        $encontrado = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $id) {
                $item['quantity']++;
                $encontrado = true;
                break;
            }
        }

        if (!$encontrado) {
            $_SESSION['cart'][] = [
                'id'       => $id,
                'name'     => $name,
                'price'    => $price,
                'quantity' => 1,
            ];
        }

        header('Location: ../index.php?view=pedir');
        exit;
    }

    /**
     * Elimina un producto del carrito de sesión.
     */
    public function remove(): void
    {
        $id = $_POST['product_id'];

        foreach ($_SESSION['cart'] as $clave => $item) {
            if ($item['id'] == $id) {
                unset($_SESSION['cart'][$clave]);
                break;
            }
        }

        // Reindexo el array tras la eliminación
        $_SESSION['cart'] = array_values($_SESSION['cart']);
        header('Location: ../index.php?view=carrito');
        exit;
    }

    /**
     * Procesa el pago y crea el pedido en la base de datos.
     */
    public function checkout(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../index.php?view=login');
            exit;
        }

        $userId = $_SESSION['user_id'];

        // Calculo los totales usando el servicio centralizado
        $totales    = DiscountService::calculateDetails($_SESSION['cart']);
        $totalFinal = $totales['finalTotal'];

        // Obtengo la información de entrega del formulario
        $datosEntrega = [
            'delivery_name'        => $_POST['delivery_name'] ?? '',
            'delivery_phone'       => $_POST['delivery_phone'] ?? '',
            'delivery_address'     => $_POST['delivery_address'] ?? '',
            'delivery_city'        => $_POST['delivery_city'] ?? '',
            'delivery_postal_code' => $_POST['delivery_postal_code'] ?? '',
            'delivery_notes'       => $_POST['delivery_notes'] ?? '',
        ];

        try {
            $orderDAO = new OrderDAO();
            $orderDAO->beginTransaction();

            // Creo el pedido con los datos de entrega
            $order = new Order(array_merge([
                'user_id' => $userId,
                'total'   => $totalFinal,
                'status'  => 'pending',
            ], $datosEntrega));

            // Preparo los artículos del pedido
            $itemsPedido = [];
            foreach ($_SESSION['cart'] as $item) {
                $itemsPedido[] = new OrderItem([
                    'product_id' => $item['id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                ]);
            }
            $order->setItems($itemsPedido);

            // Persisto el pedido en la base de datos
            $orderDAO->create($order);
            $orderDAO->commit();

            // Vacío el carrito de sesión
            $_SESSION['cart'] = [];

            header('Location: ../index.php?view=mis-pedidos&success=order_placed');
            exit;
        } catch (Exception $e) {
            $orderDAO->rollback();
            header('Location: ../index.php?view=checkout&error=order_failed');
            exit;
        }
    }
}

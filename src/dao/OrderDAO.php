<?php
require_once __DIR__ . '/BaseDAO.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/OrderItem.php';

class OrderDAO extends BaseDAO
{
    protected $table = 'orders';

    public function getAll()
    {
        $stmt = $this->db->query("
            SELECT o.*, u.name as user_name, u.email as user_email 
            FROM orders o 
            LEFT JOIN users u ON o.user_id = u.id 
            ORDER BY o.created_at DESC
        ");

        $orders = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $order = new Order($row);
            // Cargo los artículos para cada pedido
            $order->setItems($this->getOrderItems($order->getId()));
            $orders[] = $order;
        }
        return $orders;
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("
            SELECT o.*, u.name as user_name, u.email as user_email 
            FROM orders o 
            LEFT JOIN users u ON o.user_id = u.id 
            WHERE o.id = ?
        ");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $order = new Order($data);
            $order->setItems($this->getOrderItems($id));
            return $order;
        }
        return null;
    }

    public function getByUserId($userId)
    {
        $stmt = $this->db->prepare("
            SELECT o.*, u.name as user_name, u.email as user_email 
            FROM orders o 
            LEFT JOIN users u ON o.user_id = u.id 
            WHERE o.user_id = ? 
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$userId]);

        $orders = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $order = new Order($row);
            $order->setItems($this->getOrderItems($order->getId()));
            $orders[] = $order;
        }
        return $orders;
    }

    public function getOrderItems($orderId)
    {
        $stmt = $this->db->prepare("
            SELECT oi.*, p.name as product_name 
            FROM order_items oi 
            LEFT JOIN products p ON oi.product_id = p.id 
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$orderId]);

        $items = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $items[] = new OrderItem($row);
        }
        return $items;
    }

    /**
     * @param Order $order
     * @return Order
     */
    public function create(Order $order)
    {
        $stmt = $this->db->prepare("
            INSERT INTO orders (user_id, total, status, delivery_name, delivery_phone, delivery_address, delivery_city, delivery_postal_code, delivery_notes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $order->getUserId(),
            $order->getTotal(),
            $order->getStatus(),
            $order->getDeliveryName(),
            $order->getDeliveryPhone(),
            $order->getDeliveryAddress(),
            $order->getDeliveryCity(),
            $order->getDeliveryPostalCode(),
            $order->getDeliveryNotes()
        ]);
        $order->setId($this->db->lastInsertId());

        // Creo los artículos del pedido
        if (!empty($order->getItems())) {
            $this->createOrderItems($order->getId(), $order->getItems());
        }

        return $order;
    }

    public function createOrderItems($orderId, $items)
    {
        $stmt = $this->db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");

        foreach ($items as $item) {
            if ($item instanceof OrderItem) {
                $stmt->execute([
                    $orderId,
                    $item->getProductId(),
                    $item->getQuantity(),
                    $item->getPrice()
                ]);
            } else {
                // Soporte para formato de array
                $stmt->execute([
                    $orderId,
                    $item['product_id'],
                    $item['quantity'],
                    $item['price']
                ]);
            }
        }
    }

    public function updateStatus($id, $status)
    {
        $stmt = $this->db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function updateDeliveryInfo($id, $deliveryData)
    {
        $stmt = $this->db->prepare("
            UPDATE orders 
            SET delivery_name = ?, delivery_phone = ?, delivery_address = ?, delivery_city = ?, delivery_postal_code = ?, delivery_notes = ? 
            WHERE id = ?
        ");
        return $stmt->execute([
            $deliveryData['delivery_name'],
            $deliveryData['delivery_phone'],
            $deliveryData['delivery_address'],
            $deliveryData['delivery_city'],
            $deliveryData['delivery_postal_code'],
            $deliveryData['delivery_notes'] ?? null,
            $id
        ]);
    }

    public function delete($id)
    {
        // Los artículos del pedido se eliminarán automáticamente por el ON DELETE CASCADE
        return $this->deleteById($id);
    }
}
?>
<?php
class OrderItem
{
    private $id;
    private $orderId;
    private $productId;
    private $quantity;
    private $price;
    private $productName; // Para consultas con JOIN

    public function __construct($data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->orderId = $data['order_id'] ?? null;
        $this->productId = $data['product_id'] ?? null;
        $this->quantity = $data['quantity'] ?? 1;
        $this->price = $data['price'] ?? 0.0;
        $this->productName = $data['product_name'] ?? null;
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }
    public function getOrderId()
    {
        return $this->orderId;
    }
    public function getProductId()
    {
        return $this->productId;
    }
    public function getQuantity()
    {
        return $this->quantity;
    }
    public function getPrice()
    {
        return $this->price;
    }
    public function getProductName()
    {
        return $this->productName;
    }

    // Setters
    public function setId($id)
    {
        $this->id = $id;
    }
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }
    public function setProductId($productId)
    {
        $this->productId = $productId;
    }
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }
    public function setPrice($price)
    {
        $this->price = $price;
    }
    public function setProductName($productName)
    {
        $this->productName = $productName;
    }

    public function toArray()
    {
        $array = [
            'id' => $this->id,
            'order_id' => $this->orderId,
            'product_id' => $this->productId,
            'quantity' => $this->quantity,
            'price' => $this->price
        ];

        if ($this->productName !== null) {
            $array['product_name'] = $this->productName;
        }

        return $array;
    }

    public function getSubtotal()
    {
        return $this->price * $this->quantity;
    }
}
?>
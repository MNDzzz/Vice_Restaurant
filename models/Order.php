<?php
class Order
{
    private $id;
    private $userId;
    private $total;
    private $status;
    private $createdAt;
    private $deliveryName;
    private $deliveryPhone;
    private $deliveryAddress;
    private $deliveryCity;
    private $deliveryPostalCode;
    private $deliveryNotes;

    // Propiedades para consultas con JOIN
    private $userName;
    private $userEmail;
    private $items; // Array de objetos OrderItem

    public function __construct($data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->userId = $data['user_id'] ?? null;
        $this->total = $data['total'] ?? 0.0;
        $this->status = $data['status'] ?? 'pending';
        $this->createdAt = $data['created_at'] ?? null;
        $this->deliveryName = $data['delivery_name'] ?? null;
        $this->deliveryPhone = $data['delivery_phone'] ?? null;
        $this->deliveryAddress = $data['delivery_address'] ?? null;
        $this->deliveryCity = $data['delivery_city'] ?? null;
        $this->deliveryPostalCode = $data['delivery_postal_code'] ?? null;
        $this->deliveryNotes = $data['delivery_notes'] ?? null;
        $this->userName = $data['user_name'] ?? null;
        $this->userEmail = $data['user_email'] ?? null;
        $this->items = $data['items'] ?? [];
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }
    public function getUserId()
    {
        return $this->userId;
    }
    public function getTotal()
    {
        return $this->total;
    }
    public function getStatus()
    {
        return $this->status;
    }
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    public function getDeliveryName()
    {
        return $this->deliveryName;
    }
    public function getDeliveryPhone()
    {
        return $this->deliveryPhone;
    }
    public function getDeliveryAddress()
    {
        return $this->deliveryAddress;
    }
    public function getDeliveryCity()
    {
        return $this->deliveryCity;
    }
    public function getDeliveryPostalCode()
    {
        return $this->deliveryPostalCode;
    }
    public function getDeliveryNotes()
    {
        return $this->deliveryNotes;
    }
    public function getUserName()
    {
        return $this->userName;
    }
    public function getUserEmail()
    {
        return $this->userEmail;
    }
    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    // Setters
    public function setId($id)
    {
        $this->id = $id;
    }
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }
    public function setTotal($total)
    {
        $this->total = $total;
    }
    public function setStatus($status)
    {
        $this->status = $status;
    }
    public function setDeliveryName($name)
    {
        $this->deliveryName = $name;
    }
    public function setDeliveryPhone($phone)
    {
        $this->deliveryPhone = $phone;
    }
    public function setDeliveryAddress($address)
    {
        $this->deliveryAddress = $address;
    }
    public function setDeliveryCity($city)
    {
        $this->deliveryCity = $city;
    }
    public function setDeliveryPostalCode($postalCode)
    {
        $this->deliveryPostalCode = $postalCode;
    }
    public function setDeliveryNotes($notes)
    {
        $this->deliveryNotes = $notes;
    }

    /**
     * @param array $items
     */
    public function setItems(array $items)
    {
        $this->items = $items;
    }

    public function toArray()
    {
        $array = [
            'id' => $this->id,
            'user_id' => $this->userId,
            'total' => $this->total,
            'status' => $this->status,
            'created_at' => $this->createdAt,
            'delivery_name' => $this->deliveryName,
            'delivery_phone' => $this->deliveryPhone,
            'delivery_address' => $this->deliveryAddress,
            'delivery_city' => $this->deliveryCity,
            'delivery_postal_code' => $this->deliveryPostalCode,
            'delivery_notes' => $this->deliveryNotes
        ];

        if ($this->userName !== null) {
            $array['user_name'] = $this->userName;
        }
        if ($this->userEmail !== null) {
            $array['user_email'] = $this->userEmail;
        }
        if (!empty($this->items)) {
            $array['items'] = array_map(function ($item) {
                return is_object($item) && method_exists($item, 'toArray') ? $item->toArray() : $item;
            }, $this->items);
        }

        return $array;
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }
}
?>
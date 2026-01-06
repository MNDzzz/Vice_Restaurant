<?php
class Product
{
    private $id;
    private $categoryId;
    private $name;
    private $description;
    private $price;
    private $image;
    private $categoryName; // Para consultas con JOIN

    public function __construct($data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->categoryId = $data['category_id'] ?? null;
        $this->name = $data['name'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->price = $data['price'] ?? 0.0;
        $this->image = $data['image'] ?? '';
        $this->categoryName = $data['category_name'] ?? null;
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }
    public function getCategoryId()
    {
        return $this->categoryId;
    }
    public function getName()
    {
        return $this->name;
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function getPrice()
    {
        return $this->price;
    }
    public function getImage()
    {
        return $this->image;
    }
    public function getCategoryName()
    {
        return $this->categoryName;
    }

    // Setters
    public function setId($id)
    {
        $this->id = $id;
    }
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
    }
    public function setName($name)
    {
        $this->name = $name;
    }
    public function setDescription($description)
    {
        $this->description = $description;
    }
    public function setPrice($price)
    {
        $this->price = $price;
    }
    public function setImage($image)
    {
        $this->image = $image;
    }
    public function setCategoryName($categoryName)
    {
        $this->categoryName = $categoryName;
    }

    public function toArray()
    {
        $array = [
            'id' => $this->id,
            'category_id' => $this->categoryId,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'image' => $this->image
        ];

        if ($this->categoryName !== null) {
            $array['category_name'] = $this->categoryName;
        }

        return $array;
    }
}
?>
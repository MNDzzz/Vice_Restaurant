<?php
class Category
{
    private $id;
    private $name;
    private $image;

    public function __construct($data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? '';
        $this->image = $data['image'] ?? '';
    }

    // Obtengo el id, nombre e imagen
    public function getId()
    {
        return $this->id;
    }
    public function getName()
    {
        return $this->name;
    }
    public function getImage()
    {
        return $this->image;
    }

    // Establezco el id, nombre e imagen
    public function setId($id)
    {
        $this->id = $id;
    }
    public function setName($name)
    {
        $this->name = $name;
    }
    public function setImage($image)
    {
        $this->image = $image;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->image
        ];
    }
}
?>
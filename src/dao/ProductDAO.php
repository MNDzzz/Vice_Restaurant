<?php
require_once __DIR__ . '/BaseDAO.php';
require_once __DIR__ . '/../models/Product.php';

class ProductDAO extends BaseDAO
{
    protected $table = 'products';

    public function getAll()
    {
        $stmt = $this->db->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC");

        $products = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $products[] = new Product($row);
        }
        return $products;
    }

    public function getById($id)
    {
        $data = $this->findById($id);
        return $data ? new Product($data) : null;
    }

    public function getByCategoryId($categoryId)
    {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE category_id = ? ORDER BY id ASC");
        $stmt->execute([$categoryId]);

        $products = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $products[] = new Product($row);
        }
        return $products;
    }

    public function create(Product $product)
    {
        $stmt = $this->db->prepare("INSERT INTO products (name, description, price, image, category_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $product->getName(),
            $product->getDescription(),
            $product->getPrice(),
            $product->getImage(),
            $product->getCategoryId()
        ]);
        $product->setId($this->db->lastInsertId());
        return $product;
    }

    public function update(Product $product)
    {
        $stmt = $this->db->prepare("UPDATE products SET name = ?, description = ?, price = ?, image = ?, category_id = ? WHERE id = ?");
        return $stmt->execute([
            $product->getName(),
            $product->getDescription(),
            $product->getPrice(),
            $product->getImage(),
            $product->getCategoryId(),
            $product->getId()
        ]);
    }

    public function delete($id)
    {
        try {
            $this->db->beginTransaction();

            // Eliminar referencias en order_items (Historial de pedidos)
            $stmt = $this->db->prepare("DELETE FROM order_items WHERE product_id = ?");
            $stmt->execute([$id]);

            // Eliminar el producto
            $result = $this->deleteById($id);

            $this->db->commit();
            return $result;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
?>
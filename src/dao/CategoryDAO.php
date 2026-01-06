<?php
require_once __DIR__ . '/BaseDAO.php';
require_once __DIR__ . '/../models/Category.php';

class CategoryDAO extends BaseDAO {
    protected $table = 'categories';

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM categories ORDER BY id ASC");
        
        $categories = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categories[] = new Category($row);
        }
        return $categories;
    }

    public function getById($id) {
        $data = $this->findById($id);
        return $data ? new Category($data) : null;
    }

    public function create(Category $category) {
        $stmt = $this->db->prepare("INSERT INTO categories (name, image) VALUES (?, ?)");
        $stmt->execute([
            $category->getName(),
            $category->getImage()
        ]);
        $category->setId($this->db->lastInsertId());
        return $category;
    }

    public function update(Category $category) {
        $stmt = $this->db->prepare("UPDATE categories SET name = ?, image = ? WHERE id = ?");
        return $stmt->execute([
            $category->getName(),
            $category->getImage(),
            $category->getId()
        ]);
    }

    public function delete($id) {
        return $this->deleteById($id);
    }
}
?>

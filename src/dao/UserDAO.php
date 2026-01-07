<?php
require_once __DIR__ . '/BaseDAO.php';
require_once __DIR__ . '/../models/User.php';

class UserDAO extends BaseDAO
{
    protected $table = 'users';

    public function getAll($roleFilter = null)
    {
        if ($roleFilter) {
            $stmt = $this->db->prepare("SELECT id, name, email, role, is_active, created_at FROM users WHERE role = ? ORDER BY id ASC");
            $stmt->execute([$roleFilter]);
        } else {
            $stmt = $this->db->query("SELECT id, name, email, role, is_active, created_at FROM users ORDER BY role DESC, id ASC");
        }

        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new User($row);
        }
        return $users;
    }

    public function getById($id)
    {
        $data = $this->findById($id);
        return $data ? new User($data) : null;
    }

    public function getByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new User($data) : null;
    }

    public function create(User $user)
    {
        $stmt = $this->db->prepare("INSERT INTO users (name, email, password, role, is_active) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $user->getName(),
            $user->getEmail(),
            $user->getPassword(),
            $user->getRole(),
            $user->isActive()
        ]);
        $user->setId($this->db->lastInsertId());
        return $user;
    }

    public function update(User $user)
    {
        if ($user->getPassword()) {
            // Actualizo con contraseña
            $stmt = $this->db->prepare("UPDATE users SET name = ?, email = ?, password = ?, role = ?, is_active = ? WHERE id = ?");
            $stmt->execute([
                $user->getName(),
                $user->getEmail(),
                $user->getPassword(),
                $user->getRole(),
                $user->isActive(),
                $user->getId()
            ]);
        } else {
            // Actualizo sin contraseña
            $stmt = $this->db->prepare("UPDATE users SET name = ?, email = ?, role = ?, is_active = ? WHERE id = ?");
            $stmt->execute([
                $user->getName(),
                $user->getEmail(),
                $user->getRole(),
                $user->isActive(),
                $user->getId()
            ]);
        }
        return $stmt->rowCount() > 0;
    }

    public function delete($id)
    {
        try {
            $this->db->beginTransaction();

            // 1. Obtener IDs de pedidos del usuario
            $stmt = $this->db->prepare("SELECT id FROM orders WHERE user_id = ?");
            $stmt->execute([$id]);
            $orderIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

            if (!empty($orderIds)) {
                // 2. Eliminar items de esos pedidos
                $inQuery = implode(',', array_fill(0, count($orderIds), '?'));
                $stmt = $this->db->prepare("DELETE FROM order_items WHERE order_id IN ($inQuery)");
                $stmt->execute($orderIds);

                // 3. Eliminar los pedidos
                $stmt = $this->db->prepare("DELETE FROM orders WHERE user_id = ?");
                $stmt->execute([$id]);
            }

            // 4. Eliminar el usuario
            $result = $this->deleteById($id);

            $this->db->commit();
            return $result;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function toggleActive($id)
    {
        $stmt = $this->db->prepare("UPDATE users SET is_active = NOT is_active WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function toggleRole($id)
    {
        $stmt = $this->db->prepare("UPDATE users SET role = CASE WHEN role = 'admin' THEN 'user' ELSE 'admin' END WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function updateRole($id, $newRole)
    {
        $stmt = $this->db->prepare("UPDATE users SET role = ? WHERE id = ?");
        return $stmt->execute([$newRole, $id]);
    }
}
?>
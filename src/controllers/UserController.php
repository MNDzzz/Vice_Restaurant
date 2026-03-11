<?php

/**
 * UserController
 * Gestiona el CRUD de usuarios desde el panel de administración.
 * Incluye control de permisos según el rol del usuario en sesión.
 */
class UserController
{
    /**
     * Verifica si el usuario actual puede gestionar al usuario objetivo.
     */
    private function puedeGestionar(string $rolActual, string $rolObjetivo): bool
    {
        if ($rolActual === 'superadmin') {
            return true;
        }
        if ($rolActual === 'admin' && $rolObjetivo === 'user') {
            return true;
        }
        return false;
    }

    /**
     * Devuelve todos los usuarios (filtrados según el rol del usuario en sesión).
     */
    public function getAll(): void
    {
        $rolActual = $_SESSION['user_role'] ?? 'guest';

        try {
            $userDAO = new UserDAO();

            if ($rolActual === 'superadmin') {
                $usuarios = $userDAO->getAll();
            } elseif ($rolActual === 'admin') {
                $usuarios = $userDAO->getAll('user');
            } else {
                echo json_encode([]);
                return;
            }

            $resultado = array_map(fn($u) => $u->toArray(), $usuarios);
            echo json_encode($resultado);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Devuelve un usuario por su ID (con verificación de permisos).
     */
    public function getById(): void
    {
        $rolActual = $_SESSION['user_role'] ?? 'guest';

        try {
            $id      = $_GET['id'] ?? 0;
            $userDAO = new UserDAO();
            $usuario = $userDAO->getById($id);

            if ($usuario && $this->puedeGestionar($rolActual, $usuario->getRole())) {
                echo json_encode($usuario->toArray());
            } else {
                echo json_encode(['error' => 'No autorizado']);
            }
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Crea un nuevo usuario desde el panel de administración.
     */
    public function store(): void
    {
        $rolActual = $_SESSION['user_role'] ?? 'guest';
        $datos     = json_decode(file_get_contents('php://input'), true);

        if (!isset($datos['name'], $datos['email'], $datos['password'])) {
            echo json_encode(['success' => false, 'error' => 'Faltan campos requeridos']);
            return;
        }

        $rol = $datos['role'] ?? 'user';

        // Evito que un admin cree administradores
        if ($rolActual === 'admin' && $rol !== 'user') {
            echo json_encode(['success' => false, 'error' => 'No tienes permisos para crear administradores']);
            return;
        }

        try {
            $usuario = new User([
                'name'      => $datos['name'],
                'email'     => $datos['email'],
                'password'  => password_hash($datos['password'], PASSWORD_DEFAULT),
                'role'      => $rol,
                'is_active' => $datos['is_active'] ?? 1,
            ]);

            $userDAO       = new UserDAO();
            $usuarioCreado = $userDAO->create($usuario);

            echo json_encode(['success' => true, 'id' => $usuarioCreado->getId()]);
        } catch (Exception $e) {
            if (str_contains($e->getMessage(), 'Duplicate')) {
                echo json_encode(['success' => false, 'error' => 'El email ya está registrado']);
            } else {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        }
    }

    /**
     * Actualiza los datos de un usuario existente.
     */
    public function update(): void
    {
        $rolActual = $_SESSION['user_role'] ?? 'guest';
        $datos     = json_decode(file_get_contents('php://input'), true);

        if (!isset($datos['id'], $datos['name'], $datos['email'])) {
            echo json_encode(['success' => false, 'error' => 'Faltan campos requeridos']);
            return;
        }

        try {
            $userDAO       = new UserDAO();
            $usuarioObjetivo = $userDAO->getById($datos['id']);

            if (!$usuarioObjetivo || !$this->puedeGestionar($rolActual, $usuarioObjetivo->getRole())) {
                echo json_encode(['success' => false, 'error' => 'No tienes permisos para editar este usuario']);
                return;
            }

            // Evito que un admin escale privilegios
            $nuevoRol = $datos['role'] ?? $usuarioObjetivo->getRole();
            if ($rolActual === 'admin' && $nuevoRol !== 'user') {
                $nuevoRol = 'user';
            }

            $usuario = new User([
                'id'        => $datos['id'],
                'name'      => $datos['name'],
                'email'     => $datos['email'],
                'password'  => !empty($datos['password']) ? password_hash($datos['password'], PASSWORD_DEFAULT) : null,
                'role'      => $nuevoRol,
                'is_active' => $datos['is_active'] ?? 1,
            ]);

            $exito = $userDAO->update($usuario);
            echo json_encode(['success' => $exito]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Elimina un usuario (con protección de auto-eliminación).
     */
    public function destroy(): void
    {
        $rolActual    = $_SESSION['user_role'] ?? 'guest';
        $usuarioActualId = $_SESSION['user_id'] ?? 0;
        $datos        = json_decode(file_get_contents('php://input'), true);

        if (!isset($datos['id'])) {
            echo json_encode(['success' => false, 'error' => 'Falta el ID']);
            return;
        }

        // Evito que el usuario se elimine a sí mismo
        if ($datos['id'] == $usuarioActualId) {
            echo json_encode(['success' => false, 'error' => 'No puedes eliminarte a ti mismo']);
            return;
        }

        try {
            $userDAO         = new UserDAO();
            $usuarioObjetivo = $userDAO->getById($datos['id']);

            if (!$usuarioObjetivo || !$this->puedeGestionar($rolActual, $usuarioObjetivo->getRole())) {
                echo json_encode(['success' => false, 'error' => 'No tienes permisos para eliminar este usuario']);
                return;
            }

            $exito = $userDAO->delete($datos['id']);
            echo json_encode(['success' => $exito]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Alterna el rol de un usuario entre 'admin' y 'user'.
     */
    public function toggleAdmin(): void
    {
        $rolActual = $_SESSION['user_role'] ?? 'guest';

        if ($rolActual !== 'superadmin' && $rolActual !== 'admin') {
            echo json_encode(['success' => false, 'error' => 'No tienes permisos para cambiar roles']);
            return;
        }

        $datos = json_decode(file_get_contents('php://input'), true);

        if (!isset($datos['id'])) {
            echo json_encode(['success' => false, 'error' => 'Falta el ID']);
            return;
        }

        try {
            $userDAO = new UserDAO();
            $usuario = $userDAO->getById($datos['id']);

            if ($usuario) {
                $nuevoRol = $usuario->getRole() === 'admin' ? 'user' : 'admin';
                $userDAO->updateRole($datos['id'], $nuevoRol);
                echo json_encode(['success' => true, 'newRole' => $nuevoRol]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Alterna el estado activo/inactivo de un usuario.
     */
    public function toggleActive(): void
    {
        $rolActual = $_SESSION['user_role'] ?? 'guest';
        $datos     = json_decode(file_get_contents('php://input'), true);

        if (!isset($datos['id'])) {
            echo json_encode(['success' => false, 'error' => 'Falta el ID']);
            return;
        }

        try {
            $userDAO = new UserDAO();
            $usuario = $userDAO->getById($datos['id']);

            if (!$usuario || !$this->puedeGestionar($rolActual, $usuario->getRole())) {
                echo json_encode(['success' => false, 'error' => 'No tienes permisos']);
                return;
            }

            $userDAO->toggleActive($datos['id']);
            $usuarioActualizado = $userDAO->getById($datos['id']);

            echo json_encode(['success' => true, 'is_active' => $usuarioActualizado->isActive()]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}

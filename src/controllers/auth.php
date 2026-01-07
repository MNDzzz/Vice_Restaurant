<?php
session_start();
require_once __DIR__ . '/../config/DB.php';
require_once __DIR__ . '/../dao/UserDAO.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'login') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            header('Location: ../index.php?view=login&error=empty');
            exit;
        }

        try {
            $userDAO = new UserDAO();
            $user = $userDAO->getByEmail($email);

            if ($user && password_verify($password, $user->getPassword())) {
                // Verifico si el usuario está activo
                if (!$user->isActive()) {
                    header('Location: ../index.php?view=login&error=inactive');
                    exit;
                }

                // Establezco las variables de sesión
                $_SESSION['user_id'] = $user->getId();
                $_SESSION['user_name'] = $user->getName();
                $_SESSION['user_email'] = $user->getEmail();
                $_SESSION['user_role'] = $user->getRole();

                // Redirijo según el rol
                if ($user->isAdmin()) {
                    header('Location: ../index.php?view=admin');
                } else {
                    header('Location: ../index.php?view=home');
                }
                exit;
            } else {
                header('Location: ../index.php?view=login&error=invalid');
                exit;
            }
        } catch (Exception $e) {
            header('Location: ../index.php?view=login&error=system');
            exit;
        }
    }

    if ($action === 'register') {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($name) || empty($email) || empty($password)) {
            header('Location: ../index.php?view=register&error=empty');
            exit;
        }

        try {
            $userDAO = new UserDAO();

            // Verifico si el correo electrónico ya existe
            $existingUser = $userDAO->getByEmail($email);
            if ($existingUser) {
                header('Location: ../index.php?view=register&error=exists');
                exit;
            }

            // Creo un nuevo usuario
            $user = new User([
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => 'user',
                'is_active' => 1
            ]);

            $createdUser = $userDAO->create($user);

            // Inicio sesión automáticamente tras el registro
            $_SESSION['user_id'] = $createdUser->getId();
            $_SESSION['user_name'] = $createdUser->getName();
            $_SESSION['user_email'] = $createdUser->getEmail();
            $_SESSION['user_role'] = $createdUser->getRole();

            header('Location: ../index.php?view=home');
            exit;
        } catch (Exception $e) {
            header('Location: ../index.php?view=register&error=system');
            exit;
        }
    }

    if ($action === 'logout') {
        session_destroy();
        header('Location: ../index.php?view=home');
        exit;
    }

    if ($action === 'update_profile') {
        // Verifico si el usuario está logueado
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../index.php?view=login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($name) || empty($email)) {
            header('Location: ../index.php?view=perfil&error=empty');
            exit;
        }

        try {
            $userDAO = new UserDAO();

            // Verifico si el email ya existe en otro usuario (si lo ha cambiado)
            if ($email !== $_SESSION['user_email']) {
                $existingUser = $userDAO->getByEmail($email);
                if ($existingUser && $existingUser->getId() != $userId) {
                    header('Location: ../index.php?view=perfil&error=email_exists');
                    exit;
                }
            }

            // Preparo el objeto usuario para actualizar
            // (La contraseña solo se hashea si no está vacía, UserDAO maneja esto)
            $user = new User([
                'id' => $userId,
                'name' => $name,
                'email' => $email,
                'password' => !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : null,
                'role' => $_SESSION['user_role'], // Mantenemos rol actual
                'is_active' => 1 // Mantenemos activo
            ]);

            $userDAO->update($user);

            // Actualizo la sesión con los nuevos datos
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;

            // TODO: LOG (Necesitaría importar api.php o LogDAO aquí, pero auth.php es independiente)
            // Por simplicidad en auth, no estamos logueando la auditoría de cambio de perfil,
            // pero podríamos añadir "require_once dao/LogDAO.php" si fuera crítico.

            header('Location: ../index.php?view=perfil&success=updated');
            exit;

        } catch (Exception $e) {
            header('Location: ../index.php?view=perfil&error=system');
            exit;
        }
    }
}

// Si no hay una acción válida, redirijo al inicio
header('Location: ../index.php?view=home');
exit;
?>
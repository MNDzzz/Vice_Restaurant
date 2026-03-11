<?php

/**
 * AuthController
 * Gestiona el registro, login, logout y actualización de perfil.
 */
class AuthController
{
    /**
     * Procesa el inicio de sesión del usuario.
     */
    public function login(): void
    {
        $email    = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            header('Location: ../index.php?view=login&error=empty');
            exit;
        }

        try {
            $userDAO = new UserDAO();
            $user    = $userDAO->getByEmail($email);

            if ($user && password_verify($password, $user->getPassword())) {
                // Verifico si el usuario está activo
                if (!$user->isActive()) {
                    header('Location: ../index.php?view=login&error=inactive');
                    exit;
                }

                // Establezco las variables de sesión
                $_SESSION['user_id']    = $user->getId();
                $_SESSION['user_name']  = $user->getName();
                $_SESSION['user_email'] = $user->getEmail();
                $_SESSION['user_role']  = $user->getRole();

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

    /**
     * Registra un nuevo usuario en el sistema.
     */
    public function register(): void
    {
        $name     = $_POST['name'] ?? '';
        $email    = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($name) || empty($email) || empty($password)) {
            header('Location: ../index.php?view=register&error=empty');
            exit;
        }

        try {
            $userDAO = new UserDAO();

            // Verifico si el correo electrónico ya existe
            $usuarioExistente = $userDAO->getByEmail($email);
            if ($usuarioExistente) {
                header('Location: ../index.php?view=register&error=exists');
                exit;
            }

            // Creo un nuevo usuario con contraseña hasheada
            $user = new User([
                'name'      => $name,
                'email'     => $email,
                'password'  => password_hash($password, PASSWORD_DEFAULT),
                'role'      => 'user',
                'is_active' => 1,
            ]);

            $usuarioCreado = $userDAO->create($user);

            // Inicio sesión automáticamente tras el registro
            $_SESSION['user_id']    = $usuarioCreado->getId();
            $_SESSION['user_name']  = $usuarioCreado->getName();
            $_SESSION['user_email'] = $usuarioCreado->getEmail();
            $_SESSION['user_role']  = $usuarioCreado->getRole();

            header('Location: ../index.php?view=home');
            exit;
        } catch (Exception $e) {
            header('Location: ../index.php?view=register&error=system');
            exit;
        }
    }

    /**
     * Destruye la sesión y redirige al inicio.
     */
    public function logout(): void
    {
        session_destroy();
        header('Location: ../index.php?view=home');
        exit;
    }

    /**
     * Actualiza los datos del perfil del usuario autenticado.
     */
    public function updateProfile(): void
    {
        // Verifico si el usuario está logueado
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../index.php?view=login');
            exit;
        }

        $userId   = $_SESSION['user_id'];
        $name     = $_POST['name'] ?? '';
        $email    = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($name) || empty($email)) {
            header('Location: ../index.php?view=perfil&error=empty');
            exit;
        }

        try {
            $userDAO = new UserDAO();

            // Verifico si el email ya existe en otro usuario
            if ($email !== $_SESSION['user_email']) {
                $usuarioExistente = $userDAO->getByEmail($email);
                if ($usuarioExistente && $usuarioExistente->getId() != $userId) {
                    header('Location: ../index.php?view=perfil&error=email_exists');
                    exit;
                }
            }

            // Preparo el objeto usuario para actualizar
            $user = new User([
                'id'        => $userId,
                'name'      => $name,
                'email'     => $email,
                'password'  => !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : null,
                'role'      => $_SESSION['user_role'],
                'is_active' => 1,
            ]);

            $userDAO->update($user);

            // Actualizo la sesión con los nuevos datos
            $_SESSION['user_name']  = $name;
            $_SESSION['user_email'] = $email;

            header('Location: ../index.php?view=perfil&success=updated');
            exit;
        } catch (Exception $e) {
            header('Location: ../index.php?view=perfil&error=system');
            exit;
        }
    }
}

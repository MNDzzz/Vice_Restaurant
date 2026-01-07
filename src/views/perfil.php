<?php
// Protejo la vista: solo usuarios logueados
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?view=login');
    exit;
}

// Obtengo datos de usuario de la sesión para pre-rellenar el formulario
$userName = $_SESSION['user_name'] ?? '';
$userEmail = $_SESSION['user_email'] ?? '';

// Compruebo si hay mensajes de error o éxito
$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <h1 class="text-center mb-4">MI PERFIL</h1>

            <?php if ($success === 'updated'): ?>
                <div class="alert alert-success text-center">
                    ✅ Perfil actualizado correctamente
                </div>
            <?php endif; ?>

            <?php if ($error === 'email_exists'): ?>
                <div class="alert alert-danger text-center">
                    ❌ El email ya está en uso por otro usuario
                </div>
            <?php elseif ($error === 'system'): ?>
                <div class="alert alert-danger text-center">
                    ❌ Error del sistema. Inténtalo más tarde
                </div>
            <?php endif; ?>

            <div class="card p-4"
                style="background-color: var(--color-bg); border: 2px solid var(--color-primary); box-shadow: 0 0 20px rgba(255, 0, 255, 0.2);">
                <div class="card-body">
                    <form action="controllers/auth.php" method="POST">
                        <input type="hidden" name="action" value="update_profile">

                        <!-- Nombre -->
                        <div class="mb-3">
                            <label for="name" class="form-label text-primary-custom">Nombre</label>
                            <input type="text" class="form-control vice-input" id="name" name="name"
                                value="<?php echo htmlspecialchars($userName); ?>" required>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label text-primary-custom">Email</label>
                            <input type="email" class="form-control vice-input" id="email" name="email"
                                value="<?php echo htmlspecialchars($userEmail); ?>" required>
                        </div>

                        <!-- Contraseña (Opcional) -->
                        <div class="mb-4">
                            <label for="password" class="form-label text-primary-custom">
                                Nueva Contraseña <small class="text-muted text-white-50">(Opcional)</small>
                            </label>
                            <input type="password" class="form-control vice-input" id="password" name="password"
                                placeholder="Dejar vacío para mantener la actual">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary d-block fw-bold py-2">
                                GUARDAR CAMBIOS
                            </button>
                            <a href="index.php?view=home" class="btn btn-outline-light d-block">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Botón de Logout -->
            <div class="text-center mt-4">
                <form action="controllers/auth.php" method="POST">
                    <input type="hidden" name="action" value="logout">
                    <button type="submit" class="btn btn-link text-danger text-decoration-none">
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .vice-input {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid var(--color-secondary);
        color: #fff;
    }

    .vice-input:focus {
        background: rgba(255, 255, 255, 0.2);
        border-color: var(--color-primary);
        color: #fff;
        box-shadow: 0 0 10px rgba(255, 0, 255, 0.3);
    }

    .text-primary-custom {
        color: var(--color-primary);
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
</style>
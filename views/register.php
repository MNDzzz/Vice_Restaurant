<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card p-4">
                <h2 class="text-center mb-4">Únete al Club</h2>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">El email ya está registrado.</div>
                <?php endif; ?>

                <form action="controllers/auth.php" method="POST">
                    <input type="hidden" name="action" value="register">
                    <div class="mb-3">
                        <label class="form-label">Nombre Completo</label>
                        <input type="text" name="name" class="form-control bg-secondary text-white border-0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control bg-secondary text-white border-0" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="password" class="form-control bg-secondary text-white border-0"
                            required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Registrarse</button>
                </form>
                <div class="text-center mt-3">
                    <a href="index.php?view=login" class="text-secondary-custom">¿Ya tienes cuenta? Inicia sesión</a>
                </div>
            </div>
        </div>
    </div>
</div>
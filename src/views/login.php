<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card p-4">
                <h2 class="text-center mb-4">Iniciar Sesión</h2>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">Credenciales incorrectas.</div>
                <?php endif; ?>

                <form action="controllers/auth.php" method="POST">
                    <input type="hidden" name="action" value="login">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control bg-secondary text-white border-0" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="password" class="form-control bg-secondary text-white border-0"
                            required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Entrar</button>
                </form>
                <div class="text-center mt-3">
                    <a href="index.php?view=register" class="text-secondary-custom">¿No tienes cuenta? Regístrate</a>
                </div>
            </div>
        </div>
    </div>
</div>
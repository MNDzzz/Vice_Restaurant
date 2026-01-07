<?php
// Obtengo el rol del usuario para los permisos de la interfaz
$userRole = $_SESSION['user_role'] ?? 'user';
$isSuperAdmin = $userRole === 'superadmin';
?>
<div class="container-fluid py-4">
    <div class="row">
        <!-- Barra lateral -->
        <div class="col-md-3 col-lg-2">
            <div class="card bg-dark">
                <div class="card-header text-center">
                    <h5 class="mb-0 text-primary-custom">Panel Admin</h5>
                    <small class="text-muted">
                        <?php echo $isSuperAdmin ? '👑 Super Admin' : '🔧 Admin'; ?>
                    </small>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <button class="list-group-item list-group-item-action bg-dark text-light active"
                            onclick="app.loadSection('dashboard')" id="btn-dashboard">
                            📊 Dashboard
                        </button>
                        <button class="list-group-item list-group-item-action bg-dark text-light"
                            onclick="app.loadSection('users')" id="btn-users">
                            👥 Usuarios
                        </button>
                        <button class="list-group-item list-group-item-action bg-dark text-light"
                            onclick="app.loadSection('orders')" id="btn-orders">
                            📦 Pedidos
                        </button>
                        <button class="list-group-item list-group-item-action bg-dark text-light"
                            onclick="app.loadSection('products')" id="btn-products">
                            🍔 Productos
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido principal -->
        <div class="col-md-9 col-lg-10">
            <div class="card bg-dark">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 id="section-title" class="mb-0">Dashboard</h4>
                    <span class="badge bg-secondary"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                </div>
                <div class="card-body" id="admin-content">
                    <!-- El contenido dinámico se carga aquí -->
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de usuario -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header border-secondary">
                <h5 class="modal-title" id="userModalTitle">Nuevo Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="userForm">
                <div class="modal-body">
                    <input type="hidden" id="userId">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control bg-secondary text-white border-0" id="userName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control bg-secondary text-white border-0" id="userEmail"
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña <small class="text-muted">(dejar vacío para no
                                cambiar)</small></label>
                        <input type="password" class="form-control bg-secondary text-white border-0" id="userPassword">
                    </div>
                    <?php if ($isSuperAdmin): ?>
                        <div class="mb-3">
                            <label class="form-label">Rol</label>
                            <select class="form-select bg-secondary text-white border-0" id="userRole">
                                <option value="user">Usuario</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    <?php else: ?>
                        <input type="hidden" id="userRole" value="user">
                    <?php endif; ?>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="userActive" checked>
                            <label class="form-check-label" for="userActive">Cuenta Activa</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de producto -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header border-secondary">
                <h5 class="modal-title" id="productModalTitle">Nuevo Producto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="productForm">
                <div class="modal-body">
                    <input type="hidden" id="prodId">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control bg-secondary text-white border-0" id="prodName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control bg-secondary text-white border-0" id="prodDescription"
                            rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Precio (€)</label>
                        <input type="number" step="0.01" min="0" class="form-control bg-secondary text-white border-0"
                            id="prodPrice" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Imagen URL</label>
                        <input type="text" class="form-control bg-secondary text-white border-0" id="prodImage"
                            placeholder="img/producto.jpg">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Categoría</label>
                        <select class="form-select bg-secondary text-white border-0" id="prodCategory">
                            <option value="">Sin categoría</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de detalles del pedido -->
<div class="modal fade" id="orderModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header border-secondary">
                <h5 class="modal-title">Detalles del Pedido #<span id="orderIdDisplay"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="orderDetails">
                <!-- Los detalles del pedido se cargan aquí -->
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación Genérico -->
<div class="modal fade" id="confirmationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-light border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title" id="confirmTitle">Confirmación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="confirmMessage">
                ¿Estás seguro de realizar esta acción?
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmBtn">Confirmar</button>
            </div>
        </div>
    </div>
</div>


<!-- Paso el rol de usuario a JavaScript -->
<script>
        const USER_ROLE = '<?php echo $userRole; ?>';
        const IS_SUPERADMIN = <?php echo $isSuperAdmin ? 'true' : 'false'; ?>;
</script>
<script src="assets/js/admin.js"></script>
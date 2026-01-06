<?php
require_once __DIR__ . '/../config/DB.php';
require_once __DIR__ . '/../services/CurrencyService.php';
$db = DB::getInstance()->getConnection();

// Redirijo si no ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?view=login');
    exit;
}

$userId = $_SESSION['user_id'];

// Obtengo todos los pedidos del usuario
$stmt = $db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container py-5">
    <h1 class="text-center mb-5">MIS PEDIDOS</h1>

    <?php if (count($orders) === 0): ?>
        <!-- Estado vacío -->
        <div class="text-center py-5">
            <div style="font-size: 72px; opacity: 0.3;">🍽️</div>
            <h3 class="mt-4 mb-3">No tienes pedidos aún</h3>
            <p class="text-muted mb-4">¡Empieza a ordenar tus platos favoritos!</p>
            <a href="index.php?view=pedir" class="btn btn-primary btn-lg">Hacer un Pedido</a>
        </div>
    <?php else: ?>
        <!-- Lista de pedidos -->
        <div class="orders-container">
            <?php foreach ($orders as $order): ?>
                <?php
                // Obtengo los artículos del pedido
                $stmt_items = $db->prepare("
                    SELECT oi.*, p.name, p.image 
                    FROM order_items oi 
                    LEFT JOIN products p ON oi.product_id = p.id 
                    WHERE oi.order_id = ?
                ");
                $stmt_items->execute([$order['id']]);
                $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

                // Insignia de estado
                $statusClass = [
                    'pending' => 'bg-warning text-dark',
                    'completed' => 'bg-success',
                    'cancelled' => 'bg-secondary'
                ];
                $statusText = [
                    'pending' => 'Pendiente',
                    'completed' => 'Completado',
                    'cancelled' => 'Cancelado'
                ];
                ?>

                <div class="order-card mb-4" data-order-id="<?php echo $order['id']; ?>">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Pedido #<?php echo $order['id']; ?></strong>
                                <small class="text-muted ms-2">
                                    <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                                </small>
                            </div>
                            <span class="badge <?php echo $statusClass[$order['status']]; ?> px-3 py-2">
                                <?php echo $statusText[$order['status']]; ?>
                            </span>
                        </div>

                        <div class="card-body">
                            <!-- Artículos del pedido -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <h6 class="mb-3">Productos:</h6>
                                    <?php foreach ($items as $item): ?>
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="flex-grow-1">
                                                <strong><?php echo htmlspecialchars($item['name'] ?? 'Producto eliminado'); ?></strong>
                                                <span class="text-muted">× <?php echo $item['quantity']; ?></span>
                                            </div>
                                            <div>
                                                <?php echo CurrencyService::format($item['price'] * $item['quantity']); ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <strong>Total:</strong>
                                        <strong
                                            class="text-primary"><?php echo CurrencyService::format($order['total']); ?></strong>
                                    </div>
                                </div>
                            </div>

                            <!-- Información de entrega -->
                            <?php if ($order['delivery_address'] || $order['delivery_phone']): ?>
                                <div class="delivery-info mb-3 p-3" style="background: rgba(255,255,255,0.05); border-radius: 8px;">
                                    <h6 class="mb-2">📍 Información de entrega:</h6>
                                    <?php if ($order['delivery_address']): ?>
                                        <div><strong>Dirección:</strong> <?php echo htmlspecialchars($order['delivery_address']); ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($order['delivery_phone']): ?>
                                        <div><strong>Teléfono:</strong> <?php echo htmlspecialchars($order['delivery_phone']); ?></div>
                                    <?php endif; ?>
                                    <?php if ($order['delivery_notes']): ?>
                                        <div><strong>Notas:</strong> <?php echo htmlspecialchars($order['delivery_notes']); ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Acciones del pedido -->
                            <div class="order-actions d-flex gap-2 flex-wrap">
                                <!-- Botón de volver a pedir (siempre disponible) -->
                                <button class="btn btn-outline-light btn-sm reorder-btn"
                                    data-order-id="<?php echo $order['id']; ?>">
                                    🔄 Volver a Pedir
                                </button>

                                <?php if ($order['status'] === 'pending'): ?>
                                    <!-- Editar entrega (solo para pendientes) -->
                                    <button class="btn btn-outline-info btn-sm edit-delivery-btn"
                                        data-order-id="<?php echo $order['id']; ?>"
                                        data-address="<?php echo htmlspecialchars($order['delivery_address'] ?? ''); ?>"
                                        data-phone="<?php echo htmlspecialchars($order['delivery_phone'] ?? ''); ?>"
                                        data-notes="<?php echo htmlspecialchars($order['delivery_notes'] ?? ''); ?>">
                                        ✏️ Editar Entrega
                                    </button>

                                    <!-- Cancelar pedido (solo para pendientes) -->
                                    <button class="btn btn-outline-danger btn-sm cancel-order-btn"
                                        data-order-id="<?php echo $order['id']; ?>">
                                        ❌ Cancelar Pedido
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal de edición de entrega -->
<div class="modal fade" id="editDeliveryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background: var(--color-bg); border: 2px solid var(--color-primary);">
            <div class="modal-header">
                <h5 class="modal-title">Editar Información de Entrega</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editDeliveryForm">
                    <input type="hidden" id="edit_order_id" name="order_id">

                    <div class="mb-3">
                        <label for="edit_address" class="form-label">Dirección de Entrega</label>
                        <input type="text" class="form-control" id="edit_address" name="delivery_address" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_phone" class="form-label">Teléfono</label>
                        <input type="tel" class="form-control" id="edit_phone" name="delivery_phone" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_notes" class="form-label">Notas adicionales</label>
                        <textarea class="form-control" id="edit_notes" name="delivery_notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="saveDeliveryBtn">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<style>
    .order-card .card {
        background: rgba(0, 0, 0, 0.6);
        border: 1px solid var(--color-secondary);
        color: var(--color-text);
        transition: all 0.3s ease;
    }

    .order-card .card:hover {
        border-color: var(--color-primary);
        box-shadow: 0 0 20px rgba(255, 176, 196, 0.2);
    }

    .order-card .card-header {
        background: rgba(255, 176, 196, 0.1);
        border-bottom: 1px solid var(--color-secondary);
    }

    .form-control {
        background: rgba(0, 0, 0, 0.5);
        border: 1px solid var(--color-secondary);
        color: var(--color-text);
    }

    .form-control:focus {
        background: rgba(0, 0, 0, 0.7);
        border-color: var(--color-primary);
        color: var(--color-text);
    }
</style>

<script src="assets/js/mis-pedidos.js"></script>
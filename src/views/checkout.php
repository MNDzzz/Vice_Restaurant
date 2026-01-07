<?php
if (empty($_SESSION['cart'])) {
    header('Location: index.php?view=pedir');
    exit;
}
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?view=login');
    exit;
}

require_once __DIR__ . '/../services/DiscountService.php';
require_once __DIR__ . '/../services/CurrencyService.php';

$totals = DiscountService::calculateDetails($_SESSION['cart']);
$subtotal = $totals['subtotal'];
$discount = $totals['discount'];
$nombrePromocion = $totals['promo_name'];
$total = $totals['finalTotal']; // Sobrescribimos $total para compatibilidad con el resto del archivo
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h1 class="text-center mb-5">Finalizar Pedido</h1>

            <div class="row">
                <div class="col-md-5 mb-4">
                    <div class="card p-4 h-100">
                        <h3 class="mb-4 text-secondary-custom">Resumen del Pedido</h3>
                        <ul class="list-group list-group-flush bg-transparent">
                            <?php foreach ($_SESSION['cart'] as $item): ?>
                                <li class="list-group-item bg-transparent text-white d-flex justify-content-between">
                                    <span><?php echo $item['quantity']; ?>x
                                        <?php echo htmlspecialchars($item['name']); ?></span>
                                    <span><?php echo CurrencyService::format($item['price'] * $item['quantity']); ?></span>
                                </li>
                            <?php endforeach; ?>

                            <!-- Desglose de Totales -->
                            <li class="list-group-item bg-transparent mt-3 border-top border-secondary">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-white-50">Subtotal</span>
                                    <span class="text-white"><?php echo CurrencyService::format($subtotal); ?></span>
                                </div>
                                <?php if ($discount > 0): ?>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-success">Descuento
                                            (<?php echo htmlspecialchars($nombrePromocion); ?>)</span>
                                        <span class="text-success">-<?php echo CurrencyService::format($discount); ?></span>
                                    </div>
                                <?php endif; ?>
                            </li>

                            <li
                                class="list-group-item bg-transparent text-primary-custom fw-bold d-flex justify-content-between fs-4 border-top border-secondary pt-3">
                                <span>Total a Pagar</span>
                                <span><?php echo CurrencyService::format($total); ?></span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-7 mb-4">
                    <div class="card p-4 h-100">
                        <h3 class="mb-4 text-secondary-custom">Información de Entrega</h3>
                        <form action="controllers/cart_controller.php" method="POST">
                            <input type="hidden" name="action" value="checkout">

                            <div class="mb-3">
                                <label class="form-label">Dirección de Entrega *</label>
                                <textarea class="form-control bg-secondary text-white border-0" name="delivery_address"
                                    rows="3" required
                                    placeholder="Calle, número, piso... (Ej: Calle Vice City, 123, 2º A)"></textarea>
                                <small class="text-muted">Donde quieres recibir tu pedido</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Teléfono de Contacto *</label>
                                <input type="tel" class="form-control bg-secondary text-white border-0"
                                    name="delivery_phone" required placeholder="+34 600 000 000">
                                <small class="text-muted">Para notificaciones de entrega</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notas adicionales (opcional)</label>
                                <textarea class="form-control bg-secondary text-white border-0" name="delivery_notes"
                                    rows="2"
                                    placeholder="Instrucciones especiales: timbre, portero, hora preferida..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Método de Pago *</label>
                                <select class="form-select bg-secondary text-white border-0" name="payment_method"
                                    required>
                                    <option value="card">Tarjeta de Crédito</option>
                                    <option value="cash">Efectivo</option>
                                    <option value="paypal">PayPal</option>
                                </select>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    💳 Confirmar y Pagar <?php echo number_format($total, 2); ?>€
                                </button>
                                <a href="index.php?view=carrito" class="btn btn-outline-light">
                                    ← Volver al Carrito
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control,
    .form-select {
        background: rgba(0, 0, 0, 0.5) !important;
        border: 1px solid var(--color-secondary) !important;
        color: var(--color-text) !important;
    }

    .form-control:focus,
    .form-select:focus {
        background: rgba(0, 0, 0, 0.7) !important;
        border-color: var(--color-primary) !important;
        color: var(--color-text) !important;
        box-shadow: 0 0 0 0.25rem rgba(255, 176, 196, 0.25);
    }

    .form-label {
        color: var(--color-secondary);
        font-weight: 600;
    }

    small.text-muted {
        color: rgba(145, 223, 236, 0.6) !important;
    }
</style>
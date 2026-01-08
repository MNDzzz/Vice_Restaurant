<div class="container py-5">
    <h1 class="text-center mb-5">Tu Pedido</h1>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="text-center">
            <p class="fs-4">Tu carrito está vacío.</p>
            <a href="index.php?view=pedir" class="btn btn-primary">PEDIR YA!</a>
        </div>
    <?php else: ?>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card p-4">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Precio</th>
                                    <th>Cant.</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                require_once __DIR__ . '/../services/DiscountService.php';
                                require_once __DIR__ . '/../services/CurrencyService.php';
                                $detalles = DiscountService::calculateDetails($_SESSION['cart']);

                                foreach ($_SESSION['cart'] as $item):
                                    $subtotal = $item['price'] * $item['quantity'];
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                                        <td><?php echo CurrencyService::format($item['price']); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td><?php echo CurrencyService::format($subtotal); ?></td>
                                        <td>
                                            <form action="controllers/cart_controller.php" method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="remove">
                                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">X</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 pt-3 border-top border-secondary">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fs-5">Subtotal:</span>
                            <span class="fs-5"><?php echo CurrencyService::format($detalles['subtotal']); ?></span>
                        </div>
                        <?php if ($detalles['discount'] > 0): ?>
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span class="fs-5">Descuento (<?php echo htmlspecialchars($detalles['promo_name']); ?>):</span>
                                <span class="fs-5">-<?php echo CurrencyService::format($detalles['discount']); ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <h3 class="text-primary-custom">Total:
                                <?php echo CurrencyService::format($detalles['finalTotal']); ?>
                            </h3>
                            <div>
                                <a href="index.php?view=pedir" class="btn btn-secondary me-2">Seguir Pidiendo</a>
                                <a href="index.php?view=checkout" class="btn btn-primary">Tramitar Pedido</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
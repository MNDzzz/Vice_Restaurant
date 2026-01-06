<div class="container py-5">
    <h1 class="text-center mb-5">Tu Pedido</h1>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="text-center">
            <p class="fs-4">Tu carrito está vacío.</p>
            <a href="index.php?view=carta" class="btn btn-primary">Ir a la Carta</a>
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
                                $total = 0;
                                foreach ($_SESSION['cart'] as $item):
                                    $subtotal = $item['price'] * $item['quantity'];
                                    $total += $subtotal;
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                                        <td><?php echo number_format($item['price'], 2); ?>€</td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td><?php echo number_format($subtotal, 2); ?>€</td>
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

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top border-secondary">
                        <h3 class="text-primary-custom">Total: <?php echo number_format($total, 2); ?>€</h3>
                        <div>
                            <a href="index.php?view=carta" class="btn btn-secondary me-2">Seguir Pidiendo</a>
                            <a href="index.php?view=checkout" class="btn btn-primary">Tramitar Pedido</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
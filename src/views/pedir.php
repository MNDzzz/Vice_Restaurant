<?php
require_once __DIR__ . '/../config/DB.php';
$db = DB::getInstance()->getConnection();

// Obtengo las categorías
$stmt_cat = $db->query("SELECT * FROM categories");
$categories = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);

// Obtengo todos los productos (para filtrar por JS)
$stmt_prod = $db->query("SELECT * FROM products");
$all_products = $stmt_prod->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container container-fluid py-5">
    <div class="row h-100 position-relative">
        <!-- Columna de navegación lateral -->
        <div class="col-md-3 d-flex flex-column mb-4 mb-md-0 position-relative" style="z-index: 20;">
            <h1 class="mb-4 text-start" style="font-size: 3rem; margin-left: 10px;">Pedidos</h1>

            <!-- Botones de categoría -->
            <div class="sidebar-buttons d-flex flex-column gap-3 flex-grow-1">
                <?php foreach ($categories as $index => $cat): ?>
                    <button class="btn sidebar-pill-btn rounded-pill w-100 <?php echo $index === 0 ? 'active' : ''; ?>"
                        data-target="<?php echo $cat['id']; ?>" onclick="filterCategory(this, <?php echo $cat['id']; ?>)">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Botón Mi Pedido (Enlazado al carrito) -->
            <div class="mt-4">
                <a href="index.php?view=carrito"
                    class="btn sidebar-pill-btn rounded-pill w-100 text-decoration-none d-flex justify-content-center align-items-center">
                    Mi Pedido
                </a>
            </div>
        </div>

        <!-- Imagen de neón vertical (Visible en pantallas grandes) -->
        <div class="col-md-1 d-none d-md-flex justify-content-center align-items-center position-relative"
            style="z-index: 0;">
            <div class="vertical-neon-container">
                <img src="assets/img/pedir/its-a-vice.png" alt="#Its a Vice" class="vertical-neon-img">
            </div>
        </div>

        <!-- Cuadrícula de productos / Carrusel -->
        <div class="col-md-8 position-relative" style="z-index: 20;">
            <div class="products-grid-container">
                <div class="row g-4" id="products-grid">
                    <?php foreach ($all_products as $prod): ?>
                        <div class="col-md-6 col-lg-4 product-item" data-category="<?php echo $prod['category_id']; ?>">
                            <div class="card h-100">
                                <?php
                                $img_path = str_replace('views/images/', 'assets/img/', $prod['image']);
                                if (strpos($img_path, 'assets/img/') === false && strpos($img_path, 'http') === false) {
                                    $img_path = 'assets/img/' . $img_path;
                                }
                                ?>
                                <img src="<?php echo htmlspecialchars($img_path); ?>" class="card-img-top"
                                    alt="<?php echo htmlspecialchars($prod['name']); ?>">
                                <div class="card-body d-flex flex-column p-3">
                                    <h5 class="card-title mb-1"><?php echo htmlspecialchars($prod['name']); ?></h5>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <span
                                            class="fs-5 text-primary-custom fw-bold"><?php echo number_format($prod['price'], 2, ',', '.'); ?>$</span>
                                        <button class="btn btn-dark btn-sm px-3" onclick='Cart.add({
                                                    id: <?php echo $prod["id"]; ?>, 
                                                    name: "<?php echo htmlspecialchars($prod["name"], ENT_QUOTES); ?>", 
                                                    price: <?php echo $prod["price"]; ?>, 
                                                    image: "<?php echo htmlspecialchars($img_path, ENT_QUOTES); ?>"
                                                })'>
                                            + Añadir
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Filtro inicial al cargar
    document.addEventListener('DOMContentLoaded', () => {
        const activeBtn = document.querySelector('.sidebar-pill-btn.active');
        if (activeBtn) {
            const catId = activeBtn.getAttribute('data-target');
            filterCategory(activeBtn, catId);
        } else {
            const firstBtn = document.querySelector('.sidebar-pill-btn[data-target]');
            if (firstBtn) {
                const catId = firstBtn.getAttribute('data-target');
                filterCategory(firstBtn, catId);
            }
        }
    });

    function filterCategory(btn, categoryId) {
        // Actualizo botones
        document.querySelectorAll('button.sidebar-pill-btn').forEach(b => {
            b.classList.remove('active');
        });
        if (btn) btn.classList.add('active');

        // Filtro la cuadrícula
        const items = document.querySelectorAll('.product-item');
        let visibleCount = 0;
        const grid = document.getElementById('products-grid');

        // Reseteo limpio de clases de la cuadrícula
        grid.className = 'row g-4';

        items.forEach(item => {
            if (item.getAttribute('data-category') == categoryId) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        // Lógica del carrusel (> 6 elementos)
        if (visibleCount > 6) {
            grid.classList.add('carousel-mode');
            grid.classList.remove('row');
        } else {
            grid.classList.remove('carousel-mode');
            grid.classList.add('row');
        }
    }
</script>
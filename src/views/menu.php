<?php
require_once __DIR__ . '/../config/DB.php';
$db = DB::getInstance()->getConnection();

// Obtengo las categorías y sus productos
$stmt_cat = $db->query("SELECT * FROM categories ORDER BY id ASC");
$categories = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="vice-menu-page" style="background-color: var(--color-bg); padding-bottom: 80px;">

    <!-- Hero del menú -->
    <div class="menu-hero-small"
        style="min-height: 40vh; height: auto; display: flex; align-items: flex-start; justify-content: center; position: relative; padding-top: 0px; padding-bottom: 80px;">
        <img src="assets/img/menu/vice-menu-logo.svg" alt="Vice Menu"
            style="position: relative; z-index: 2; max-width: 90%; width: 750px; margin-top: 0; margin-bottom: 0; filter: drop-shadow(0 0 20px rgba(255, 0, 222, 0.6));">
    </div>

    <!-- Bucle de categorías -->
    <?php foreach ($categories as $cat): ?>
        <?php
        // Obtengo los productos de la categoría
        $stmt_prod = $db->prepare("SELECT * FROM products WHERE category_id = ?");
        $stmt_prod->execute([$cat['id']]);
        $products = $stmt_prod->fetchAll(PDO::FETCH_ASSOC);

        if (count($products) == 0)
            continue;

        // Ruta del banner o fallback
        $bannerImg = strpos($cat['image'], 'img/') === 0 ? 'assets/' . $cat['image'] : 'assets/img/menu/banners/' . basename($cat['image']);
        ?>

        <!-- Banner de sección -->
        <div class="menu-section-banner" id="<?php echo strtolower($cat['name']); ?>"
            style="background-image: url('<?php echo htmlspecialchars($bannerImg); ?>');">
            <div class="menu-section-overlay"></div>
            <div class="menu-section-title reveal-text">
                <?php echo htmlspecialchars($cat['name']); ?>
            </div>
        </div>

        <!-- Productos de la categoría -->
        <div class="editorial-container">
            <?php foreach ($products as $prod): ?>
                <?php
                // Ruta de imagen del producto
        
                $filename = basename($prod['image']);
                $prodImg = strpos($prod['image'], 'img/') === 0 ? 'assets/' . $prod['image'] : 'assets/img/menu/products/' . $filename;

                // Si no existe, busco alternativa
                if (!file_exists(__DIR__ . '/../' . $prodImg)) {
                    // Busco en otra carpeta
                    $fallback = 'assets/img/' . $filename;
                    if (file_exists(__DIR__ . '/../' . $fallback)) {
                        $prodImg = $fallback;
                    } else {
                        // Si tampoco, uso imagen por defecto
                        $prodImg = 'assets/img/default-product.webp';
                    }
                }
                ?>
                <div class="editorial-row reveal-row">
                    <!-- Imagen -->
                    <div class="editorial-image-col">
                        <img src="<?php echo htmlspecialchars($prodImg); ?>" class="editorial-img"
                            alt="<?php echo htmlspecialchars($prod['name']); ?>">
                    </div>
                    <!-- Contenido -->
                    <div class="editorial-content-col">
                        <div class="editorial-title"><?php echo htmlspecialchars($prod['name']); ?></div>
                        <div class="editorial-desc"><?php echo htmlspecialchars($prod['description']); ?></div>
                        <span class="editorial-price"><?php echo number_format($prod['price'], 2); ?>€</span>

                        <!-- Botón pedir -->
                        <button class="btn btn-editorial" onclick='Cart.add({
                                    id: <?php echo $prod["id"]; ?>, 
                                    name: "<?php echo htmlspecialchars($prod["name"], ENT_QUOTES); ?>", 
                                    price: <?php echo $prod["price"]; ?>, 
                                    image: "<?php echo htmlspecialchars($prodImg, ENT_QUOTES); ?>"
                                })'>
                            PEDIR
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php endforeach; ?>

</div>

<!-- Animaciones GSAP -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        gsap.registerPlugin(ScrollTrigger);

        // Animo el título
        gsap.from('.menu-title-main', {
            duration: 1.5,
            y: 50,
            opacity: 0,
            ease: "power4.out"
        });

        // Animo los títulos de sección
        gsap.utils.toArray('.reveal-text').forEach(text => {
            gsap.from(text, {
                scrollTrigger: {
                    trigger: text,
                    start: "top 80%",
                    toggleActions: "play none none reverse"
                },
                y: 50,
                opacity: 0,
                duration: 1.2,
                ease: "power3.out"
            });
        });

        // Animo las filas de productos
        gsap.utils.toArray('.reveal-row').forEach(row => {
            gsap.to(row, {
                scrollTrigger: {
                    trigger: row,
                    start: "top 75%"
                },
                y: 0,
                opacity: 1,
                duration: 1,
                ease: "power3.out"
            });
        });
    });
</script>
<?php
require_once __DIR__ . '/../config/DB.php';
$db = DB::getInstance()->getConnection();

// Obtengo las categorías y sus productos
$stmt_cat = $db->query("SELECT * FROM categories ORDER BY id ASC");
$categories = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="vice-menu-page" style="background-color: var(--color-bg); padding-bottom: 80px;">

    <!-- HERO PEQUEÑO -->
    <div class="menu-hero-small"
        style="min-height: 40vh; height: auto; display: flex; align-items: flex-start; justify-content: center; position: relative; padding-top: 0px; padding-bottom: 80px;">
        <img src="assets/img/menu/vice-menu-logo.svg" alt="Vice Menu"
            style="position: relative; z-index: 2; max-width: 90%; width: 750px; margin-top: 0; margin-bottom: 0; filter: drop-shadow(0 0 20px rgba(255, 0, 222, 0.6));">
    </div>

    <!-- BUCLE DE CATEGORÍAS -->
    <?php foreach ($categories as $cat): ?>
        <?php
        // Obtengo los productos de la categoría
        $stmt_prod = $db->prepare("SELECT * FROM products WHERE category_id = ?");
        $stmt_prod->execute([$cat['id']]);
        $products = $stmt_prod->fetchAll(PDO::FETCH_ASSOC);

        if (count($products) == 0)
            continue;

        // Limpio la ruta de la imagen y apunto a la carpeta organizada
        $filename = basename($cat['image']);
        $bannerImg = 'assets/img/menu/banners/' . $filename;
        ?>

        <!-- BANNER DE SECCIÓN -->
        <div class="menu-section-banner" style="background-image: url('<?php echo htmlspecialchars($bannerImg); ?>');">
            <div class="menu-section-overlay"></div>
            <div class="menu-section-title reveal-text">
                <?php echo htmlspecialchars($cat['name']); ?>
            </div>
        </div>

        <!-- DISEÑO EDITORIAL DE PRODUCTOS -->
        <div class="editorial-container">
            <?php foreach ($products as $prod): ?>
                <?php
                // Estandarización de rutas de imagen (Igual que en pedir.php)
                $filename = basename($prod['image']);
                $prodImg = 'assets/img/menu/products/' . $filename;

                // Si no existe la imagen, busco alternativa
                if (!file_exists(__DIR__ . '/../' . $prodImg)) {
                    // Pruebo en la carpeta raíz
                    $fallback = 'assets/img/' . $filename;
                    if (file_exists(__DIR__ . '/../' . $fallback)) {
                        $prodImg = $fallback;
                    }
                }
                ?>
                <div class="editorial-row reveal-row">
                    <!-- Columna de imagen -->
                    <div class="editorial-image-col">
                        <img src="<?php echo htmlspecialchars($prodImg); ?>" class="editorial-img"
                            alt="<?php echo htmlspecialchars($prod['name']); ?>">
                    </div>
                    <!-- Columna de contenido -->
                    <div class="editorial-content-col">
                        <div class="editorial-title"><?php echo htmlspecialchars($prod['name']); ?></div>
                        <div class="editorial-desc"><?php echo htmlspecialchars($prod['description']); ?></div>
                        <span class="editorial-price"><?php echo number_format($prod['price'], 2); ?>€</span>

                        <!-- Botón "Pedir" que actúa como añadir al carrito o redirección -->
                        <button class="btn btn-editorial" onclick='Cart.add({
                                    id: <?php echo $prod["id"]; ?>, 
                                    name: "<?php echo htmlspecialchars($prod["name"], ENT_QUOTES); ?>", 
                                    price: <?php echo $prod["price"]; ?>, 
                                    image: "<?php echo htmlspecialchars($prodImg, ENT_QUOTES); ?>"
                                })'>
                            ORDENAR
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php endforeach; ?>

</div>

<!-- Script GSAP -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        gsap.registerPlugin(ScrollTrigger);

        // Revelación del encabezado
        gsap.from('.menu-title-main', {
            duration: 1.5,
            y: 50,
            opacity: 0,
            ease: "power4.out"
        });

        // Títulos de sección (Texto del banner)
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

        // Filas editoriales (Imagen/Texto)
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
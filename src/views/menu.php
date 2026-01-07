<?php
require_once __DIR__ . '/../config/DB.php';
require_once __DIR__ . '/../services/CurrencyService.php';
$db = DB::getInstance()->getConnection();

// Recupero las categorías y sus productos de la base de datos
$stmt_cat = $db->query("SELECT * FROM categories ORDER BY id ASC");
$categories = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="vice-menu-page" style="background-color: var(--color-bg); padding-bottom: 80px;">

    <!-- HERO -->
    <div class="menu-hero-small"
        style="height: 60vh; display: flex; align-items: flex-start; justify-content: center; background-color: var(--color-bg); position: relative; padding-top: 40px; box-sizing: border-box;">
        <img src="assets/img/menu/viceMenu.png" alt="Vice Menu" class="menu-hero-logo"
            style="position: relative; z-index: 2; height: 750px; width: auto; filter: drop-shadow(0 0 15px rgba(255, 0, 255, 0.6));">
    </div>

    <!-- Recorro cada categoría para mostrar su sección -->
    <?php foreach ($categories as $cat): ?>
        <?php
        $cid = $cat['id'];
        // Consulto los productos específicos de esta categoría
        $stmt_prod = $db->prepare("SELECT * FROM products WHERE category_id = ?");
        $stmt_prod->execute([$cid]);
        $dbProducts = $stmt_prod->fetchAll(PDO::FETCH_ASSOC);

        // Si no hay productos, salto a la siguiente categoría
        if (empty($dbProducts))
            continue;

        // Limito la visualización a 3 productos por diseño
        $finalProducts = array_slice($dbProducts, 0, 3);

        // Selecciono la imagen del banner desde la base de datos
        $filename = basename($cat['image']);
        $bannerImg = 'assets/img/menu/banners/' . $filename;
        ?>

        <!-- Muestro el banner de la sección -->
        <div class="menu-section-banner parallax-banner"
            style="background-image: url('<?php echo htmlspecialchars($bannerImg); ?>'); background-position: center center; background-size: cover; height: 60vh;">
            <div class="menu-section-overlay"></div>
            <div class="menu-section-title reveal-text">
                <?php echo htmlspecialchars($cat['name']); ?>
            </div>
        </div>

        <!-- Maqueto los 3 productos usando un diseño editorial -->
        <div class="editorial-container">
            <?php foreach ($finalProducts as $index => $prod): ?>
                <?php
                $imgName = basename($prod['image']);
                $prodImg = 'assets/img/menu/products/' . $imgName;

                if (!file_exists(__DIR__ . '/../' . $prodImg)) {
                    $prodImg = $prod['image'];
                    if (!file_exists(__DIR__ . '/../' . $prodImg)) {
                        $prodImg = 'assets/img/default-product.jpg';
                    }
                }

                // Alterno la disposición: izquierda para impares, derecha para pares
                $rowClass = ($index % 2 === 0) ? 'row-normal' : 'row-reverse';
                ?>
                <div class="editorial-row reveal-row <?php echo $rowClass; ?>">
                    <!-- Muestro la imagen del producto -->
                    <div class="editorial-image-col">
                        <img src="<?php echo htmlspecialchars($prodImg); ?>" class="editorial-img"
                            alt="<?php echo htmlspecialchars($prod['name']); ?>">
                    </div>
                    <!-- Muestro la información del producto -->
                    <div class="editorial-content-col">
                        <div class="editorial-title"><?php echo htmlspecialchars($prod['name']); ?></div>
                        <div class="editorial-desc"><?php echo htmlspecialchars($prod['description']); ?></div>
                        <span class="editorial-price"><?php echo CurrencyService::format($prod['price']); ?></span>

                        <!-- Botón de acción -->
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

<!-- Estilos específicos para esta página -->
<style>
    .editorial-row.row-reverse {
        flex-direction: row-reverse;
        text-align: right;
    }

    .editorial-row.row-reverse .editorial-content-col {
        align-items: flex-end;
    }

    /* Ajusto el tamaño del logo para pantallas móviles */
    @media (max-width: 768px) {
        .menu-hero-logo {
            height: 150px !important;
            /* Reduzco altura para móvil */
        }

        .editorial-row,
        .editorial-row.row-reverse {
            flex-direction: column !important;
            text-align: center !important;
        }

        .editorial-content-col {
            align-items: center !important;
        }
    }
</style>

<!-- Importo GSAP para las animaciones -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        gsap.registerPlugin(ScrollTrigger);

        // Oculto los elementos inicialmente para animar su entrada
        gsap.set('.menu-hero-logo', { scale: 0.8, opacity: 0 });
        gsap.set('.reveal-text', { y: 50, opacity: 0 });
        gsap.set('.reveal-row', { y: 50, opacity: 0 });

        // Animo la aparición del logo principal
        gsap.to('.menu-hero-logo', {
            duration: 1.5,
            scale: 1,
            opacity: 1,
            ease: "elastic.out(1, 0.7)"
        });

        // Configuro el efecto parallax suave para dar profundidad a los banners
        gsap.utils.toArray('.parallax-banner').forEach(banner => {
            gsap.to(banner, {
                backgroundPosition: "50% 100%", // Desplazo el fondo lentamente para el efecto visual
                ease: "none",
                scrollTrigger: {
                    trigger: banner,
                    start: "top bottom", // La animación inicia cuando el banner entra en pantalla
                    end: "bottom top",   // Termina cuando sale
                    scrub: true
                }
            });
        });

        // Animo la entrada de los títulos de las categorías
        gsap.utils.toArray('.reveal-text').forEach(text => {
            gsap.to(text, {
                scrollTrigger: {
                    trigger: text,
                    start: "top 80%",
                    toggleActions: "play none none reverse"
                },
                y: 0,
                opacity: 1,
                duration: 1.2,
                ease: "power3.out"
            });
        });

        // Animo la aparición de cada fila de producto
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
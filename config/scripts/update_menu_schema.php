<?php
/**
 * Migración de Base de Datos: Rediseño del Sistema de Menú
 * - Añado columnas de información de entrega a la tabla de pedidos
 * - Añado categorías Principales y Extras
 * - Creo productos de ejemplo para las nuevas categorías
 */

require_once 'DB.php';

try {
    $db = DB::getInstance()->getConnection();
    echo "<h2>Vice Menu System - Database Migration</h2><pre>\n";

    // Paso 1: Añado columnas de información de entrega a la tabla de pedidos
    echo "1. Adding delivery information columns to orders table...\n";
    try {
        $db->exec("ALTER TABLE orders 
                   ADD COLUMN delivery_address VARCHAR(255) DEFAULT NULL,
                   ADD COLUMN delivery_phone VARCHAR(20) DEFAULT NULL,
                   ADD COLUMN delivery_notes TEXT DEFAULT NULL");
        echo "   ✓ Delivery columns added successfully\n\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "   ℹ Delivery columns already exist\n\n";
        } else {
            throw $e;
        }
    }

    // Paso 2: Añado nuevas categorías (Principales, Extras)
    echo "2. Adding new categories (Principales, Extras)...\n";

    $categories = [
        ['name' => 'Principales', 'image' => 'views/images/banners/principales-banner.jpg'],
        ['name' => 'Extras', 'image' => 'views/images/banners/extras-banner.jpg']
    ];

    $stmt = $db->prepare("INSERT INTO categories (name, image) VALUES (?, ?) 
                          ON DUPLICATE KEY UPDATE image = VALUES(image)");

    foreach ($categories as $cat) {
        try {
            $stmt->execute([$cat['name'], $cat['image']]);
            echo "   ✓ Category '{$cat['name']}' added/updated\n";
        } catch (PDOException $e) {
            echo "   ℹ Category '{$cat['name']}' already exists\n";
        }
    }
    echo "\n";

    // Paso 3: Añado productos de ejemplo para las nuevas categorías
    echo "3. Adding sample products...\n";

    // Obtengo los IDs de las categorías
    $stmt = $db->prepare("SELECT id, name FROM categories WHERE name IN ('Principales', 'Extras')");
    $stmt->execute();
    $cats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $catMap = [];
    foreach ($cats as $cat) {
        $catMap[$cat['name']] = $cat['id'];
    }

    $products = [];

    if (isset($catMap['Principales'])) {
        $products[] = [
            'category_id' => $catMap['Principales'],
            'name' => 'Vice Burger',
            'description' => 'Hamburguesa premium con queso cheddar, bacon crujiente, cebolla caramelizada y salsa especial Vice.',
            'price' => 16.50,
            'image' => 'img/burger-vice.jpg'
        ];
        $products[] = [
            'category_id' => $catMap['Principales'],
            'name' => 'Miami Ribs',
            'description' => 'Costillas BBQ glaseadas con salsa de miel y especias, acompañadas de patatas rústicas.',
            'price' => 22.00,
            'image' => 'img/ribs-miami.jpg'
        ];
        $products[] = [
            'category_id' => $catMap['Principales'],
            'name' => 'Neon Tacos',
            'description' => 'Tres tacos de carne asada con guacamole, pico de gallo y queso fundido.',
            'price' => 14.50,
            'image' => 'img/tacos-neon.jpg'
        ];
    }

    if (isset($catMap['Extras'])) {
        $products[] = [
            'category_id' => $catMap['Extras'],
            'name' => 'Patatas Vice',
            'description' => 'Patatas fritas con salsa especial Vice y queso fundido.',
            'price' => 6.00,
            'image' => 'img/patatas-vice.jpg'
        ];
        $products[] = [
            'category_id' => $catMap['Extras'],
            'name' => 'Onion Rings',
            'description' => 'Aros de cebolla crujientes con salsa de mostaza y miel.',
            'price' => 5.50,
            'image' => 'img/onion-rings.jpg'
        ];
        $products[] = [
            'category_id' => $catMap['Extras'],
            'name' => 'Miami Fries',
            'description' => 'Patatas deluxe con bacon, jalapeños y salsa ranch.',
            'price' => 7.50,
            'image' => 'img/miami-fries.jpg'
        ];
    }

    $stmt = $db->prepare("INSERT INTO products (category_id, name, description, price, image) 
                          VALUES (?, ?, ?, ?, ?)");

    foreach ($products as $prod) {
        try {
            $stmt->execute([
                $prod['category_id'],
                $prod['name'],
                $prod['description'],
                $prod['price'],
                $prod['image']
            ]);
            echo "   ✓ Product '{$prod['name']}' added\n";
        } catch (PDOException $e) {
            echo "   ℹ Product '{$prod['name']}' may already exist\n";
        }
    }

    echo "\n========================================\n";
    echo "Migration Complete!\n";
    echo "========================================\n\n";
    echo "Changes applied:\n";
    echo "- Orders table now has delivery_address, delivery_phone, delivery_notes columns\n";
    echo "- Categories: Principales and Extras added\n";
    echo "- Sample products added for new categories\n\n";

    echo "</pre><br><a href='index.php' class='btn btn-primary'>Go to Home</a>\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "</pre>";
}
?>
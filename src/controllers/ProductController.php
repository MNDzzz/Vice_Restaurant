<?php

/**
 * ProductController
 * Gestiona el CRUD de productos y la obtención de categorías.
 * Todas las respuestas son JSON (usado por admin.js vía api.php).
 */
class ProductController
{
    /**
     * Devuelve todos los productos en formato JSON.
     */
    public function getAll(): void
    {
        try {
            $productDAO = new ProductDAO();
            $productos  = $productDAO->getAll();

            $resultado = array_map(fn($p) => $p->toArray(), $productos);
            echo json_encode($resultado);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Devuelve un producto por su ID.
     */
    public function getById(): void
    {
        try {
            $id         = $_GET['id'] ?? 0;
            $productDAO = new ProductDAO();
            $producto   = $productDAO->getById($id);

            echo json_encode($producto ? $producto->toArray() : null);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Crea un nuevo producto a partir de los datos JSON del cuerpo de la petición.
     */
    public function store(): void
    {
        $datos = json_decode(file_get_contents('php://input'), true);

        if (!isset($datos['name'], $datos['price'])) {
            echo json_encode(['success' => false, 'error' => 'Faltan campos requeridos']);
            return;
        }

        try {
            $producto = new Product([
                'name'        => $datos['name'],
                'description' => $datos['description'] ?? '',
                'price'       => $datos['price'],
                'image'       => $datos['image'] ?? 'img/default-product.webp',
                'category_id' => $datos['category_id'] ?? null,
            ]);

            $productDAO     = new ProductDAO();
            $productoCreado = $productDAO->create($producto);

            echo json_encode(['success' => true, 'id' => $productoCreado->getId()]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Actualiza un producto existente.
     */
    public function update(): void
    {
        $datos = json_decode(file_get_contents('php://input'), true);

        if (!isset($datos['id'], $datos['name'], $datos['price'])) {
            echo json_encode(['success' => false, 'error' => 'Faltan campos requeridos']);
            return;
        }

        try {
            $producto = new Product([
                'id'          => $datos['id'],
                'name'        => $datos['name'],
                'description' => $datos['description'] ?? '',
                'price'       => $datos['price'],
                'image'       => $datos['image'] ?? 'img/default-product.webp',
                'category_id' => !empty($datos['category_id']) ? $datos['category_id'] : null,
            ]);

            $productDAO = new ProductDAO();
            $exito      = $productDAO->update($producto);

            echo json_encode(['success' => $exito]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Elimina un producto.
     */
    public function destroy(): void
    {
        $datos = json_decode(file_get_contents('php://input'), true);

        if (!isset($datos['id'])) {
            echo json_encode(['success' => false, 'error' => 'Falta el ID']);
            return;
        }

        try {
            $productDAO = new ProductDAO();
            $exito      = $productDAO->delete($datos['id']);

            echo json_encode(['success' => $exito]);
        } catch (Exception $e) {
            // Capturo error de clave foránea (producto en pedidos)
            if ($e->getCode() == '23000' && str_contains($e->getMessage(), '1451')) {
                echo json_encode(['success' => false, 'error' => 'No se puede eliminar: este producto está en pedidos realizados.']);
            } else {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        }
    }

    /**
     * Devuelve todas las categorías disponibles.
     */
    public function getCategories(): void
    {
        try {
            $categoryDAO = new CategoryDAO();
            $categorias  = $categoryDAO->getAll();

            $resultado = array_map(fn($c) => $c->toArray(), $categorias);
            echo json_encode($resultado);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}

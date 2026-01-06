<?php
require_once __DIR__ . '/../dao/ProductDAO.php';

class DiscountService
{
    private static function getCurrentTime()
    {
        $dia = date('N'); // 1-7
        $hora = date('H'); // 0-23

        // Testing diferentes días y/o horas
        //$dia = 7;
        //$hora = 22;

        return ['dia' => $dia, 'hora' => $hora];
    }

    public static function getPromoStatus()
    {
        $time = self::getCurrentTime();
        $dia = $time['dia'];
        $hora = $time['hora'];

        if ($dia >= 1 && $dia <= 3) {
            return 'CHILL';
        } elseif ($dia >= 4 && $dia <= 7 && $hora >= 20 && $hora < 23) {
            return 'PARTY';
        } elseif ($dia >= 4 && $dia <= 7) {
            return 'WEEKEND_WAIT';
        }

        return 'NONE';
    }

    public static function calculateDetails($cartItems)
    {
        $productDAO = new ProductDAO();
        $total = 0;
        $descuento = 0;
        $nombrePromocion = '';

        // Recupero detalles completos
        $itemsConDetalles = [];
        foreach ($cartItems as $item) {
            $prod = $productDAO->getById($item['id']);
            if ($prod) {
                $itemsConDetalles[] = [
                    'id' => $item['id'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'category_id' => $prod->getCategoryId()
                ];
            } else {
                // Fallback si no encuentra producto
                $itemsConDetalles[] = [
                    'id' => $item['id'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'category_id' => 0
                ];
            }
        }

        $time = self::getCurrentTime();
        $dia = $time['dia'];
        $hora = $time['hora'];

        $preciosCocteles = [];
        $preciosPrincipales = [];

        foreach ($itemsConDetalles as $item) {
            $lineTotal = $item['price'] * $item['quantity'];
            $total += $lineTotal;

            if ($item['category_id'] == 2) { // Cocktails
                for ($i = 0; $i < $item['quantity']; $i++)
                    $preciosCocteles[] = $item['price'];
            } elseif ($item['category_id'] == 3) { // Principales
                for ($i = 0; $i < $item['quantity']; $i++)
                    $preciosPrincipales[] = $item['price'];
            }
        }

        // Lógica de Descuentos
        if ($dia >= 1 && $dia <= 3) { // Lunes - Miércoles
            foreach ($preciosCocteles as $precio) {
                $descuento += $precio * 0.50;
            }
            if ($descuento > 0) {
                $nombrePromocion = 'Chill Week (50% en Cócteles)';
            }
        } elseif ($dia >= 4 && $dia <= 7 && $hora >= 20 && $hora < 23) { // Jueves - Domingo (20-23h)
            rsort($preciosCocteles);
            rsort($preciosPrincipales);
            $numParejas = min(count($preciosCocteles), count($preciosPrincipales));

            for ($i = 0; $i < $numParejas; $i++) {
                $descuento += $preciosCocteles[$i] * 0.25;
                $descuento += $preciosPrincipales[$i] * 0.25;
            }

            if ($descuento > 0) {
                $nombrePromocion = 'Happy Weekend (25% en Parejas)';
            }
        }

        $totalFinal = max(0, $total - $descuento);

        return [
            'subtotal' => $total,
            'discount' => $descuento,
            'promo_name' => $nombrePromocion,
            'finalTotal' => $totalFinal
        ];
    }
}
?>
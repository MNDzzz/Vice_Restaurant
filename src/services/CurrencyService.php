<?php
require_once __DIR__ . '/../dao/ConfigDAO.php';

class CurrencyService
{
    private static $symbol = '€';
    private static $rate = 1.0;
    private static $initialized = false;

    // Mapeo simple de códigos a símbolos
    private static $codeToSymbol = [
        'EUR' => '€',
        'USD' => '$',
        'GBP' => '£',
        'JPY' => '¥'
    ];

    private static function init()
    {
        if (!self::$initialized) {
            $configDAO = new ConfigDAO();
            $config = $configDAO->getAll();

            // Intento obtener el código de moneda (ej: USD)
            if (isset($config['currency_code'])) {
                $code = $config['currency_code'];
                // Si existe en mi lista, uso su símbolo
                if (isset(self::$codeToSymbol[$code])) {
                    self::$symbol = self::$codeToSymbol[$code];
                } else {
                    self::$symbol = $code; // Si no, usa el código tal cual
                }
            } elseif (isset($config['currency_symbol'])) {
                self::$symbol = $config['currency_symbol'];
            }

            if (isset($config['currency_rate'])) {
                self::$rate = floatval($config['currency_rate']);
            }

            // Si la tasa es 0 o negativa, vuelvo a 1 (bug)
            if (self::$rate <= 0)
                self::$rate = 1.0;

            self::$initialized = true;
        }
    }

    public static function format($amount)
    {
        self::init();
        $converted = $amount * self::$rate;

        // Doy formato al precio
        // Si es Euro, lo pongo al final con coma. Si es otro, al principio con punto.
        if (self::$symbol === '€') {
            return number_format($converted, 2, ',', '.') . ' ' . self::$symbol;
        } else {
            return self::$symbol . ' ' . number_format($converted, 2, '.', ',');
        }
    }

    public static function convert($amount)
    {
        self::init();
        return $amount * self::$rate;
    }

    public static function getSymbol()
    {
        self::init();
        return self::$symbol;
    }

    public static function getRate()
    {
        self::init();
        return self::$rate;
    }
}
?>
<?php

namespace FlightDeck;

class Autoload
{
    /**
     * Autoload FlightDeck files
     */

    public static function core($class)
    {
        $prefix = 'FlightDeck';

        //$app_path = realpath(dirname(__FILE__));

        //$base_dir = $app_path;

        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }

        $relative_class = substr($class, $len);

        $file = FLIGHTDECK_CORE_PATH . str_replace('\\', '/', $relative_class) . '.php';

        if (file_exists($file)) {
            require $file;
        }
    }
}
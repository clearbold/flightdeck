<?php

// Set the path to flightdeck relative to this file
$flightdeck_path = '../flightdeck';

// Do not modify anything below this line!

define('FLIGHTDECK_PATH', __DIR__ . '/' . $flightdeck_path);
define('FLIGHTDECK_CORE_PATH', FLIGHTDECK_PATH . '/app/core');

require FLIGHTDECK_PATH . '/app/vendor/autoload.php';
require FLIGHTDECK_PATH . '/app/core/Flightdeck.php';
require FLIGHTDECK_PATH . '/app/lib/Premailer.php';

spl_autoload_register('\FlightDeck\Autoload::core');

$app = new \Slim\Slim(array(
    'view' => new \Slim\Views\Twig()
));

require FLIGHTDECK_PATH . '/routes.php';

$app->run();
<?php

$flightdeck_path = '../flightdeck';

// Do not modify anything below this line!

$flightdeck_path = __DIR__ . '/' . $flightdeck_path;

// Keep
require $flightdeck_path . '/app/vendor/autoload.php';
require $flightdeck_path . '/app/vendor/mandrill/mandrill/src/Mandrill.php';
// Replace with FlightDeck Autoloader
require $flightdeck_path . '/app/lib/Premailer.php';
require $flightdeck_path . '/app/core/Console.php';
require $flightdeck_path . '/app/core/EmailTemplate.php';

// Keep
$app = new \Slim\Slim(array(
    'view' => new \Slim\Views\Twig()
));

// Put FlightDeck Autoloader here

require $flightdeck_path . '/routes.php';

$app->run();
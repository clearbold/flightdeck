<?php

use \FlightDeck\Console as Console;
use \FlightDeck\EmailTemplate as EmailTemplate;

$view = $app->view();
$view->parserOptions = array(
    'debug' => true,
    'cache' => $flightdeck_path . '/cache'
);
$view->twigTemplateDirs = array(
    $flightdeck_path . '/app/templates'
);

$app->get('/', function () use ($app)
{

    $console = new Console;

    $file_tree_email = $console->listEmailTemplates();

    $app->render('email-template-list.html', array( 'file_tree' => $file_tree_email ));

});

$app->get('/build/:template', function($requestedTemplate) use ($app)
{

    $template = new EmailTemplate;

    $response = $template->buildEmailTemplate($requestedTemplate);

    $app->response->headers->set('Content-Type', 'application/json');
    echo json_encode($response);

});
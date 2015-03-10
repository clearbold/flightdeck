<?php

require '../flightdeck/app/vendor/autoload.php';
require '../flightdeck/app/lib/Premailer.php';
require '../flightdeck/app/core/Console.php';
require '../flightdeck/app/core/Template.php';

$app = new \Slim\Slim(array(
    'view' => new \Slim\Views\Twig()
));

$console = new Console();
$template = new Template();

use secondparty\Dipper\Dipper as Dipper;

// TODO: Move global config stuff to the right place
// TODO: Move reading YAML to somewhere central
// Get the contents of the file as a string
$config_file = file_get_contents('../flightdeck/config/general.yaml');
// Delineate the YAML front matter and template HTML
$config_contents = explode('---', $config_file);
$global_config = Dipper::parse($config_contents[1]);

$view = $app->view();
$view->parserOptions = array(
    'debug' => true,
    'cache' => dirname(__FILE__) . '/../flightdeck/cache'
);
$view->twigTemplateDirs = array(
    '../flightdeck/app/templates'
);

$app->get('/', function () use ($app, $console) {

    $file_tree_email = $console->listEmailTemplates();

    $app->render('email-template-list.html', array( 'file_tree' => $file_tree_email ));

});

$app->get('/build/:template', function($requestedTemplate) use ($app, $template) {

    $response = $template->buildEmailTemplate($requestedTemplate);

    $app->response->headers->set('Content-Type', 'application/json');
    echo json_encode($response);

});

$app->run();

// http://php.net/manual/en/function.file-put-contents.php
function file_force_contents($filename, $data, $flags = 0){
    if(!is_dir(dirname($filename)))
        mkdir(dirname($filename).'/', 0777, TRUE);
    return file_put_contents($filename, $data,$flags);
}
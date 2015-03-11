<?php

use \FlightDeck\Console as Console;
use \FlightDeck\EmailTemplate as EmailTemplate;

$view = $app->view();
$view->parserOptions = array(
    // This Twig setting needs to move into config
    'debug' => true,
    'cache' => FLIGHTDECK_PATH . '/cache'
);
$view->twigTemplateDirs = array(
    FLIGHTDECK_PATH . '/app/templates'
);

$app->get('/', function() use ($app)
{
    echo '';
});

/**
 * Default page, loads list of email templates
 * @return rendered Twig template as HTML
 */
$app->get('/console', function() use ($app)
{

    $console = new Console;

    $app->render('email-template-list.html',
        array( 'file_tree' => $console->listEmailTemplates() ));

});

/**
 * Build (& Test) URL called for a template
 * @param string  $requestedTemplate  modified path to template to build & test
 * @throws
 * @return JSON  status + build datetime
 */
$app->get('/build/:template', function($requestedTemplate) use ($app)
{

    $template = new EmailTemplate(str_replace('::',
        '/', filter_var($requestedTemplate, FILTER_SANITIZE_STRING)));

    $app->response->headers->set('Content-Type', 'application/json');
    echo json_encode($template->buildEmailTemplate());

});

$app->get('/ui/css/styles.css', function() use ($app)
{
    $app->response->headers->set('Content-Type', 'text/css');
    echo file_get_contents(FLIGHTDECK_PATH . '/resources/css/styles.css');
});

$app->get('/ui/js/vendor/jquery.js', function() use ($app)
{
    $app->response->headers->set('Content-Type', 'text/javascript');
    echo file_get_contents(FLIGHTDECK_PATH . '/resources/js/vendor/jquery.js');
});

$app->get('/ui/js/scripts.js', function() use ($app)
{
    $app->response->headers->set('Content-Type', 'text/javascript');
    echo file_get_contents(FLIGHTDECK_PATH . '/resources/js/scripts.js');
});
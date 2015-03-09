<?php

require '../dispatchwire/app/vendor/autoload.php';
require '../dispatchwire/app/lib/Premailer.php';

$app = new \Slim\Slim(array(
    'view' => new \Slim\Views\Twig()
));
use secondparty\Dipper\Dipper as Dipper;

// TODO: Move global config stuff to the right place
// TODO: Move reading YAML to somewhere central
// Get the contents of the file as a string
$config_file = file_get_contents('../dispatchwire/config/general.yaml');
// Delineate the YAML front matter and template HTML
$config_contents = explode('---', $config_file);
$global_config = Dipper::parse($config_contents[1]);

$mandrill = new Mandrill($global_config['_mandrill_api_key']);

$view = $app->view();
$view->parserOptions = array(
    'debug' => true,
    'cache' => dirname(__FILE__) . '/../dispatchwire/cache'
);
$view->twigTemplateDirs = array(
    '../dispatchwire/app/templates'
);

$app->get('/', function () use ($app) {

    // Supporting a max of 2 email levels to organize templates by client
    $dirs_email = glob("./templates/email/*", GLOB_ONLYDIR);
    $all_email = glob("./templates/email/*");
    $files_email = array_diff($all_email, $dirs_email);

    $file_tree_email = array();

    // L1 files
    $i = 0;
    foreach ($files_email as $filename) {
        $file_tree_email[] = array('item_name' => explode('/',$filename)[count(explode('/',$filename))-1], 'item_type' => 'file', 'item_level' => 'l1', 'item_cycle' => ($i % 2 == 0) ? 'even' : 'odd', 'item_template_name' => str_replace('/', '::', $filename) );
        $i++;
    }
    // L1 dirs
    $i = 0;
    foreach ($dirs_email as $dirname) {
        $file_tree_email[] = array('item_name' => explode('/',$dirname)[count(explode('/',$dirname))-1], 'item_type' => 'dir', 'item_level' => 'l1', 'item_cycle' => '', 'item_template_name' => str_replace('/', '::', $dirname) );
        // L2 files›
        $i = 0;
        foreach (glob("$dirname/*") as $filename) {
            $file_tree_email[] = array('item_name' => explode('/',$filename)[count(explode('/',$filename))-1], 'item_type' => 'file', 'item_level' => 'l2', 'item_cycle' => ($i % 2 == 0) ? 'even' : 'odd', 'item_template_name' => str_replace('/', '::', $filename) );
            $i++;
        }
    }

    $app->render('email-template-list.html', array( 'file_tree' => $file_tree_email ));

});

$app->get('/build/:template', function($template) use ($app, $mandrill) {

    // Fetch the template's filename from the request, convert it back to a filepath
    $filename = str_replace('::', '/', $template);
    // Get the contents of the file as a string
    $template_file = file_get_contents($filename);
    // Delineate the YAML front matter and template HTML
    $template_contents = explode('---', $template_file);

    // Use Dipper to parse the YAML front matter into a PHP array
    $template_config = Dipper::parse($template_contents[1]);

    // Pass the template's HTML to the Premailer API
    $pre = Premailer::html(trim($template_contents[2]));
    $live_html = $pre['html'];

    // Create the filepaths for the Preview & Live versions of the template
    $template_filename = explode('/', $filename);
    // Swap in the preview dir
    $template_filename[1] = 'preview';
    $template_filename_preview = implode('/', $template_filename);
    // Swap in the live dir
    $template_filename[1] = 'live';
    $template_filename_live = implode('/', $template_filename);

    // TODO: Per the YAML Config, swap in preview values
    $preview_html = $live_html;
    foreach ($template_config['_tags_field_value'] as $key => $value) {
        $preview_html = str_replace($key, $value, $preview_html);
    }

    if ($template_config['_email_test']) {
        // We're going to send the test email using Mandrill to the specified addresses
        // TODO: Fall back to an address stored in config/general.yaml

        // Fetch _test_addresses from template YAML
        $test_addresses = array();
        foreach ($template_config['_test_addresses'] as $address) {
            $test_addresses[] = array(
                'email' => $address,
                'name' => '',
                'type' => ''
            );
        }
        $message = array(
            'html' => $preview_html,
            'text' => '',
            'subject' => 'PREVIEW ' . $template_config['_subject'],
            'from_email' => $template_config['_sender_email'],
            'from_name' => $template_config['_sender_name'],
            'to' => $test_addresses
        );

        $async = false;
        $result = $mandrill->messages->send($message, $async);
    }

    // Write the updated preview file
    $preview_file = file_force_contents($template_filename_preview, $preview_html);
    // Write the updated live file
    $live_file = file_force_contents($template_filename_live, $live_html);

    $app->response->headers->set('Content-Type', 'application/json');
    echo json_encode(array(
        "status" => true
    ));

});

$app->run();

// http://php.net/manual/en/function.file-put-contents.php
function file_force_contents($filename, $data, $flags = 0){
    if(!is_dir(dirname($filename)))
        mkdir(dirname($filename).'/', 0777, TRUE);
    return file_put_contents($filename, $data,$flags);
}
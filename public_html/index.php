<?php

require '../dispatchwire/app/vendor/autoload.php';
require '../dispatchwire/app/lib/Premailer.php';

$app = new \Slim\Slim(array(
    'view' => new \Slim\Views\Twig()
));
use secondparty\Dipper\Dipper as Dipper;

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
        foreach  (glob("$dirname/*") as $filename) {
            $file_tree_email[] = array('item_name' => explode('/',$filename)[count(explode('/',$filename))-1], 'item_type' => 'file', 'item_level' => 'l2', 'item_cycle' => ($i % 2 == 0) ? 'even' : 'odd', 'item_template_name' => str_replace('/', '::', $filename) );
            $i++;
        }
    }

    $app->render('email-template-list.html', array( 'file_tree' => $file_tree_email ));

});

$app->get('/build/:template', function($template) use ($app) {

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
    $html = $pre['html'];

    // Create the filepaths for the Preview & Live versions of the template
    $template_filename = explode('/', $filename);
    // Swap in the preview dir
    $template_filename[1] = 'preview';
    $template_filename_preview = implode('/', $template_filename);
    // Swap in the live dir
    $template_filename[1] = 'live';
    $template_filename_live = implode('/', $template_filename);

    // TODO: Per the YAML Config, swap in preview values, send test emails

    // Write the updated preview file
    $preview_file = file_force_contents($template_filename_preview, $html);
    // Write the updated live file
    $live_file = file_force_contents($template_filename_live, $html);

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
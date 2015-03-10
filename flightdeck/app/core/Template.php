<?php

use secondparty\Dipper\Dipper as Dipper;

class Template {

    public static function buildEmailTemplate($template) {

        // TODO: Move global config stuff to the right place
        // TODO: Move reading YAML to somewhere central
        // Get the contents of the file as a string
        $config_file = file_get_contents('../flightdeck/config/general.yaml');
        // Delineate the YAML front matter and template HTML
        $config_contents = explode('---', $config_file);
        $global_config = Dipper::parse($config_contents[1]);

        $mandrill = new Mandrill($global_config['_mandrill_api_key']);

        // Fetch the template's filename from the request, convert it back to a filepath
        $filename = str_replace('::', '/', $template);
        // Get the contents of the file as a string
        $template_file = file_get_contents($filename);
        // Delineate the YAML front matter and template HTML
        $template_contents = explode('---', $template_file);

        // Use Dipper to parse the YAML front matter into a PHP array
        $template_config = Dipper::parse($template_contents[1]);

        $template_html = trim($template_contents[2]);
        $snippets = array();
        $snippet_tags_found = preg_match_all( "/(\{\{\s*snippets\.([a-z0-9A-Z\-_]+)\s*\}\})/", $template_html, $snippets );

        $i = 0;
        foreach($snippets[2] as $tag_filename) {
            $snippet_filename = './snippets/email/' . $tag_filename . '.html';
            if (file_exists($snippet_filename))
            {
                $snippet_file = file_get_contents($snippet_filename);
                $template_html = str_replace($snippets[1][$i], $snippet_file, $template_html);
            }
            $i++;
        }

        // Pass the template's HTML to the Premailer API
        try
        {
            $pre = Premailer::html($template_html);
            $live_html = $pre['html'];
        }
        catch (Exception $e)
        {
            $live_html = '';
        }

        // Create the filepaths for the Preview & Live versions of the template
        $template_filename = explode('/', $filename);
        // Swap in the preview dir
        $template_filename[1] = 'preview';
        $template_filename_preview = implode('/', $template_filename);
        // Swap in the live dir
        $template_filename[1] = 'live';
        $template_filename_live = implode('/', $template_filename);

        $preview_html = $live_html;

        // Swap custom field tags with config values for the preview (not live)
        if (isset($template_config['_tags_field_value'])) {
            foreach ($template_config['_tags_field_value'] as $key => $value) {
                $preview_html = str_replace($key, $value, $preview_html);
            }
        }

        // Send email test
        if (isset($template_config['_email_test']) && $template_config['_email_test']) {
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

        return array(
            "status" => true,
            "lastBuild" => date("m-d-Y H:i:s", filemtime($template_filename_live))
        );
    }

}
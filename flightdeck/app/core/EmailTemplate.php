<?php

namespace FlightDeck;

use \FlightDeck\Config as Config;
use secondparty\Dipper\Dipper as Dipper;

class EmailTemplate
{

    private $template_file_path;
    private $template_file;
    private $template_contents;
    private $template_file_path_preview;
    private $template_file_path_live;

    public function __construct($template_file_path) {

        $this->template_file_path = $template_file_path;
        $this->template_file = file_get_contents($this->template_file_path);
        $this->template_contents = explode('---', $this->template_file);

        // Create the filepaths for the Preview & Live versions of the template
        $template_filename = explode('/', $this->template_file_path);
        // Swap in the preview dir
        $template_filename[1] = 'preview';
        $this->template_file_path_preview = implode('/', $template_filename);
        // Swap in the live dir
        $template_filename[1] = 'live';
        $this->template_file_path_live = implode('/', $template_filename);

    }

    public function templateConfig() {
        // Needs to include global config defaults where template values are not set
        return Dipper::parse($this->template_contents[1]);
    }

    public function templateHtml() {
        return trim($this->template_contents[2]);
    }

    public function buildEmailTemplate() {

        $snippets = array();
        $snippet_tags_found = preg_match_all( "/(\{\{\s*snippets\.([a-z0-9A-Z\-_]+)\s*\}\})/", $this->templateHtml(), $snippets );

        $i = 0;
        foreach($snippets[2] as $tag_filename) {
            $snippet_filename = './snippets/email/' . $tag_filename . '.html';
            if (file_exists($snippet_filename))
            {
                $snippet_file = file_get_contents($snippet_filename);
                $template_html = str_replace($snippets[1][$i], $snippet_file, $this->templateHtml());
            }
            $i++;
        }

        // Pass the template's HTML to the Premailer API
        try
        {
            $pre = \Premailer::html($template_html);
            $live_html = $pre['html'];
        }
        catch (Exception $e)
        {
            $live_html = '';
        }

        $preview_html = $live_html;

        // Swap custom field tags with config values for the preview (not live)
        if (isset($this->template_config['_tags_field_value'])) {
            foreach ($this->template_config['_tags_field_value'] as $key => $value) {
                $preview_html = str_replace($key, $value, $preview_html);
            }
        }

        // Send email test
        if (isset($this->templateConfig()['_email_test']) && $this->templateConfig()['_email_test']) {

            $mandrill = new \Mandrill(Config::getConfig()['_mandrill_api_key']);

            // We're going to send the test email using Mandrill to the specified addresses
            // TODO: Fall back to an address stored in config/general.yaml
            // (Needs to happen in the config function)

            // Fetch _test_addresses from template YAML
            $test_addresses = array();
            foreach ($this->templateConfig()['_test_addresses'] as $address) {
                $test_addresses[] = array(
                    'email' => $address,
                    'name' => '',
                    'type' => ''
                );
            }
            $message = array(
                'html' => $preview_html,
                'text' => '',
                'subject' => 'PREVIEW ' . $this->templateConfig()['_subject'],
                'from_email' => $this->templateConfig()['_sender_email'],
                'from_name' => $this->templateConfig()['_sender_name'],
                'to' => $test_addresses
            );

            $async = false;
            $result = $mandrill->messages->send($message, $async);
        }

        // Write the updated preview file
        $preview_file = $this->writeFile($this->template_file_path_preview, $preview_html);
        // Write the updated live file
        $live_file = $this->writeFile($this->template_file_path_live, $live_html);

        return array(
            "status" => true,
            "lastBuild" => date("m-d-Y H:i:s", filemtime($this->template_file_path_live))
        );

    }

    // file_force_contents from http://php.net/manual/en/function.file-put-contents.php
    protected function writeFile($filename, $data, $flags = 0)
    {
        if(!is_dir(dirname($filename)))
            mkdir(dirname($filename).'/', 0777, TRUE);
        return file_put_contents($filename, $data,$flags);
    }

}
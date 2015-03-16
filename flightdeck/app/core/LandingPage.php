<?php

namespace FlightDeck;
use secondparty\Dipper\Dipper as Dipper;

class LandingPage
{
    private $landing_page_file;
    private $landing_page_contents;
    private $landing_page_path;
    private $yaml;
    private $markdown;
    private $html;

    private $has_campaignmonitor_form = false;
    private $campaignmonitor = array();

    public function __construct($landing_page_path)
    {
        $this->landing_page_path = $landing_page_path;
        $this->landing_page_file = file_get_contents($this->landing_page_path);
        $this->landing_page_contents = explode('---', $this->landing_page_file);

        $this->markdown = trim($this->landing_page_contents[2]);
        $this->raw_yaml = trim($this->landing_page_contents[1]);

        $this->yaml = Dipper::parse($this->landing_page_contents[1]);

        $Extra = new \ParsedownExtra;
        $this->html = $Extra->text($this->markdown);

        $this->has_campaignmonitor_form = isset($this->yaml['_campaignmonitor']) ? 'true' : 'false';
    }

    public function yaml()
    {
        return $this->yaml;
    }

    public function markdown()
    {
        return $this->markdown;
    }

    public function html()
    {
        return $this->html;
    }

    public function template()
    {
        return $this->yaml['_layout'] . '.html';
    }

    public function formMarkup() {
        if ($this->has_campaignmonitor_form) {
            return $this->cmFormMarkup();
        }
        else {
            return '';
        }
    }

    public function cmFormMarkup() {
        $createsend = new \CS_REST_General($this->yaml['_campaignmonitor']['_client_api_key']);

        $list = new \CS_REST_Lists($this->yaml['_campaignmonitor']['_client_list_id'],
            $this->yaml['_campaignmonitor']['_client_api_key']);

        $custom_fields = $list->get_custom_fields();

        $custom_fields = json_decode(json_encode($custom_fields->response), true);

        $visible_fields = array_filter($custom_fields, function($object) { return $object['VisibleInPreferenceCenter'] == 'true'; });

        //var_dump($visible_fields);

        // Will need a route to accept a POST at the same URL
        // https://www.campaignmonitor.com/api/subscribers/#adding_a_subscriber
        // EmailAddress and Name fields will have to be added in to the form

        $form_markup = "<form method=\"POST\"";
        $form_markup .= ">\n";
        $form_markup .= "    <p><label for=\"EmailAddress\">Email Address:</label>\n";
        $form_markup .= "    <input type=\"email\" name=\"EmailAddress\" id=\"EmailAddress\" /></p>\n";
        $form_markup .= "    <p><label for=\"Name\">Name:</label>\n";
        $form_markup .= "    <input type=\"text\" name=\"Name\" id=\"Name\" /></p>\n";
        foreach ($visible_fields as $field) {
            $key = explode(']',explode('[',$field['Key'])[1])[0];
            $form_markup .= "    <p><label for=\"$key\">$field[FieldName]:</label>\n";
            switch ($field['DataType']) {
                case 'Text':
                    $form_markup .= "    <input type=\"text\" name=\"$key\" id=\"$key\" /></p>\n";
                    break;
                case 'Number':
                    $form_markup .= "    <input type=\"number\" name=\"$key\" id=\"$key\" /></p>\n";
                    break;
                case 'MultiSelectOne':
                    $form_markup .= "    <select name=\"$key\" id=\"$key\">\n";
                foreach ( $field['FieldOptions'] as $option)
                        $form_markup .= "        <option>$option</option>\n";
                    $form_markup .= "    </select></p>\n";
                    break;
                case 'MultiSelectMany':
                    $form_markup .= "    <select name=\"$key\" id=\"$key\" multiple=\"true\">\n";
                foreach ( $field['FieldOptions'] as $option)
                        $form_markup .= "        <option>$option</option>\n";
                    $form_markup .= "    </select></p>\n";
                    break;
                case 'Date':
                    $form_markup .= "    <input type=\"datetime\" name=\"$key\" id=\"$key\" /></p>\n";
                    break;
                default:
                    $form_markup .= "    <input type=\"text\" name=\"$key\" id=\"$key\" /></p>\n";
            }
        }

        $form_markup .= '</form>';

        return $form_markup;
    }
}
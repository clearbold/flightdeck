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
        return $this->yaml['layout'] . '.html';
    }
}
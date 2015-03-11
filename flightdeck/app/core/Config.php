<?php

namespace FlightDeck;

use secondparty\Dipper\Dipper as Dipper;

class Config
{

    public static function getConfig()
    {
        // Get the contents of the file as a string
        $config_file = file_get_contents(FLIGHTDECK_PATH . '/config/general.yaml');
        // Delineate the YAML front matter and template HTML
        $config_contents = explode('---', $config_file);
        return Dipper::parse($config_contents[1]);
    }

}
<?php

namespace FlightDeck;

use secondparty\Dipper\Dipper as Dipper;

class Console
{

    public static function listEmailTemplates() {

        $file_tree_email = array();

        // Supporting a max of 2 email levels to organize templates by client
        // First get the directories at L1
        $dirs_email = glob("./templates/email/*", GLOB_ONLYDIR);
        // Then get the files at L1
        $all_email = glob("./templates/email/*");
        // Then subtract the directories to list just the L1 files first
        $files_email = array_diff($all_email, $dirs_email);

        // L1 files
        $i = 0;
        foreach ($files_email as $filename) {
            $filename_file = explode('/',$filename)[count(explode('/',$filename))-1];
            if (substr($filename_file, 0, 1) != '_') { // Better way of doing this/breaking out?
                $filename_live = str_replace('templates', 'live', $filename);
                $filename_preview =  str_replace('templates', 'preview', $filename);
                $last_build = file_exists($filename_live) ? date("m-d-Y H:i:s", filemtime($filename_live)) : 'Never';

                // Get the contents of the file as a string
                $template_file = file_get_contents($filename);
                // Delineate the YAML front matter and template HTML
                $template_contents = explode('---', $template_file);

                // Use Dipper to parse the YAML front matter into a PHP array
                $template_config = Dipper::parse($template_contents[1]);

                $test_link = (isset($template_config['_email_test']) && $template_config['_email_test']) ? 'true' : 'false';

                $file_tree_email[] = array('item_name' => $filename_file, 'item_type' => 'file', 'item_level' => 'l1', 'item_cycle' => ($i % 2 == 0) ? 'even' : 'odd', 'item_template_name' => str_replace('/', '::', $filename), 'item_preview_link' => $filename_preview, 'item_last_build' => $last_build, 'item_test' => $test_link );
                $i++;
            }
        }
        // L1 dirs
        $i = 0;
        foreach ($dirs_email as $dirname) {
            $dirname_dir = explode('/',$dirname)[count(explode('/',$dirname))-1];
            if (substr($dirname_dir, 0, 1) != '_') { // Better way of doing this/breaking out?
                $file_tree_email[] = array('item_name' => $dirname_dir, 'item_type' => 'dir', 'item_level' => 'l1', 'item_cycle' => '', 'item_template_name' => str_replace('/', '::', $dirname), 'item_preview_link' => '', 'item_last_build' => '', 'item_test' => '' );
                // L2 files›
                $i = 0;
                foreach (glob("$dirname/*") as $filename) {
                    $filename_file = explode('/',$filename)[count(explode('/',$filename))-1];
                    if (substr($filename_file, 0, 1) != '_') { // Better way of doing this/breaking out?
                        $filename_live = str_replace('templates', 'live', $filename);
                        $filename_preview =  str_replace('templates', 'preview', $filename);
                        $last_build = file_exists($filename_live) ? date("m-d-Y H:i:s", filemtime($filename_live)) : 'Never';

                        // Get the contents of the file as a string
                        $template_file = file_get_contents($filename);
                        // Delineate the YAML front matter and template HTML
                        $template_contents = explode('---', $template_file);

                        // Use Dipper to parse the YAML front matter into a PHP array
                        $template_config = Dipper::parse($template_contents[1]);

                        $test_link = (isset($template_config['_email_test']) && $template_config['_email_test']) ? 'true' : 'false';


                        $file_tree_email[] = array('item_name' => $filename_file, 'item_type' => 'file', 'item_level' => 'l2', 'item_cycle' => ($i % 2 == 0) ? 'even' : 'odd', 'item_template_name' => str_replace('/', '::', $filename), 'item_preview_link' => $filename_preview, 'item_last_build' => $last_build, 'item_test' => $test_link );
                        $i++;
                    }
                }
            }
        }

        return $file_tree_email;
    }

}
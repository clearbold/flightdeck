<?php

namespace FlightDeck;

use secondparty\Dipper\Dipper as Dipper;

class Console
{
    private $dirs_email;
    private $all_email;
    private $files_email;

    public function __construct()
    {
        // Supporting a max of 2 email levels to organize templates by client
        // First get the directories at L1
        $this->dirs_email = glob("./templates/email/*", GLOB_ONLYDIR);
        // Then get the files at L1
        $this->all_email = glob("./templates/email/*");
        // Then subtract the directories to list just the L1 files first
        $this->files_email = array_diff($this->all_email, $this->dirs_email);
    }

    public function listEmailTemplates() {

        $file_tree_email = array();

        // L1 files
        $i = 0;
        $level = 1;
        foreach ($this->files_email as $filename) {
            if (!$this->itemHidden($filename)) {
                $file_tree_email[] = $this->itemArray($filename, $i, $level);
                $i++;
            }
        }
        // L1 dirs
        foreach ($this->dirs_email as $dirname) {
            $i = 0;
            $level = 1;
            if (!$this->itemHidden($dirname)) {
                $file_tree_email[] = $this->itemArray($dirname, $i, $level);

                // L2 files for this dir
                $i = 0;
                $level = 2;
                foreach (glob("$dirname/*") as $filename) {
                    if (!$this->itemHidden($filename)) {
                        $file_tree_email[] = $this->itemArray($filename, $i, $level);
                        $i++;
                    }
                }
            }
        }

        return $file_tree_email;
    }

    private function itemHidden($item_path)
    {

        $item_name = explode('/',$item_path)[count(explode('/',$item_path))-1];

        return (substr($item_name, 0, 1) == '_') ? true : false;

    }

    private function itemArray($item_path, $cycle, $level=1)
    {
        $item_name = explode('/',$item_path)[count(explode('/',$item_path))-1];
        $item_type = is_dir($item_path) ? 'dir' : 'file';
        $item_cycle = is_dir($item_path) ? '' : (($cycle % 2 == 0) ? 'even' : 'odd');
        $item_template_name = is_dir($item_path) ? '' : str_replace('/', '::', $item_path);
        $item_path_live = is_dir($item_path) ? '' : str_replace('templates', 'live', $item_path);
        $item_path_preview = is_dir($item_path) ? '' : str_replace('templates', 'preview', $item_path);
        $item_last_build = is_dir($item_path) ? '' : (file_exists($item_path_live) ? date("m-d-Y H:i:s", filemtime($item_path_live)) : 'Never');

        $item_test_link = 'false';

        if (!is_dir($item_path)) {
            // Get the contents of the file as a string
            $template_file = file_get_contents($item_path);
            // Delineate the YAML front matter and template HTML
            $template_contents = explode('---', $template_file);
            // Use Dipper to parse the YAML front matter into a PHP array
            $template_config = Dipper::parse($template_contents[1]);

            $item_test_link = (isset($template_config['_email_test']) && $template_config['_email_test']) ? 'true' : 'false';
        }

        return array(
            'item_name' => $item_name,
            'item_type' => $item_type,
            'item_level' => 'l' . $level,
            'item_cycle' => $item_cycle,
            'item_template_name' => $item_template_name,
            'item_preview_link' => $item_path_preview,
            'item_last_build' => $item_last_build,
            'item_test' => $item_test_link
        );
    }

}
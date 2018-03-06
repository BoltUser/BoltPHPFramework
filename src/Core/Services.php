<?php
/**
 * Date: 26/12/17
 * Time: 23.39
 */

namespace Bolt\Core;


class Services
{
    public function load()
    {
        $stack = array_reverse(debug_backtrace());
        $app_path = dirname($stack[0]['file']);
        $found = FALSE;
        while (BASE_PATH != $app_path) {
            if (is_dir($servicePath = $app_path . "/services/")) {
                $found = TRUE;
                break;
            }
            $app_path = dirname($app_path);
        }
        if ($found === FALSE) {
            throw new \Exception('Services Folder Not Found!');
        }

        foreach (glob($servicePath . '*.php') as $file) {
            if (file_exists($file))
                require_once($file);
        }
    }

}
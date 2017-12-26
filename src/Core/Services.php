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
        $app_path = dirname(dirname($stack[0]['file']));
        $servicePath = $app_path . "/services/";
        $serviceFiles = scandir($servicePath);
        foreach ($serviceFiles as $file) {
            if (strrpos($file, '.php') === strlen($file) - strlen('.php')) {
                if (file_exists($serviceFile = $servicePath . $file))
                    require_once($serviceFile);
            }
        }
    }

}
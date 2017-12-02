<?php

namespace Bolt\Route;


class Reader
{

    public static function getRoutes()
    {
        $stack = array_reverse(debug_backtrace());
        $app_path = dirname(dirname($stack[0]['file']));
        $routes = include_once($app_path . '/routes/routes.php');
        return $routes;
    }

}
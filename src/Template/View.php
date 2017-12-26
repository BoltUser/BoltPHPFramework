<?php

namespace Bolt\Template;


class View
{

    /**
     * Render a view file
     *
     * @param string $view The view file
     * @param array $args Associative array of data to display in the view (optional)
     *
     * @return void
     */
    public static function render($viewName, $args = [])
    {
        extract($args, EXTR_SKIP);
        $stack = array_reverse(debug_backtrace());
        $app_path = dirname(dirname($stack[0]['file']));
        $file = $app_path . "/resources/views/$viewName";
        if (is_readable($file)) {
            require $file;
        } else {
            throw new \Exception("$file not found");
        }
    }

    /**
     * Render a view template using Twig
     *
     * @param string $template The template file
     * @param array $args Associative array of data to display in the view (optional)
     *
     * @return void
     */
    public static function renderTemplate($viewName, $args = [])
    {
        $stack = array_reverse(debug_backtrace());
        $app_path = dirname(dirname($stack[0]['file']));
        static $twig = null;
        if ($twig === null) {
            $loader = new \Twig_Loader_Filesystem($app_path . "/resources/views");
            $twig = new \Twig_Environment($loader);
        }
        echo $twig->render($viewName, $args);
    }

}
<?php

namespace Bolt\Core;

use Aura\Web\WebFactory;
use Bolt\Route\Reader as RouteReader;
use FastRoute;


class BoltPHP
{
    /**
     * @var Container
     */
    private static $container;


    public function __construct()
    {
    }

    public function run()
    {
        if (!defined("BASE_PATH"))
            throw new \Exception('Please Define a BASE_PATH constant in your index file on docroot');

        $wf = new WebFactory(array('_ENV' => $_ENV, '_GET' => $_GET, '_POST' => $_POST, '_COOKIE' => $_COOKIE, '_SERVER' => $_SERVER));
        $request = $wf->newRequest();
        $response = $wf->newResponse();
        foreach ($response->headers->get() as $header) {
            header($header, FALSE);
        }
        $routeDefinitionCallback = function (\FastRoute\RouteCollector $r) {
            $routes = RouteReader::getRoutes();
            foreach ($routes as $route) {
                $r->addRoute($route[0], $route[1], $route[2]);
            }
        };

        $dispatcher = \FastRoute\simpleDispatcher($routeDefinitionCallback);
        $routeInfo = $dispatcher->dispatch($request->method->get(), $request->url->get(PHP_URL_PATH));
        switch ($routeInfo[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                $response->content->set('404 - Page not found');
                $response->status->set('404', 'Not Found', '1.1');
                echo $response->content->get();
                break;
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $response->content->set('405 - Method not allowed');
                $response->status->set('405', 'Not Allowed', '1.1');
                echo $response->content->get();
                break;
            case FastRoute\Dispatcher::FOUND:
                $handler = $routeInfo[1];

                $className = $routeInfo[1][0];
                $method = $routeInfo[1][1];
                $vars = $routeInfo[2];

                $class = new $className($request, $response, $this->session);
                $class->beforeRun();
                $class->$method($vars);
                $class->afterRun();
                break;
        }
    }

    /**
     * Get usage memory
     *
     * @param bool $isPeak
     *
     * @return string
     */
    public static function getMemory($isPeak = TRUE): string
    {
        if ($isPeak) {
            $memory = memory_get_peak_usage(FALSE);
        } else {
            $memory = memory_get_usage(FALSE);
        }
        return $memory;
    }

    public static function iniSet($varName, $newValue)
    {
        return ini_set($varName, $newValue);
    }

    /**
     * Alias fo ini_get function
     *
     * @param string $varName
     *
     * @return mixed
     */
    public static function iniGet($varName)
    {
        return ini_get($varName);
    }

    /**
     * Set PHP execution time limit (doesn't work in safe mode)
     *
     * @param int $newLimit
     */
    public static function setTime($newLimit = 0)
    {
        $newLimit = (int)$newLimit;
        self::iniSet('set_time_limit', $newLimit);
        self::iniSet('max_execution_time', $newLimit);
        set_time_limit($newLimit);
    }

    public static function redirect($location)
    {
        header("Location: $location");
        die();
    }


    /**
     * Set new memory limit
     *
     * @param string $newLimit
     */
    public static function setMemory($newLimit = '256M')
    {
        self::iniSet('memory_limit', $newLimit);
    }

    /**
     * Sets the headers to prevent caching for the different browsers.
     * Different browsers support different nocache headers, so several
     * headers must be sent so that all of them get the point that no caching should occur
     * @return boolean
     * @codeCoverageIgnore
     */
    public static function nocache(): bool
    {
        if (!headers_sent()) {
            header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: no-cache, must-revalidate, max-age=0');
            header('Pragma: no-cache');
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Check is current OS Windows
     * @return bool
     */
    public static function isWin(): bool
    {
        return strncasecmp(PHP_OS, 'WIN', 3) === 0;
    }

    /**
     * Generate Server Specific hash.
     * @method generateServerSpecificHash
     * @return string
     */
    public static function generateServerSpecificHash()
    {
        return (isset($_SERVER['SERVER_NAME']) && !empty($_SERVER['SERVER_NAME'])) ? md5($_SERVER['SERVER_NAME']) : md5(pathinfo(__FILE__, PATHINFO_FILENAME));
    }

    /**
     * Dump information about a variable.
     *
     * @param mixed $variable Variable to debug
     *
     * @return void
     */
    public static function debug($variable)
    {
        ob_start();
        var_dump($variable);
        $output = ob_get_clean();
        $maps = ['string' => "/(string\((?P<length>\d+)\)) (?P<value>\"(?<!\\\).*\")/i", 'array' => "/\[\"(?P<key>.+)\"(?:\:\"(?P<class>[a-z0-9_\\\]+)\")?(?:\:(?P<scope>public|protected|private))?\]=>/Ui", 'countable' => "/(?P<type>array|int|string)\((?P<count>\d+)\)/", 'resource' => "/resource\((?P<count>\d+)\) of type \((?P<class>[a-z0-9_\\\]+)\)/", 'bool' => "/bool\((?P<value>true|false)\)/", 'float' => "/float\((?P<value>[0-9\.]+)\)/", 'object' => "/object\((?P<class>\S+)\)\#(?P<id>\d+) \((?P<count>\d+)\)/i"];
        foreach ($maps as $function => $pattern) {
            $output = preg_replace_callback($pattern, function ($matches) use ($function) {
                switch ($function) {
                    case 'string':
                        $matches['value'] = htmlspecialchars($matches['value']);

                        return '<span style="color: #0000FF;">string</span>(<span style="color: #1287DB;">' . $matches['length'] . ')</span> <span style="color: #6B6E6E;">' . $matches['value'] . '</span>';

                    case 'array':
                        $key = '<span style="color: #008000;">"' . $matches['key'] . '"</span>';
                        $class = '';
                        $scope = '';
                        if (isset($matches['class']) && !empty($matches['class'])) {
                            $class = ':<span style="color: #4D5D94;">"' . $matches['class'] . '"</span>';
                        }
                        if (isset($matches['scope']) && !empty($matches['scope'])) {
                            $scope = ':<span style="color: #666666;">' . $matches['scope'] . '</span>';
                        }

                        return '[' . $key . $class . $scope . ']=>';

                    case 'countable':
                        $type = '<span style="color: #0000FF;">' . $matches['type'] . '</span>';
                        $count = '(<span style="color: #1287DB;">' . $matches['count'] . '</span>)';

                        return $type . $count;

                    case 'bool':
                        return '<span style="color: #0000FF;">bool</span>(<span style="color: #0000FF;">' . $matches['value'] . '</span>)';

                    case 'float':
                        return '<span style="color: #0000FF;">float</span>(<span style="color: #1287DB;">' . $matches['value'] . '</span>)';

                    case 'resource':
                        return '<span style="color: #0000FF;">resource</span>(<span style="color: #1287DB;">' . $matches['count'] . '</span>) of type (<span style="color: #4D5D94;">' . $matches['class'] . '</span>)';

                    case 'object':
                        return '<span style="color: #0000FF;">object</span>(<span style="color: #4D5D94;">' . $matches['class'] . '</span>)#' . $matches['id'] . ' (<span style="color: #1287DB;">' . $matches['count'] . '</span>)';

                }
            }, $output);
        }
        $header = '';
        list($debugfile) = debug_backtrace();

        if (!empty($debugfile['file'])) {
            $header = '<h4 style="border-bottom:1px solid #bbb;font-weight:bold;margin:0 0 10px 0;padding:3px 0 10px 0">' . $debugfile['file'] . '</h4>';
        }

        echo '<pre style="background-color: #CDDCF4;border: 1px solid #bbb;border-radius: 4px;-moz-border-radius:4px;-webkit-border-radius\:4px;font-size:12px;line-height:1.4em;margin:30px;padding:7px">' . $header . $output . '</pre>';
    }

    /**
     * Get the service container instance.
     *
     */
    public static function getContainer()
    {
        if (null === self::$container) {
            self::$container = new Container();
        }

        return self::$container;
    }


    /**
     * Set the service container instance.
     *
     */
    public static function setContainer(Container $container)
    {
        self::$container = $container;
    }


    /**
     * print_r's Variable in <pre> tags.
     *
     * @param mixed $variable variable to print_r
     *
     * @return void
     */
    public static function pr($variable)
    {
        echo '<pre>';
        print_r($variable);
        echo '</pre>';
    }
}
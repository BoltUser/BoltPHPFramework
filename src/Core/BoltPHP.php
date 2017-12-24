<?php
namespace Bolt\Core;

use Bolt\Config\Cache as CacheConfig;
use Bolt\Route\Reader as RouteReader;
use FastRoute;
use Aura\Web\WebFactory;

class BoltPHP
{

    public function __construct()
    {


    }

    public function run()
    {
        if(!defined("BASE_PATH"))
            throw new \Exception('Please Define a BASE_PATH constant in your index file on docroot');

        $wf = new WebFactory(array('_ENV' => $_ENV,'_GET' => $_GET,'_POST' => $_POST,'_COOKIE' => $_COOKIE,'_SERVER' => $_SERVER));
        $request = $wf->newRequest();
        $response = $wf->newResponse();
        foreach($response->headers->get() as $header){
            header($header,FALSE);
        }
        $routeDefinitionCallback = function(\FastRoute\RouteCollector $r){
            $routes = RouteReader::getRoutes();
            foreach($routes as $route){
                $r->addRoute($route[0],$route[1],$route[2]);
            }
        };


        $dispatcher = \FastRoute\simpleDispatcher($routeDefinitionCallback);
        $routeInfo = $dispatcher->dispatch($request->method->get(),$request->url->get(PHP_URL_PATH));
        switch($routeInfo[0]){
            case FastRoute\Dispatcher::NOT_FOUND:
                $response->content->set('404 - Page not found');
                $response->status->set('404','Not Found','1.1');
                echo $response->content->get();
                break;
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $response->content->set('405 - Method not allowed');
                $response->status->set('405','Not Allowed','1.1');
                echo $response->content->get();
                break;
            case FastRoute\Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                $className = $routeInfo[1][0];
                $method = $routeInfo[1][1];
                $vars = $routeInfo[2];

                $class = new $className($request,$response);
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
        if($isPeak){
            $memory = memory_get_peak_usage(FALSE);
        }else{
            $memory = memory_get_usage(FALSE);
        }
        return $memory;
    }

    public static function iniSet($varName,$newValue)
    {
        return ini_set($varName,$newValue);
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
        self::iniSet('set_time_limit',$newLimit);
        self::iniSet('max_execution_time',$newLimit);
        set_time_limit($newLimit);
    }

    /**
     * Strip all witespaces from the given string.
     *
     * @param  string $string The string to strip
     *
     * @return string
     */
    public static function stripSpace($string)
    {
        return preg_replace('/\s+/','',$string);
    }

    /**
     * Converts all accent characters to ASCII characters.
     * If there are no accent characters, then the string given is just
     * returned.
     *
     * @param  string $string Text that might have accent characters
     *
     * @return string Filtered  string with replaced "nice" characters
     */
    public static function removeAccents($string)
    {
        if(!preg_match('/[\x80-\xff]/',$string)){
            return $string;
        }
    }

    /**
     * Sanitize a string by performing the following operation :
     * - Remove accents
     * - Lower the string
     * - Remove punctuation characters
     * - Strip whitespaces
     *
     * @param  string $string the string to sanitize
     *
     * @return string
     */
    public static function sanitizeString($string)
    {
        $string = self::removeAccents($string);
        $string = strtolower($string);
        $string = preg_replace('/[^a-zA-Z 0-9]+/','',$string);
        $string = self::stripSpace($string);
        return $string;
    }

    /**
     * Check if a string contains another string.
     *
     * @param  string $haystack
     * @param  string $needle
     *
     * @return boolean
     */
    public static function contains($haystack,$needle,$caseInsentive = FALSE)
    {
        if($caseInsentive)
            return strpos($haystack,$needle) !== FALSE;else
            return stripos($haystack,$needle) !== FALSE;
    }

    /**
     * Check if a string contains another string. This version is case
     * insensitive.
     *
     * @param  string $haystack
     * @param  string $needle
     *
     * @return boolean
     */
    public static function containsInsensitive($haystack,$needle)
    {
        return self::contains($haystack,$needle,TRUE);
    }


    /**
     * Nice formatting for computer sizes (Bytes).
     *
     * @param   integer $bytes The number in bytes to format
     * @param   integer $decimals The number of decimal points to include
     *
     * @return  string
     */
    public static function sizeFormat($bytes,$decimals = 0)
    {
        $bytes = floatval($bytes);
        if($bytes < 1024){
            return $bytes . ' B';
        }elseif($bytes < pow(1024,2)){
            return number_format($bytes / 1024,$decimals,'.','') . ' KiB';
        }elseif($bytes < pow(1024,3)){
            return number_format($bytes / pow(1024,2),$decimals,'.','') . ' MiB';
        }elseif($bytes < pow(1024,4)){
            return number_format($bytes / pow(1024,3),$decimals,'.','') . ' GiB';
        }elseif($bytes < pow(1024,5)){
            return number_format($bytes / pow(1024,4),$decimals,'.','') . ' TiB';
        }elseif($bytes < pow(1024,6)){
            return number_format($bytes / pow(1024,5),$decimals,'.','') . ' PiB';
        }else{
            return number_format($bytes / pow(1024,5),$decimals,'.','') . ' PiB';
        }
    }

    /**
     * Set new memory limit
     *
     * @param string $newLimit
     */
    public static function setMemory($newLimit = '256M')
    {
        self::iniSet('memory_limit',$newLimit);
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
        if(!headers_sent()){
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
        return strncasecmp(PHP_OS,'WIN',3) === 0;
    }

    /**
     * Get file extension.
     *
     * @param string $filename File path
     *
     * @return string file extension
     */
    public static function getFileExtension($filename)
    {
        return pathinfo($filename,PATHINFO_EXTENSION);
    }


    /**
     * Create HTML A Tag.
     *
     * @param string $link URL or Email address
     * @param string $text Optional, If link text is empty, $link variable value will be used by default
     * @param array  $attributes Optional, additional key/value attributes to include in the IMG tag
     *
     * @return string containing complete a tag
     */
    public static function createLinkTag($link,$text = '',$attributes = [])
    {
        $linkTag = '<a href="' . str_replace(['"',"'"],[urlencode('"'),urlencode("'")],$link) . '"';

        if(self::validateEmail($link)){
            $linkTag = '<a href="mailto:' . $link . '"';
        }

        if(!isset($attributes['title']) && !empty($text)){
            $linkTag .= ' title="' . str_replace('"','',strip_tags($text)) . '" ';
        }

        if(empty($text)){
            $text = $link;
        }

        $attr = trim(self::arrayToString($attributes));
        $linkTag .= $attr . '>' . htmlspecialchars($text,ENT_QUOTES,'UTF-8') . '</a>';

        return $linkTag;
    }

    /**
     * Validate Email address.
     *
     * @param string $address Email address to validate
     * @param bool   $tempEmailAllowed Allow Temporary email addresses?
     *
     * @return bool True if email address is valid, false is returned otherwise
     */
    public static function validateEmail($address,$tempEmailAllowed = TRUE)
    {
        strpos($address,'@') ? list(,$mailDomain) = explode('@',$address) : $mailDomain = NULL;
        if(filter_var($address,FILTER_VALIDATE_EMAIL) && !is_null($mailDomain) && checkdnsrr($mailDomain,'MX')){
            if($tempEmailAllowed){
                return TRUE;
            }else{
                $handle = fopen(__DIR__ . '/banned.txt','r');
                $temp = [];
                while(($line = fgets($handle)) !== FALSE){
                    $temp[] = trim($line);
                }
                if(in_array($mailDomain,$temp)){
                    return FALSE;
                }

                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * Validate URL.
     *
     * @param string $url Website URL
     *
     * @return bool True if URL is valid, false is returned otherwise
     */
    public static function validateURL($url)
    {
        return (bool)filter_var($url,FILTER_VALIDATE_URL);
    }


    /**
     * Convert object to the array.
     *
     * @param object $object PHP object
     *
     * @throws \Exception
     * @return array
     */
    public static function objectToArray($object)
    {
        if(is_object($object)){
            return json_decode(json_encode($object),TRUE);
        }else{
            throw new \Exception('Not an object');
        }
    }

    /**
     * Convert array to the object.
     *
     * @param array $array PHP array
     *
     * @throws \Exception
     * @return object
     */
    public static function arrayToObject(array $array = [])
    {
        if(!is_array($array)){
            throw new \Exception('Not an array');
        }

        $object = new \stdClass();
        if(is_array($array) && count($array) > 0){
            foreach($array as $name => $value){
                if(is_array($value)){
                    $object->{$name} = self::arrayToObject($value);
                }else{
                    $object->{$name} = $value;
                }
            }
        }

        return $object;
    }

    /**
     * Convert Array to string.
     *
     * @param array  $array array to convert to string
     * @param string $delimiter
     *
     * @throws \Exception
     * @return string <key1>="value1" <key2>="value2"
     */
    public static function arrayToString(array $array = [],$delimiter = ' ')
    {
        $pairs = [];
        foreach($array as $key => $value){
            $pairs[] = "$key=\"$value\"";
        }

        return implode($delimiter,$pairs);
    }


    /**
     * Generate Simple Random Password.
     *
     * @param int    $length length of generated password, default 8
     * @param string $customAlphabet a custom alphabet string
     *
     * @return string Generated Password
     */
    public static function generateRandomPassword($length = 8,$customAlphabet = NULL)
    {
        $pass = [];
        if(strlen(trim($customAlphabet))){
            $alphabet = trim($customAlphabet);
        }else{
            $alphabet = 'abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789';
        }

        $alphaLength = strlen($alphabet) - 1;
        for($i = 0;$i < $length;++$i){
            $n = rand(0,$alphaLength);
            $pass[] = $alphabet[$n];
        }

        return implode($pass);
    }

    /**
     * Simple Encode string.
     *
     * @param string $string String you would like to encode
     * @param string $passkey salt for encoding
     *
     * @return string
     */
    public static function simpleEncode($string,$passkey = NULL)
    {
        $key = $passkey;
        if(!isset($passkey) || empty($passkey)){
            $key = self::generateServerSpecificHash();
        }

        $result = '';
        for($i = 0;$i < strlen($string);$i++){
            $char = substr($string,$i,1);
            $keychar = substr($key,($i % strlen($key)) - 1,1);
            $char = chr(ord($char) + ord($keychar));
            $result .= $char;
        }

        return base64_encode($result);
    }

    /**
     * Simple Decode string.
     *
     * @param string $string String encoded via Recipe::simpleEncode()
     * @param string $passkey salt for encoding
     *
     * @return string
     */
    public static function simpleDecode($string,$passkey = NULL)
    {
        $key = $passkey;
        if(!isset($passkey) || empty($passkey)){
            $key = self::generateServerSpecificHash();
        }

        $result = '';
        $string = base64_decode($string);
        for($i = 0;$i < strlen($string);$i++){
            $char = substr($string,$i,1);
            $keychar = substr($key,($i % strlen($key)) - 1,1);
            $char = chr(ord($char) - ord($keychar));
            $result .= $char;
        }

        return $result;
    }

    /**
     * Generate Server Specific hash.
     * @method generateServerSpecificHash
     * @return string
     */
    public static function generateServerSpecificHash()
    {
        return (isset($_SERVER['SERVER_NAME']) && !empty($_SERVER['SERVER_NAME'])) ? md5($_SERVER['SERVER_NAME']) : md5(pathinfo(__FILE__,PATHINFO_FILENAME));
    }


    /**
     * Check if number is odd.
     *
     * @param int $num integer to check
     *
     * @return bool
     */
    public static function isNumberOdd($num)
    {
        return $num % 2 !== 0;
    }

    /**
     * Check if number is even.
     *
     * @param int $num integer to check
     *
     * @return bool
     */
    public static function isNumberEven($num)
    {
        return $num % 2 == 0;
    }


    /**
     * Truncate String (shorten) with or without ellipsis.
     *
     * @param string $string String to truncate
     * @param int    $maxLength Maximum length of string
     * @param bool   $addEllipsis if True, "..." is added in the end of the string, default true
     * @param bool   $wordsafe if True, Words will not be cut in the middle
     *
     * @return string Shortened Text
     */
    public static function shortenString($string,$maxLength,$addEllipsis = TRUE,$wordsafe = FALSE)
    {
        $ellipsis = '';
        $maxLength = max($maxLength,0);

        if(mb_strlen($string) <= $maxLength){
            return $string;
        }

        if($addEllipsis){
            $ellipsis = mb_substr('...',0,$maxLength);
            $maxLength -= mb_strlen($ellipsis);
            $maxLength = max($maxLength,0);
        }

        $string = mb_substr($string,0,$maxLength);

        if($wordsafe){
            $string = preg_replace('/\s+?(\S+)?$/','',mb_substr($string,0,$maxLength));
        }

        if($addEllipsis){
            $string .= $ellipsis;
        }

        return $string;
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
        $maps = ['string' => "/(string\((?P<length>\d+)\)) (?P<value>\"(?<!\\\).*\")/i",'array' => "/\[\"(?P<key>.+)\"(?:\:\"(?P<class>[a-z0-9_\\\]+)\")?(?:\:(?P<scope>public|protected|private))?\]=>/Ui",'countable' => "/(?P<type>array|int|string)\((?P<count>\d+)\)/",'resource' => "/resource\((?P<count>\d+)\) of type \((?P<class>[a-z0-9_\\\]+)\)/",'bool' => "/bool\((?P<value>true|false)\)/",'float' => "/float\((?P<value>[0-9\.]+)\)/",'object' => "/object\((?P<class>\S+)\)\#(?P<id>\d+) \((?P<count>\d+)\)/i"];
        foreach($maps as $function => $pattern){
            $output = preg_replace_callback($pattern,function($matches) use ($function){
                switch($function){
                    case 'string':
                        $matches['value'] = htmlspecialchars($matches['value']);

                        return '<span style="color: #0000FF;">string</span>(<span style="color: #1287DB;">' . $matches['length'] . ')</span> <span style="color: #6B6E6E;">' . $matches['value'] . '</span>';

                    case 'array':
                        $key = '<span style="color: #008000;">"' . $matches['key'] . '"</span>';
                        $class = '';
                        $scope = '';
                        if(isset($matches['class']) && !empty($matches['class'])){
                            $class = ':<span style="color: #4D5D94;">"' . $matches['class'] . '"</span>';
                        }
                        if(isset($matches['scope']) && !empty($matches['scope'])){
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
            },$output);
        }
        $header = '';
        list($debugfile) = debug_backtrace();

        if(!empty($debugfile['file'])){
            $header = '<h4 style="border-bottom:1px solid #bbb;font-weight:bold;margin:0 0 10px 0;padding:3px 0 10px 0">' . $debugfile['file'] . '</h4>';
        }

        echo '<pre style="background-color: #CDDCF4;border: 1px solid #bbb;border-radius: 4px;-moz-border-radius:4px;-webkit-border-radius\:4px;font-size:12px;line-height:1.4em;margin:30px;padding:7px">' . $header . $output . '</pre>';
    }

    /**
     *  Takes a number and adds “th, st, nd, rd, th” after it.
     *
     * @param int $cardinal Number to add termination
     *
     * @return string
     */
    public static function ordinal($cardinal)
    {
        $test_c = abs($cardinal) % 10;
        $ext = ((abs($cardinal) % 100 < 21 && abs($cardinal) % 100 > 4) ? 'th' : (($test_c < 4) ? ($test_c < 3) ? ($test_c < 2) ? ($test_c < 1) ? 'th' : 'st' : 'nd' : 'rd' : 'th'));

        return $cardinal . $ext;
    }

    /**
     * Returns the number of days for the given month and year.
     *
     * @param int $month Month to check
     * @param int $year Year to check
     *
     * @return int
     */
    public static function numberOfDaysInMonth($month = 0,$year = 0)
    {
        if($month < 1 or $month > 12){
            return 0;
        }

        if(!is_numeric($year) or strlen($year) != 4){
            $year = date('Y');
        }

        if($month == 2){
            if($year % 400 == 0 or ($year % 4 == 0 and $year % 100 != 0)){
                return 29;
            }
        }

        $days_in_month = [31,28,31,30,31,30,31,31,30,31,30,31];

        return $days_in_month[$month - 1];
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

    /**
     * Sanitize FileName from special chart.
     * @method sanitizeFileName
     *
     * @param string $filename filename to sanitize
     *
     * @return string Sanitized filename
     */
    public static function sanitizeFileName($filename)
    {
        return str_replace([' ','"',"'",'&','/','\\','?','#'],'_',$filename);
    }


}
<?php

namespace Bolt\Core;

class Str
{
    /**
     * Default charset is UTF-8
     * @var string
     */
    public static $encoding = 'UTF-8';

    public static function ArrayToString(array $aArray)
    {
        $sString = '';
        foreach ($aArray as $string) {
            $sString .= $string . " ";
        }
        return $sString;
    }

    /**
     * Parse text by lines
     *
     * @param string $text
     * @param bool $toAssoc
     *
     * @return array
     */
    public static function parseLines($text, $toAssoc = TRUE): array
    {
        $text = htmlspecialchars_decode($text);
        $text = self::clean($text);

        $text = str_replace(["\n", "\r", "\r\n", PHP_EOL], "\n", $text);
        $lines = explode("\n", $text);

        $result = [];
        if (!empty($lines)) {
            foreach ($lines as $line) {
                $line = trim($line);

                if ($line === '') {
                    continue;
                }

                if ($toAssoc) {
                    $result[$line] = $line;
                } else {
                    $result[] = $line;
                }
            }
        }

        return $result;
    }

    public static function convertToReadableSize($size)
    {
        $base = log($size) / log(1024);
        $suffix = array("", "KB", "MB", "GB", "TB");
        $f_base = floor($base);
        return round(pow(1024, $base - floor($base)), 1) . $suffix[$f_base];
    }

    /**
     * Make string safe
     * - Remove UTF-8 chars
     * - Remove all tags
     * - Trim
     * - Add Slashes (opt)
     * - To lower (opt)
     *
     * @param string $string
     * @param bool $toLower
     * @param bool $addSlashes
     *
     * @return string
     */
    public static function clean($string, $toLower = FALSE, $addSlashes = FALSE): string
    {
        $string = strip_tags($string);
        $string = trim($string);

        if ($addSlashes) {
            $string = addslashes($string);
        }

        if ($toLower) {
            $string = self::low($string);
        }

        return $string;
    }

    public static function getDomain($sEmail){
        $aDomain = explode('@',$sEmail);
        return $aDomain[1];
    }

    public static function getEmailAccount($sEmail){
        $aDomain = explode('@',$sEmail);
        return $aDomain[0];
    }

    public static function sanitizeEmail($email){
        $email = str_replace(' ','',$email);
        $email = str_replace('.@','@',$email);
        $email = str_replace('.@','@',$email);
        $email = str_replace('.@','@',$email);
        $email = str_replace('@.','@',$email);
        $email = str_replace('@.','@',$email);
        $email = str_replace('@.','@',$email);
        $email = str_replace('@@','@',$email);
        $email = str_replace('-@-@','@',$email);
        $email = str_replace('^','',$email);
        $email = str_replace(')','',$email);
        $email = str_replace('(','',$email);
        $email = str_replace('...','.',$email);
        $email = str_replace('..','.',$email);
        $email = str_replace('[','',$email);
        $email = str_replace('{','',$email);
        $email = str_replace('}','',$email);
        $email = str_replace(']','',$email);
        $email = str_replace('<','',$email);
        $email = str_replace('>','',$email);
        $email = str_replace('*','',$email);
        $email = str_replace('�','',$email);
        $email = str_replace('?','',$email);
        $email = str_replace(':','',$email);

        return $email;
    }

    public static function getExt($sDomain){
        $aExt = explode('.',$sDomain);
        $aExt = array_reverse($aExt);
        return $aExt[0];
    }

    public static function startsWithBis($string, $findme){
        $length = strlen($findme);
        return (substr($string, 0, $length) === $findme);
    }

    public static function endsWithBis($string, $findme){
        $length = strlen($findme);
        return $length === 0 || (substr($string, -$length) === $findme);
    }

    public static function isEmailValid($sEmail){
        if (filter_var($sEmail, FILTER_VALIDATE_EMAIL)) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Make a string lowercase
     *
     * @param string $string
     *
     * @return string
     */
    public static function low($string): string
    {
        if (self::isMBString()) {
            return mb_strtolower($string, self::$encoding);
        }

        return strtolower($string); // @codeCoverageIgnore
    }

    /**
     * Check is mbstring loaded
     * @return bool
     */
    public static function isMBString(): bool
    {
        static $isLoaded;

        if (NULL === $isLoaded) {
            $isLoaded = extension_loaded('mbstring');

            if ($isLoaded) {
                mb_internal_encoding(self::$encoding);
            }
        }

        return $isLoaded;
    }

    /**
     * Convert >, <, ', " and & to html entities, but preserves entities that are already encoded.
     *
     * @param string $string The text to be converted
     * @param bool $encodedEntities
     *
     * @return string
     */
    public static function htmlEnt($string, $encodedEntities = FALSE): string
    {
        if ($encodedEntities) {
            // @codeCoverageIgnoreStart
            if (defined('HHVM_VERSION')) {
                $transTable = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
            } else {
                /** @noinspection PhpMethodParametersCountMismatchInspection */
                $transTable = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES, self::$encoding);
            }
            // @codeCoverageIgnoreEnd

            $transTable[chr(38)] = '&';

            $regExp = '/&(?![A-Za-z]{0,4}\w{2,3};|#[\d]{2,3};)/';

            return preg_replace($regExp, '&amp;', strtr($string, $transTable));
        }

        return htmlentities($string, ENT_QUOTES, self::$encoding);
    }

    /**
     * Get unique string
     * @param  string $prefix
     * @return string
     */
    public static function unique($prefix = 'unique'): string
    {
        $prefix = rtrim(trim($prefix), '-');
        $random = random_int(10000000, 99999999);

        $result = $random;
        if ($prefix) {
            $result = $prefix . '-' . $random;
        }

        return $result;
    }

    /**
     * Generate readable random string
     * @param int $length
     * @param bool $isReadable
     * @return string
     */
    public static function random($length = 10, $isReadable = TRUE): string
    {
        $result = '';

        if ($isReadable) {
            $vowels = ['a', 'e', 'i', 'o', 'u', '0'];
            $consonants = ['b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'p', 'r', 's', 't', 'v', 'w', 'x', 'y', 'z', '1', '2', '3', '4', '5', '6', '7', '8', '9',];

            $max = $length / 2;

            for ($pos = 1; $pos <= $max; $pos++) {
                $result .= $consonants[random_int(0, count($consonants) - 1)];
                $result .= $vowels[random_int(0, count($vowels) - 1)];
            }
        } else {
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

            for ($pos = 0; $pos < $length; $pos++) {
                $result .= $chars[mt_rand() % strlen($chars)];
            }
        }

        return $result;
    }

    /**
     * Pads a given string with zeroes on the left.
     * @param  int $number The number to pad
     * @param  int $length The total length of the desired string
     * @return string
     */
    public static function zeroPad($number, $length): string
    {
        return str_pad($number, $length, '0', STR_PAD_LEFT);
    }

    /**
     * Truncate a string to a specified length without cutting a word off.
     *
     * @param   string $string The string to truncate
     * @param   integer $length The length to truncate the string to
     * @param   string $append Text to append to the string IF it gets truncated, defaults to '...'
     *
     * @return  string
     */
    public static function truncateSafe($string, $length, $append = '...'): string
    {
        $result = self::sub($string, 0, $length);
        $lastSpace = self::rPos($result, ' ');

        if ($lastSpace !== FALSE && $string !== $result) {
            $result = self::sub($result, 0, $lastSpace);
        }

        if ($result !== $string) {
            $result .= $append;
        }

        return $result;
    }

    /**
     * Get part of string
     *
     * @param string $string
     * @param int $start
     * @param int $length
     *
     * @return string
     */
    public static function sub($string, $start, $length = 0)
    {
        if (self::isMBString()) {
            if (0 === $length) {
                $length = self::len($string);
            }

            return mb_substr($string, $start, $length, self::$encoding);
        }

        return substr($string, $start, $length); // @codeCoverageIgnore
    }

    /**
     * Get string length
     *
     * @param $string
     *
     * @return int
     */
    public static function len($string): int
    {
        if (self::isMBString()) {
            return mb_strlen($string, self::$encoding);
        }

        return strlen($string); // @codeCoverageIgnore
    }

    /**
     * Find position of last occurrence of a string in a string
     *
     * @param string $haystack
     * @param string $needle
     * @param int $offset
     *
     * @return int
     */
    public static function rPos($haystack, $needle, $offset = 0)
    {
        if (self::isMBString()) {
            return mb_strrpos($haystack, $needle, $offset, self::$encoding);
        }

        return strrpos($haystack, $needle, $offset); // @codeCoverageIgnore
    }

    /**
     * Truncate the string to given length of characters.
     *
     * @param string $string The variable to truncate
     * @param integer $limit The length to truncate the string to
     * @param string $append Text to append to the string IF it gets truncated, defaults to '...'
     *
     * @return string
     */
    public static function limitChars($string, $limit = 100, $append = '...'): string
    {
        if (self::len($string) <= $limit) {
            return $string;
        }

        return rtrim(self::sub($string, 0, $limit)) . $append;
    }

    /**
     * Truncate the string to given length of words.
     *
     * @param string $string
     * @param int $limit
     * @param string $append
     *
     * @return string
     */
    public static function limitWords($string, $limit = 100, $append = '...'): string
    {
        preg_match('/^\s*+(?:\S++\s*+){1,' . $limit . '}/u', $string, $matches);

        if (!Arr::key(0, $matches) || self::len($string) === self::len($matches[0])) {
            return $string;
        }

        return rtrim($matches[0]) . $append;
    }

    /**
     * Check if a given string matches a given pattern.
     *
     * @param  string $pattern Pattern of string expected
     * @param  string $string String that need to be matched
     * @param  bool $caseSensitive
     *
     * @return bool
     */
    public static function like($pattern, $string, $caseSensitive = TRUE): bool
    {
        if ($pattern === $string) {
            return TRUE;
        }

        // Preg flags
        $flags = $caseSensitive ? '' : 'i';

        // Escape any regex special characters
        $pattern = preg_quote($pattern, '#');

        // Unescaped * which is our wildcard character and change it to .*
        $pattern = str_replace('\*', '.*', $pattern);

        return (bool)preg_match('#^' . $pattern . '$#' . $flags, $string);
    }

    /**
     * Finds first occurrence of a string within another
     *
     * @param string $haystack
     * @param string $needle
     * @param bool $beforeNeedle
     *
     * @return string
     */
    public static function strStr($haystack, $needle, $beforeNeedle = FALSE)
    {
        if (self::isMBString()) {
            return mb_strstr($haystack, $needle, $beforeNeedle, self::$encoding);
        }

        return strstr($haystack, $needle, $beforeNeedle); // @codeCoverageIgnore
    }

    /**
     * Finds first occurrence of a string within another, case insensitive
     *
     * @param string $haystack
     * @param string $needle
     * @param bool $beforeNeedle
     *
     * @return string
     */
    public static function iStr($haystack, $needle, $beforeNeedle = FALSE)
    {
        if (self::isMBString()) {
            return mb_stristr($haystack, $needle, $beforeNeedle, self::$encoding);
        }

        return stristr($haystack, $needle, $beforeNeedle); // @codeCoverageIgnore
    }

    /**
     * Finds the last occurrence of a character in a string within another
     *
     * @param string $haystack
     * @param string $needle
     * @param bool $part
     *
     * @return string
     */
    public static function rChr($haystack, $needle, $part = NULL)
    {
        if (self::isMBString()) {
            return mb_strrchr($haystack, $needle, $part, self::$encoding);
        }

        return strrchr($haystack, $needle); // @codeCoverageIgnore
    }

    /**
     * Make a string uppercase
     *
     * @param string $string
     *
     * @return string
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public static function up($string): string
    {
        if (self::isMBString()) {
            return mb_strtoupper($string, self::$encoding);
        }

        return strtoupper($string); // @codeCoverageIgnore
    }

    /**
     * Count the number of substring occurrences
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return int
     */
    public static function subCount($haystack, $needle)
    {
        if (self::isMBString()) {
            return mb_substr_count($haystack, $needle, self::$encoding);
        }

        return substr_count($haystack, $needle); // @codeCoverageIgnore
    }

    /**
     * Checks if the $haystack starts with the text in the $needle.
     *
     * @param string $haystack
     * @param string $needle
     * @param bool $caseSensitive
     *
     * @return bool
     */
    public static function startWith($haystack, $needle, $caseSensitive = FALSE): bool
    {
        if ($caseSensitive) {
            return $needle === '' || self::pos($haystack, $needle) === 0;
        }

        return $needle === '' || self::iPos($haystack, $needle) === 0;
    }

    /**
     * Find position of first occurrence of string in a string
     *
     * @param string $haystack
     * @param string $needle
     * @param int $offset
     *
     * @return int
     */
    public static function pos($haystack, $needle, $offset = 0)
    {
        if (self::isMBString()) {
            return mb_strpos($haystack, $needle, $offset, self::$encoding);
        }

        return strpos($haystack, $needle, $offset);
    }

    /**
     * Finds position of first occurrence of a string within another, case insensitive
     *
     * @param string $haystack
     * @param string $needle
     * @param int $offset
     *
     * @return int
     */
    public static function iPos($haystack, $needle, $offset = 0)
    {
        if (self::isMBString()) {
            return mb_stripos($haystack, $needle, $offset, self::$encoding);
        }

        return stripos($haystack, $needle, $offset); // @codeCoverageIgnore
    }

    /**
     * Checks if the $haystack ends with the text in the $needle. Case sensitive.
     *
     * @param string $haystack
     * @param string $needle *
     * @return bool
     */
    public static function endWith($haystack, $needle): bool
    {
        return strrpos($haystack, $needle) === strlen($haystack) - strlen($needle);
    }

    /**
     * Checks if the $haystack ends with the text in the $needle. Case sensitive.
     *
     * @param string $haystack
     * @param string $needle *
     * @return bool
     */
    public static function endWith2($haystack, $needle): bool
    {
        return substr($haystack, strlen($needle) * -1) == $needle;
    }

    /**
     * Trim whitespaces and other special chars
     *
     * @param string $value
     * @param bool $extendMode
     *
     * @return string
     */
    public static function trim($value, $extendMode = FALSE): string
    {
        $result = (string)trim($value);

        if ($extendMode) {
            $result = trim($result, chr(0xE3) . chr(0x80) . chr(0x80));
            $result = trim($result, chr(0xC2) . chr(0xA0));
            $result = trim($result);
        }

        return $result;
    }

    /**
     * Escape string before save it as xml content
     *
     * @param $string
     *
     * @return mixed
     */
    public static function escXml($string)
    {
        $string = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $string);

        $string = str_replace(['&', '<', '>', '"', "'"], ['&amp;', '&lt;', '&gt;', '&quot;', '&apos;'], $string);

        return $string;
    }

    /**
     * Escape UTF-8 strings
     *
     * @param string $string
     *
     * @return string
     */
    public static function esc($string): string
    {
        return htmlspecialchars($string, ENT_NOQUOTES, self::$encoding);
    }

    /**
     * Convert camel case to human readable format
     *
     * @param string $input
     * @param string $separator
     * @param bool $toLower *
     *
     * @return string
     */
    public static function splitCamelCase($input, $separator = '_', $toLower = TRUE): string
    {
        $original = $input;

        $output = preg_replace(['/(?<=[^A-Z])([A-Z])/', '/(?<=[^0-9])([0-9])/'], '_$0', $input);
        $output = preg_replace('#_{1,}#', $separator, $output);

        $output = trim($output);
        if ($toLower) {
            $output = strtolower($output);
        }

        if ('' === $output) {
            return $original;
        }

        return $output;
    }

    /**
     * Convert test name to human readable string
     *
     * @param string $input
     *
     * @return mixed|string
     */
    public static function testName2Human($input)
    {
        $original = $input;
        $input = self::getClassName($input);

        /** @noinspection NotOptimalRegularExpressionsInspection */
        if (!preg_match('#^tests#i', $input)) {
            $input = preg_replace('#^(test)#i', '', $input);
        }

        $input = preg_replace('#(test)$#i', '', $input);
        $output = preg_replace(['/(?<=[^A-Z])([A-Z])/', '/(?<=[^0-9])([0-9])/'], ' $0', $input);
        $output = trim(str_replace('_', ' ', $output));

        $output = implode(' ', array_filter(array_map(function ($item) {
            $item = ucwords($item);
            $item = trim($item);

            return $item;
        }, explode(' ', $output))));


        if (strcasecmp($original, $output) === 0) {
            return $original;
        }

        if ('' === $output) {
            return $original;
        }

        return $output;
    }

    /**
     * Get class name without namespace
     *
     * @param mixed $object
     * @param bool $toLower
     *
     * @return mixed|string
     */
    public static function getClassName($object, $toLower = FALSE)
    {
        $className = $object;
        if (is_object($object)) {
            $className = get_class($object);
        }

        $result = $className;
        if (strpos($className, '\\') !== FALSE) {
            $className = explode('\\', $className);
            reset($className);
            $result = end($className);
        }

        if ($toLower) {
            $result = strtolower($result);
        }

        return $result;
    }

    /**
     * Generates a universally unique identifier (UUID v4) according to RFC 4122
     * Version 4 UUIDs are pseudo-random!
     * Returns Version 4 UUID format: xxxxxxxx-xxxx-4xxx-Yxxx-xxxxxxxxxxxx where x is
     * any random hex digit and Y is a random choice from 8, 9, a, or b.
     * @see http://stackoverflow.com/questions/2040240/php-function-to-generate-v4-uuid
     * @return string
     */
    public static function uuid(): string
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',// 32 bits for "time_low"
            random_int(0, 0xffff), random_int(0, 0xffff),// 16 bits for "time_mid"
            random_int(0, 0xffff),// 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            random_int(0, 0x0fff) | 0x4000,// 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            random_int(0, 0x3fff) | 0x8000,// 48 bits for "node"
            random_int(0, 0xffff), random_int(0, 0xffff), random_int(0, 0xffff));
    }

    /**
     * Increments a trailing number in a string.
     * Used to easily create distinct labels when copying objects. The method has the following styles:
     *  - default: "Label" becomes "Label (2)"
     *  - dash:    "Label" becomes "Label-2"
     *
     * @param   string $string The source string.
     * @param   string $style The the style (default|dash).
     * @param   integer $next If supplied, this number is used for the copy, otherwise it is the 'next' number.
     *
     * @return  string
     */
    public static function inc($string, $style = 'default', $next = 0): string
    {
        $styles = ['dash' => ['#-(\d+)$#', '-%d'], 'default' => [['#\((\d+)\)$#', '#\(\d+\)$#'], [' (%d)', '(%d)']],];

        $styleSpec = $styles[$style] ?? $styles['default'];

        // Regular expression search and replace patterns.
        if (is_array($styleSpec[0])) {
            $rxSearch = $styleSpec[0][0];
            $rxReplace = $styleSpec[0][1];
        } else {
            $rxSearch = $rxReplace = $styleSpec[0];
        }

        // New and old (existing) sprintf formats.
        if (is_array($styleSpec[1])) {
            $newFormat = $styleSpec[1][0];
            $oldFormat = $styleSpec[1][1];
        } else {
            $newFormat = $oldFormat = $styleSpec[1];
        }

        // Check if we are incrementing an existing pattern, or appending a new one.
        if (preg_match($rxSearch, $string, $matches)) {
            $next = empty($next) ? ($matches[1] + 1) : $next;
            $string = preg_replace($rxReplace, sprintf($oldFormat, $next), $string);
        } else {
            $next = empty($next) ? 2 : $next;
            $string .= sprintf($newFormat, $next);
        }

        return $string;
    }

    /**
     * Splits a string of multiple queries into an array of individual queries.
     * Single line or line end comments and multi line comments are stripped off.
     *
     * @param   string $sql Input SQL string with which to split into individual queries.
     *
     * @return  array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public static function splitSql($sql): array
    {
        $start = 0;
        $open = FALSE;
        $comment = FALSE;
        $endString = '';
        $end = strlen($sql);
        $queries = [];
        $query = '';

        /** @noinspection ForeachInvariantsInspection */
        for ($i = 0; $i < $end; $i++) {
            $current = $sql[$i];
            $current2 = substr($sql, $i, 2);
            $current3 = substr($sql, $i, 3);
            $lenEndString = strlen($endString);
            $testEnd = substr($sql, $i, $lenEndString);

            if ($current === '"' || $current === "'" || $current2 === '--' || ($current2 === '/*' && $current3 !== '/*!' && $current3 !== '/*+') || ($current === '#' && $current3 !== '#__') || ($comment && $testEnd === $endString)) {
                // Check if quoted with previous backslash
                $num = 2;

                while ($sql[$i - $num + 1] === '\\' && $num < $i) {
                    $num++;
                }

                // Not quoted
                if ($num % 2 === 0) {
                    if ($open) {
                        if ($testEnd === $endString) {
                            if ($comment) {
                                $comment = FALSE;
                                if ($lenEndString > 1) {
                                    $i += ($lenEndString - 1);
                                    $current = $sql[$i];
                                }
                                $start = $i + 1;
                            }
                            $open = FALSE;
                            $endString = '';
                        }
                    } else {
                        $open = TRUE;
                        if ('--' === $current2) {
                            $endString = "\n";
                            $comment = TRUE;
                        } elseif ('/*' === $current2) {
                            $endString = '*/';
                            $comment = TRUE;
                        } elseif ('#' === $current) {
                            $endString = "\n";
                            $comment = TRUE;
                        } else {
                            $endString = $current;
                        }
                        if ($comment && $start < $i) {
                            $query .= substr($sql, $start, $i - $start);
                        }
                    }
                }
            }

            if ($comment) {
                $start = $i + 1;
            }

            if (($current === ';' && !$open) || $i === $end - 1) {
                if ($start <= $i) {
                    $query .= substr($sql, $start, $i - $start + 1);
                }
                $query = trim($query);

                if ($query) {
                    if ($current !== ';') {
                        $query .= ';';
                    }
                    $queries[] = $query;
                }

                $query = '';
                $start = $i + 1;
            }
        }

        return $queries;
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
    public static function containsInsensitive($haystack, $needle)
    {
        return self::contains($haystack, $needle, TRUE);
    }

    /**
     * Check if a string contains another string.
     *
     * @param  string $haystack
     * @param  string $needle
     *
     * @return boolean
     */
    public static function contains($haystack, $needle, $caseInsentive = FALSE)
    {
        if ($caseInsentive) {
            if (strpos($haystack, $needle) !== FALSE)
                return TRUE;
            else
                return FALSE;
        } else {
            if (stripos($haystack, $needle) !== FALSE)
                return TRUE;
            else
                return FALSE;
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
        $string = preg_replace('/[^a-zA-Z 0-9]+/', '', $string);
        $string = self::stripSpace($string);
        return $string;
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
        if (!preg_match('/[\x80-\xff]/', $string)) {
            return $string;
        }
    }

    /**
     * Strip all whitespaces from the given string.
     * @param  string $string The string to strip
     *
     * @return string
     */
    public static function stripSpace($string): string
    {
        return preg_replace('/\s+/', '', $string);
    }

    /**
     * Simple Encode string.
     *
     * @param string $string String you would like to encode
     * @param string $passkey salt for encoding
     *
     * @return string
     */
    public static function simpleEncode($string, $passkey = NULL)
    {
        $key = $passkey;
        if (!isset($passkey) || empty($passkey)) {
            $key = self::generateServerSpecificHash();
        }

        $result = '';
        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key)) - 1, 1);
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
    public static function simpleDecode($string, $passkey = NULL)
    {
        $key = $passkey;
        if (!isset($passkey) || empty($passkey)) {
            $key = self::generateServerSpecificHash();
        }

        $result = '';
        $string = base64_decode($string);
        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key)) - 1, 1);
            $char = chr(ord($char) - ord($keychar));
            $result .= $char;
        }

        return $result;
    }

    /**
     * Truncate String (shorten) with or without ellipsis.
     *
     * @param string $string String to truncate
     * @param int $maxLength Maximum length of string
     * @param bool $addEllipsis if True, "..." is added in the end of the string, default true
     * @param bool $wordsafe if True, Words will not be cut in the middle
     *
     * @return string Shortened Text
     */
    public static function shortenString($string, $maxLength, $addEllipsis = TRUE, $wordsafe = FALSE)
    {
        $ellipsis = '';
        $maxLength = max($maxLength, 0);

        if (mb_strlen($string) <= $maxLength) {
            return $string;
        }

        if ($addEllipsis) {
            $ellipsis = mb_substr('...', 0, $maxLength);
            $maxLength -= mb_strlen($ellipsis);
            $maxLength = max($maxLength, 0);
        }

        $string = mb_substr($string, 0, $maxLength);

        if ($wordsafe) {
            $string = preg_replace('/\s+?(\S+)?$/', '', mb_substr($string, 0, $maxLength));
        }

        if ($addEllipsis) {
            $string .= $ellipsis;
        }

        return $string;
    }
}

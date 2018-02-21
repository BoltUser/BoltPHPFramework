<?php
/**
 * Created by PhpStorm.
 * User: lordmaster
 * Date: 21/02/18
 * Time: 12.49
 */

namespace Bolt\Core;


class Utility
{

    /**
     * Nice formatting for computer sizes (Bytes).
     *
     * @param   integer $bytes The number in bytes to format
     * @param   integer $decimals The number of decimal points to include
     *
     * @return  string
     */
    public static function sizeFormat($bytes, $decimals = 0)
    {
        $bytes = floatval($bytes);
        if ($bytes < 1024) {
            return $bytes . ' B';
        } elseif ($bytes < pow(1024, 2)) {
            return number_format($bytes / 1024, $decimals, '.', '') . ' KiB';
        } elseif ($bytes < pow(1024, 3)) {
            return number_format($bytes / pow(1024, 2), $decimals, '.', '') . ' MiB';
        } elseif ($bytes < pow(1024, 4)) {
            return number_format($bytes / pow(1024, 3), $decimals, '.', '') . ' GiB';
        } elseif ($bytes < pow(1024, 5)) {
            return number_format($bytes / pow(1024, 4), $decimals, '.', '') . ' TiB';
        } elseif ($bytes < pow(1024, 6)) {
            return number_format($bytes / pow(1024, 5), $decimals, '.', '') . ' PiB';
        } else {
            return number_format($bytes / pow(1024, 5), $decimals, '.', '') . ' PiB';
        }
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
        return pathinfo($filename, PATHINFO_EXTENSION);
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
        if (is_object($object)) {
            return json_decode(json_encode($object), TRUE);
        } else {
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
        if (!is_array($array)) {
            throw new \Exception('Not an array');
        }

        $object = new \stdClass();
        if (is_array($array) && count($array) > 0) {
            foreach ($array as $name => $value) {
                if (is_array($value)) {
                    $object->{$name} = self::arrayToObject($value);
                } else {
                    $object->{$name} = $value;
                }
            }
        }

        return $object;
    }

    /**
     * Generate Simple Random Password.
     *
     * @param int $length length of generated password, default 8
     * @param string $customAlphabet a custom alphabet string
     *
     * @return string Generated Password
     */
    public static function generateRandomPassword($length = 8, $customAlphabet = NULL)
    {
        $pass = [];
        if (strlen(trim($customAlphabet))) {
            $alphabet = trim($customAlphabet);
        } else {
            $alphabet = 'abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789';
        }

        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < $length; ++$i) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }

        return implode($pass);
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
    public static function numberOfDaysInMonth($month = 0, $year = 0)
    {
        if ($month < 1 or $month > 12) {
            return 0;
        }

        if (!is_numeric($year) or strlen($year) != 4) {
            $year = date('Y');
        }

        if ($month == 2) {
            if ($year % 400 == 0 or ($year % 4 == 0 and $year % 100 != 0)) {
                return 29;
            }
        }

        $days_in_month = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

        return $days_in_month[$month - 1];
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
        return str_replace([' ', '"', "'", '&', '/', '\\', '?', '#'], '_', $filename);
    }
}
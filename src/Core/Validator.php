<?php
/**
 * Created by PhpStorm.
 * User: lordmaster
 * Date: 21/02/18
 * Time: 12.53
 */

namespace Bolt\Core;


class Validator
{
    /**
     * Validate Email address.
     *
     * @param string $address Email address to validate
     * @param bool $tempEmailAllowed Allow Temporary email addresses?
     *
     * @return bool True if email address is valid, false is returned otherwise
     */
    public static function validateEmail($address, $tempEmailAllowed = TRUE)
    {
        strpos($address, '@') ? list(, $mailDomain) = explode('@', $address) : $mailDomain = NULL;
        if (filter_var($address, FILTER_VALIDATE_EMAIL) && !is_null($mailDomain) && checkdnsrr($mailDomain, 'MX')) {
            if ($tempEmailAllowed) {
                return TRUE;
            } else {
                $handle = fopen(__DIR__ . '/banned.txt', 'r');
                $temp = [];
                while (($line = fgets($handle)) !== FALSE) {
                    $temp[] = trim($line);
                }
                if (in_array($mailDomain, $temp)) {
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
        return (bool)filter_var($url, FILTER_VALIDATE_URL);
    }
}
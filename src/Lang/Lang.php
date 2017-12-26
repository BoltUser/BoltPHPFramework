<?php
/**
 * Date: 26/12/17
 * Time: 22.18
 */

namespace Bolt\Lang;


class Lang
{
    /**
     * @param key
     *
     * @return element translated
     */
    public function translate($key)
    {
        if (count($this->langElements) > 0) {
            return $this->langElements[$key];
        }
    }

    /**
     * @param fileLang
     */
    public function register($fileLang)
    {
        if (file_exists($fileLang))
            $this->langElements = include_once($fileLang);
    }


}
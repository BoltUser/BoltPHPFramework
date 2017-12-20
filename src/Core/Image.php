<?php

namespace Bolt\Core;


class Image
{

    /**
     * Takes HEX color code value and converts to a RGB value.
     *
     * @param string $color Color hex value, example: #000000, #000 or 000000, 000
     *
     * @return string color rbd value
     */
    public static function hex2rgb($color)
    {
        $color = str_replace('#','',$color);

        $hex = strlen($color) == 3 ? [$color[0] . $color[0],$color[1] . $color[1],$color[2] . $color[2]] : [$color[0] . $color[1],$color[2] . $color[3],$color[4] . $color[5]];

        list($r,$g,$b) = $hex;

        return sprintf('rgb(%s, %s, %s)',hexdec($r),hexdec($g),hexdec($b));
    }

    /**
     * @param string $format
     *
     * @return bool
     */
    public static function isJpeg($format): bool
    {
        $format = strtolower($format);
        return 'image/jpg' === $format || 'jpg' === $format || 'image/jpeg' === $format || 'jpeg' === $format;
    }

    /**
     * @param string $format
     *
     * @return bool
     */
    public static function isGif($format): bool
    {
        $format = strtolower($format);
        return 'image/gif' === $format || 'gif' === $format;
    }

    /**
     * @param string $format
     *
     * @return bool
     */
    public static function isPng($format): bool
    {
        $format = strtolower($format);
        return 'image/png' === $format || 'png' === $format;
    }

    /**
     * Takes RGB color value and converts to a HEX color code
     * Could be used as Recipe::rgb2hex("rgb(0,0,0)") or Recipe::rgb2hex(0,0,0).
     *
     * @param mixed $r Full rgb,rgba string or red color segment
     * @param mixed $g null or green color segment
     * @param mixed $b null or blue color segment
     *
     * @return string hex color value
     */
    public static function rgb2hex($r,$g = NULL,$b = NULL)
    {
        if(strpos($r,'rgb') !== FALSE || strpos($r,'rgba') !== FALSE){
            if(preg_match_all('/\(([^\)]*)\)/',$r,$matches) && isset($matches[1][0])){
                list($r,$g,$b) = explode(',',$matches[1][0]);
            }else{
                return FALSE;
            }
        }

        $result = '';
        foreach([$r,$g,$b] as $c){
            $hex = base_convert($c,10,16);
            $result .= ($c < 16) ? ('0' . $hex) : $hex;
        }

        return '#' . $result;
    }

}
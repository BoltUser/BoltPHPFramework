<?php

namespace Bolt\Core;


class Network
{

    /**
     * Detect if user is on mobile device.
     * @return bool
     * @todo Put everything to an array & then implode it?
     */
    public static function isMobile()
    {
        if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop' . '|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i' . '|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)' . '|vodafone|wap|windows ce|xda|xiino/i',$_SERVER['HTTP_USER_AGENT']) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)' . '|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi' . '(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co' . '(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)' . '|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|' . 'haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|' . 'i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|' . 'kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|' . 'm1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|' . 't(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)' . '\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|' . 'phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|' . 'r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|' . 'mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy' . '(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)' . '|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|' . '70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($_SERVER['HTTP_USER_AGENT'],0,4))){
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Get user browser.
     * @return string
     */
    public static function getBrowser()
    {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $browserName = $ub = $platform = 'Unknown';
        if(preg_match('/linux/i',$u_agent)){
            $platform = 'Linux';
        }elseif(preg_match('/macintosh|mac os x/i',$u_agent)){
            $platform = 'Mac OS';
        }elseif(preg_match('/windows|win32/i',$u_agent)){
            $platform = 'Windows';
        }

        if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)){
            $browserName = 'Internet Explorer';
            $ub = 'MSIE';
        }elseif(preg_match('/Firefox/i',$u_agent)){
            $browserName = 'Mozilla Firefox';
            $ub = 'Firefox';
        }elseif(preg_match('/Chrome/i',$u_agent)){
            $browserName = 'Google Chrome';
            $ub = 'Chrome';
        }elseif(preg_match('/Safari/i',$u_agent)){
            $browserName = 'Apple Safari';
            $ub = 'Safari';
        }elseif(preg_match('/Opera/i',$u_agent)){
            $browserName = 'Opera';
            $ub = 'Opera';
        }elseif(preg_match('/Netscape/i',$u_agent)){
            $browserName = 'Netscape';
            $ub = 'Netscape';
        }

        $known = ['Version',$ub,'other'];
        $pattern = '#(?<browser>' . implode('|',$known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        preg_match_all($pattern,$u_agent,$matches);
        $i = count($matches['browser']);
        $version = $matches['version'][0];
        if($i != 1 && strripos($u_agent,'Version') >= strripos($u_agent,$ub)){
            $version = $matches['version'][1];
        }
        if($version == NULL || $version == ''){
            $version = '?';
        }

        return implode(', ',[$browserName,'Version: ' . $version,$platform]);
    }

    /**
     * Get client location.
     * @return string|false
     */
    public static function getClientLocation()
    {
        $result = FALSE;
        $ip_data = @json_decode(self::curl('http://www.geoplugin.net/json.gp?ip=' . self::getClientIP()));

        if(isset($ip_data) && $ip_data->geoplugin_countryName != NULL){
            $result = $ip_data->geoplugin_city . ', ' . $ip_data->geoplugin_countryCode;
        }

        return $result;
    }

    /**
     * Make a Curl call.
     *
     * @param string     $url URL to curl
     * @param string     $method GET or POST, Default GET
     * @param mixed      $data Data to post, Default false
     * @param mixed      $headers Additional headers, example: array ("Accept: application/json")
     * @param bool       $returnInfo Whether or not to retrieve curl_getinfo()
     * @param bool|array $auth Basic authentication params. If array with keys 'username' and 'password' specified, CURLOPT_USERPWD cURL option will be set
     *
     * @return array|string if $returnInfo is set to True, array is returned with two keys, contents (will contain response) and info (information regarding a specific transfer), otherwise response content is returned
     */
    public static function curl($url,$method = 'GET',$data = FALSE,$headers = FALSE,$returnInfo = FALSE,$auth = FALSE)
    {
        $ch = curl_init();
        $info = NULL;
        if(strtoupper($method) == 'POST'){
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_POST,TRUE);
            if($data !== FALSE){
                curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
            }
        }else{
            if($data !== FALSE){
                if(is_array($data)){
                    $dataTokens = [];
                    foreach($data as $key => $value){
                        array_push($dataTokens,urlencode($key) . '=' . urlencode($value));
                    }
                    $data = implode('&',$dataTokens);
                }
                curl_setopt($ch,CURLOPT_URL,$url . '?' . $data);
            }else{
                curl_setopt($ch,CURLOPT_URL,$url);
            }
        }

        curl_setopt($ch,CURLOPT_HEADER,FALSE);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,TRUE);
        curl_setopt($ch,CURLOPT_TIMEOUT,10);

        if($headers !== FALSE){
            curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
        }

        if($auth !== FALSE && strlen($auth['username']) > 0 && strlen($auth['password']) > 0){
            curl_setopt($ch,CURLOPT_USERPWD,$auth['username'] . ':' . $auth['password']);
        }

        $contents = curl_exec($ch);
        if($returnInfo){
            $info = curl_getinfo($ch);
        }

        curl_close($ch);

        if($returnInfo){
            return ['contents' => $contents,'info' => $info];
        }

        return $contents;
    }

    /**
     * Returns the IP address of the client.
     *
     * @param bool $headerContainingIPAddress Default false
     *
     * @return string
     */
    public static function getClientIP($headerContainingIPAddress = NULL)
    {
        if(!empty($headerContainingIPAddress)){
            return isset($_SERVER[$headerContainingIPAddress]) ? trim($_SERVER[$headerContainingIPAddress]) : FALSE;
        }

        $knowIPkeys = ['HTTP_CLIENT_IP','HTTP_X_FORWARDED_FOR','HTTP_X_FORWARDED','HTTP_X_CLUSTER_CLIENT_IP','HTTP_FORWARDED_FOR','HTTP_FORWARDED','REMOTE_ADDR',];

        foreach($knowIPkeys as $key){
            if(array_key_exists($key,$_SERVER) !== TRUE){
                continue;
            }
            foreach(explode(',',$_SERVER[$key]) as $ip){
                $ip = trim($ip);
                if(filter_var($ip,FILTER_VALIDATE_IP,FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== FALSE){
                    return $ip;
                }
            }
        }

        return FALSE;
    }

    /**
     * Get information on a short URL. Find out where it forwards.
     *
     * @param string $shortURL shortened URL
     *
     * @return mixed full url or false
     */
    public static function expandShortUrl($shortURL)
    {
        if(empty($shortURL)){
            return FALSE;
        }

        $headers = get_headers($shortURL,1);
        if(isset($headers['Location'])){
            return $headers['Location'];
        }

        $data = self::curl($shortURL);

        preg_match_all('/<[\s]*meta[\s]*http-equiv="?' . '([^>"]*)"?[\s]*' . 'content="?([^>"]*)"?[\s]*[\/]?[\s]*>/si',$data,$match);

        if(isset($match) && is_array($match) && count($match) == 3){
            $originals = $match[0];
            $names = $match[1];
            $values = $match[2];
            if((isset($originals) && isset($names) && isset($values)) && count($originals) == count($names) && count($names) == count($values)){
                $metaTags = [];
                for($i = 0,$limit = count($names);$i < $limit;$i++){
                    $metaTags[$names[$i]] = ['html' => htmlentities($originals[$i]),'value' => $values[$i]];
                }
            }
        }

        if(isset($metaTags['refresh']['value']) && !empty($metaTags['refresh']['value'])){
            $returnData = explode('=',$metaTags['refresh']['value']);
            if(isset($returnData[1]) && !empty($returnData[1])){
                return $returnData[1];
            }
        }

        return FALSE;
    }

    /**
     * Get Alexa ranking for a domain name.
     *
     * @param string $domain Domain name to get ranking for
     *
     * @return mixed false if ranking is found, otherwise integer
     */
    public static function getAlexaRank($domain)
    {
        $domain = preg_replace('~^https?://~','',$domain);
        $alexa = 'http://data.alexa.com/data?cli=10&dat=s&url=%s';
        $request_url = sprintf($alexa,urlencode($domain));
        $xml = simplexml_load_file($request_url);

        if(!isset($xml->SD[1])){
            return FALSE;
        }

        $nodeAttributes = $xml->SD[1]->POPULARITY->attributes();
        $text = (int)$nodeAttributes['TEXT'];

        return $text;
    }

    /**
     * Shorten URL via tinyurl.com service.
     *
     * @param string $url URL to shorten
     *
     * @return mixed shortened url or false
     */
    public static function getTinyUrl($url)
    {
        if(strpos($url,'http') !== 0){
            $url = 'http://' . $url;
        }

        $gettiny = self::curl('http://tinyurl.com/api-create.php?url=' . $url);

        if(isset($gettiny) && !empty($gettiny)){
            return $gettiny;
        }

        return FALSE;
    }

    /**
     * Get keyword suggestion from Google.
     *
     * @param string $keyword keyword to get suggestions for
     *
     * @return mixed array of keywords or false
     */
    public static function getKeywordSuggestionsFromGoogle($keyword)
    {
        $data = self::curl('http://suggestqueries.google.com/complete/search?output=firefox&client=firefox&hl=en-US&q=' . urlencode($keyword));
        if(($data = json_decode($data,TRUE)) !== NULL && !empty($data[1])){
            return $data[1];
        }

        return FALSE;
    }

    /**
     * Get a Website favicon image.
     *
     * @param string $url website url
     * @param array  $attributes Optional, additional key/value attributes to include in the IMG tag
     *
     * @return string containing complete image tag
     */
    public static function getFavicon($url,$attributes = [])
    {
        $attr = trim(self::arrayToString($attributes));

        if(!empty($attr)){
            $attr = " $attr";
        }

        return sprintf('<img src="https://www.google.com/s2/favicons?domain=%s"/>', urlencode($url), $attr);
    }

    /**
     * Get a QR code.
     *
     * @param string $string String to generate QR code for.
     * @param int    $width QR code width
     * @param int    $height QR code height
     * @param array  $attributes Optional, additional key/value attributes to include in the IMG tag
     *
     * @return string containing complete image tag
     */
    public static function getQRcode($string,$width = 150,$height = 150,$attributes = [])
    {
        $protocol = 'http://';
        if(self::isHttps()){
            $protocol = 'https://';
        }

        $attr = trim(self::arrayToString($attributes));
        $apiUrl = $protocol . 'chart.apis.google.com/chart?chs=' . $width . 'x' . $height . '&cht=qr&chl=' . urlencode($string);

        return '<img src="' . $apiUrl . '" ' . $attr . ' />';
    }

    /**
     * Check to see if the current page is being served over SSL.
     * @return bool
     */
    public static function isHttps()
    {
        return isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    }

    /**
     * Get a Gravatar for email.
     *
     * @param string $email The email address
     * @param int    $size Size in pixels, defaults to 80 (in px), available values from 1 to 2048
     * @param string $default Default imageset to use, available values: 404, mm, identicon, monsterid, wavatar
     * @param string $rating Maximum rating (inclusive), available values:  g, pg, r, x
     * @param array  $attributes Optional, additional key/value attributes to include in the IMG tag
     *
     * @return string containing complete image tag
     */
    public static function getGravatar($email,$size = 80,$default = 'mm',$rating = 'g',$attributes = [])
    {
        $attr = trim(self::arrayToString($attributes));

        $url = 'https://www.gravatar.com/';

        return sprintf('<img src="%savatar.php?gravatar_id=%s&default=%s&size=%s&rating=%s" width="%spx" height="%spx" %s />',$url,md5(strtolower(trim($email))),$default,$size,$rating,$size,$size,$attr);
    }

    /**
     * Determine if current page request type is ajax.
     * @return bool
     */
    public static function isAjax()
    {
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Return referer page.
     * @return string|false
     */
    public static function getReferer()
    {
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : FALSE;
    }

    /**
     * Search wikipedia.
     *
     * @param string $keyword Keywords to search in wikipedia
     *
     * @return mixed Array or false
     */
    public static function wikiSearch($keyword)
    {
        $apiurl = 'http://wikipedia.org/w/api.php?action=opensearch&search=' . urlencode($keyword) . '&format=xml&limit=1';
        $data = self::curl($apiurl);
        $xml = simplexml_load_string($data);
        if((string)$xml->Section->Item->Description){
            $array = [];
            $array['title'] = (string)$xml->Section->Item->Text;
            $array['description'] = (string)$xml->Section->Item->Description;
            $array['url'] = (string)$xml->Section->Item->Url;
            if(isset($xml->Section->Item->Image)){
                $img = (string)$xml->Section->Item->Image->attributes()->source;
                $array['image'] = str_replace('/50px-','/200px-',$img);
            }

            return $array;
        }

        return FALSE;
    }

    /**
     * Return the current URL.
     * @return string
     */
    public static function getCurrentURL()
    {
        $url = 'http://';
        if(self::isHttps()){
            $url = 'https://';
        }

        if(isset($_SERVER['PHP_AUTH_USER'])){
            $url .= $_SERVER['PHP_AUTH_USER'];
            if(isset($_SERVER['PHP_AUTH_PW'])){
                $url .= ':' . $_SERVER['PHP_AUTH_PW'];
            }
            $url .= '@';
        }
        if(isset($_SERVER['HTTP_HOST'])){
            $url .= $_SERVER['HTTP_HOST'];
        }
        if(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != 80){
            $url .= ':' . $_SERVER['SERVER_PORT'];
        }
        if(!isset($_SERVER['REQUEST_URI'])){
            $url .= substr($_SERVER['PHP_SELF'],1);
            if(isset($_SERVER['QUERY_STRING'])){
                $url .= '?' . $_SERVER['QUERY_STRING'];
            }

            return $url;
        }

        $url .= $_SERVER['REQUEST_URI'];

        return $url;
    }

    /**
     * @param $ipAddress
     *
     * @return string
     */
    public static function getNetMask($ipAddress): string
    {
        if(is_string($ipAddress)){
            $ipAddress = ip2long($ipAddress);
        }
        $mask = 0xFFFFFFFF;
        if(($ipAddress & 0x80000000) === 0){
            $mask = 0xFF000000;
        }elseif(($ipAddress & 0xC0000000) === 0x80000000){
            $mask = 0xFFFF0000;
        }elseif(($ipAddress & 0xE0000000) === 0xC0000000){
            $mask = 0xFFFFFF00;
        }
        return long2ip($mask);
    }

    /**
     * Read RSS feed as array.
     * requires simplexml.
     * @see http://php.net/manual/en/simplexml.installation.php
     *
     * @param string $url RSS feed URL
     *
     * @return array Representation of XML feed
     */
    public static function rssReader($url)
    {
        if(strpos($url,'http') !== 0){
            $url = 'http://' . $url;
        }

        $feed = self::curl($url);
        $xml = simplexml_load_string($feed,'SimpleXMLElement',LIBXML_NOCDATA);

        return self::objectToArray($xml);
    }
}
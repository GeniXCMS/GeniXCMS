<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 1.0.0 build date 20170118
 *
 * @version 1.1.11
 *
 * @link https://github.com/semplon/GeniXCMS
 * 
 *
 * @author Puguh Wijayanto <metalgenix@gmail.com>
 * @copyright 2014-2020 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

class Http
{
    public static $agent;

    public static $ipApi;

    public function __construct()
    {
        self::$agent = self::varAgent();
        self::$ipApi = self::varIPApi();
    }

    public static function validateUrl($url)
    {
        $prot = self::validateProtocol($url, array('http', 'https'));
        if ($prot){
            //check if host is same with config
             $url_p = @parse_url($url);
             $url_c = @parse_url(Site::$url);
             $host_same = self::sameAsSite($url);

             if (!$host_same) {
                 if (self::validatePort($url))
                     return true;

                 if (self::isLocal($url))
                     return true;

                 if ( $url_c && $host_same && isset( $url_c['port'] ) && $url_c['port'] === $url_p['port'] )
                     return true;

                 return false;

             } else {
                 return true;
             }
        } else {
            return false;
        }
    }

    public static function validateProtocol($url, $protocol)
    {
        $url_p = @parse_url($url);
        if (in_array($url_p['scheme'], $protocol)) {
            return true;
        } else {
            return false;
        }
    }

    public static function validatePort($url, $ports = [80, 443, 8080])
    {
        $purl = @parse_url($url);
        if (in_array($purl['port'], $ports)) {
            return true;
        } elseif($purl['port'] == ''){
            return true;
        } else {
            return false;
        }
    }

    public static function sameAsSite($url)
    {
        $purl = @parse_url($url);
        $surl = @parse_url(Site::$url);
        if ($purl['host'] == $surl['host']) {
            return true;
        } else {
            return false;
        }

    }

    public static function isLocal($url)
    {
        $data['url'] = $url;
        $url_p = @parse_url($url);
        $host = trim( $url_p['host'], '.' );
        if ( filter_var($host, FILTER_VALIDATE_IP)) {
            $ip = $host;
        } else {
            $ip = gethostbyname( $host );
            if ( $ip === $host ) {
                $ip = false;
            }
        }

        if ($ip) {
            $parts = array_map( 'intval', explode( '.', $ip ) );
            if ( 127 === $parts[0] || 10 === $parts[0] || 0 === $parts[0]
                || ( 172 === $parts[0] && 16 <= $parts[1] && 31 >= $parts[1] )
                || ( 192 === $parts[0] && 168 === $parts[1] )
            ) {
                    return false;
            }
        }
    }

    /**
     * $vars = [
     *      'url' => '',
     *      'curl' => 'true/false',
     *      'curl_options' => [],
     *      'curl_param' => []
     * ]
     */
    public static function fetch($vars)
    {
        if (is_array($vars)){
            $url = isset($vars['url']) ? $vars['url']: '';
            $curl = isset($vars['curl']) ? $vars['curl']: '';
            $c_options[] = isset($vars['curl_options']) ? $vars['curl_options']: [];
        } else {
            $url = $vars;
            $curl = false;
        }

        if ($curl) {
            $ch = @curl_init();
//            $opt = '';
            $c_options[] = array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $url
            );
//            $options = array_merge($options, $c_options);
//            print_r($c_options);
            $options = [];
            foreach ($c_options as $k => $v) {
                foreach ($v as $k2 => $v2){
                    $options[$k2] = $v2;
                }

            }
//            print_r($options);
            @curl_setopt_array($ch, $options);
            $fetch = @curl_exec($ch);
            @curl_close($ch);
        } else {
            $fetch = @file_get_contents($url);
        }

        return $fetch;
    }

    public static function ipDetail($ip)
    {

        $ipApi = self::randIpApi();
        $detail = self::fetch($ipApi.$ip);

        return $detail;
    }

    public static function getIpCountry($ip)
    {
        $ip = json_decode(self::ipDetail($ip), true);

        return $ip['country_code'];
    }

    public static function varAgent()
    {
        $agent = [
            'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36',
            'Opera/9.80 (Windows NT 6.2; Win64; x64) Presto/2.12 Version/12.16',
            'Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko',
            'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)',
            'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)',
            'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:45.0) Gecko/20100101 Firefox/45.0',
            'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)',
            'Mozilla/5.0 (Windows NT 6.1; Trident/7.0; rv:11.0) like Gecko',
            'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; .NET4.0E)',
            'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.11 (KHTML like Gecko) Chrome/23.0.1271.95 Safari/537.11',
            'Mozilla/5.0 (Windows NT 6.3; WOW64; Trident/7.0; rv:11.0) like Gecko',
            'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:31.0) Gecko/20100101 Firefox/31.0',
            'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML like Gecko) Chrome/36.0.1985.143 Safari/537.36',
            'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:32.0) Gecko/20100101 Firefox/32.0',
            'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML like Gecko) Chrome/31.0.1650.63 Safari/537.36',
            'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML like Gecko) Chrome/35.0.1916.153 Safari/537.36'

        ];

        return $agent;
    }

    public static function randAgent()
    {
        $rnd = array_rand(self::$agent, 1);

        return self::$agent[$rnd];
    }

    public static function addAgent($agent)
    {
        if (is_array($agent)) {
            $newAgent = array_merge($agent, self::$agent);
        } else {
            $newAgent = array($agent);
            $newAgent = array_merge($newAgent,self::$agent);
        }

        return $newAgent;
    }

    public static function varIpApi()
    {
        $ipApi = [
            'https://freegeoip.app/json/',
            'https://freegeoip.live/json/',
            'https://freegeoip.lwan.ws/json/',
        ];

        return $ipApi;
    }

    public static function randIpApi()
    {
        $rnd = array_rand(self::$ipApi, 1);

        return self::$ipApi[$rnd];
    }

}
<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System.
 *
 * PHP Based Content Management System and Framework
 *
 * @since 1.0.0 build date 20170118
 *
 * @version 1.0.0
 *
 * @link https://github.com/semplon/GeniXCMS
 * @link http://genixcms.org
 *
 * @author Puguh Wijayanto <psw@metalgenix.com>
 * @copyright 2014-2017 Puguh Wijayanto
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

class Http
{
    public function __construct()
    {
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
}
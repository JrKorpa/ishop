<?php
/**
 *  -------------------------------------------------
 *   @file		: App.class.php
 *   @link		: 珂兰钻石 www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		:
 *   @update	:
 *  -------------------------------------------------
 */
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class Util {

    /*
       *  从常量数组获取指定参数值
       */
    public static function get_defined_array_var($const, $key = '') {
        if (!defined($const)) {
            return false;
        }
        $array = json_decode(constant($const), true);
        if (empty($array)) {
            return false;
        }

        if (empty($key)) return $array;
        return array_key_exists($key, $array) ? $array[$key] : false;
    }


    /*
         * 	跳转函数,取代header。强制以top方式打开页面，防止程序嵌套。目前只应用于会话过期后跳转登录页
         */

    public static function jump($url, $target = '_top') {
        if (self::isPost()) {
            $str = '<meta http-equiv="refresh" content="0;url=\'' . $url . '\'">';
            echo $str;
        } else {
            $str = "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><script type=\"text/javascript\">";
            $str .="javascript:window.location.target='" . $target . "';";
            $str .="javascript:window.location.href='" . $url . "'";
            $str .=";</script>";
            echo $str;
        }
        exit;
    }


    /**
     * 	isPost，判断是否为post提交
     *
     * 	@return Boolean
     *
     */
    public static function isPost() {
        return strtolower($_SERVER['REQUEST_METHOD']) == 'post';
    }



    /**
     * 	getDomain，取得当前的域名
     *
     * 	@return String
     *
     */
    public static function getDomain() {
        // 协议
        $protocol = self::http();
        // 域名或IP地址
        if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
            $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
        } elseif (isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        } else {
            // 端口
            if (isset($_SERVER['SERVER_PORT'])) {
                $port = ':' . $_SERVER['SERVER_PORT'];
                if ((':80' == $port && 'http://' == $protocol) || (':443' == $port && 'https://' == $protocol)) {
                    $port = '';
                }
            } else {
                $port = '';
            }

            if (isset($_SERVER['SERVER_NAME'])) {
                $host = $_SERVER['SERVER_NAME'] . $port;
            } elseif (isset($_SERVER['SERVER_ADDR'])) {
                $host = $_SERVER['SERVER_ADDR'] . $port;
            }
        }
        return $protocol . $host;
    }


    /**
     * 	http，获得当前环境的 HTTP 协议方式
     *
     * 	@return String
     *
     */
    public static function http() {
        return (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) ? 'https://' : 'http://';
    }


    /**
     * 获取变量值，
     * 存在则返回将原值转换成字符串并trim后返回，
     * 不存在则将默认值转换成字符串并trim后返回。
     *
     * @param string $var
     * @param string $default
     * @return string
     */
    public static function getString($var, $default=null)
    {
        $value = isset($_REQUEST[$var]) ? $_REQUEST[$var] : $default;
        return self::transInvalidChar(trim((string)$value));
    }



    // 转意非法字符，目前单引号和双引号已转义
    public static function transInvalidChar($str) {
        // 转义配对双引号 为中文
        $str = preg_replace('/"([^"]*)"/', '“${1}”', $str);
        // 转义单个双引号 为中文
        $str = str_replace('"', '”', $str);
        // 转义配对单引号 为中文
        $str = preg_replace("/'([^']*)'/", '‘${1}’', $str);
        // 单个单引号替换
        $str = str_replace("'", "’", $str);
        return $str;
    }


    /**
     * 	encrypt，加密函数
     *
     * 	@param String $str 明文
     * 	@param String $key 密钥
     *
     * 	@return String  密文
     *
     */
    public static function encrypt($str, $key = AUTH_KEY) {
        $coded = '';
        $keylength = strlen($key);
        $count = strlen($str);
        for ($i = 0; $i < $count; $i += $keylength) {
            $coded .= substr($str, $i, $keylength) ^ $key;
        }
        return str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($coded));
    }


    /**
     * 	decrypt，解密函数
     *
     * 	@param String $str 密文
     * 	@param String $key 密钥
     *
     * 	@return  strinSg  明文
     *
     */
    public static function decrypt($str, $key = AUTH_KEY) {
        $coded = '';
        $keylength = strlen($key);
        $str = base64_decode(str_replace(array('-', '_'), array('+', '/'), $str));
        $count = strlen($str);
        for ($i = 0; $i < $count; $i += $keylength) {
            $coded .= substr($str, $i, $keylength) ^ $key;
        }
        return $coded;
    }




    /**
     * 	random，取随机字符串
     *
     * 	@param int $length 生成的字符串长度
     * 	@param bool $numeric 如果为真，则生成纯数字字符串
     *
     * 	@return String
     *
     */
    public static function random($length, $numeric = 0) {
        if ($numeric) {
            $hash = sprintf('%0' . $length . 'd', mt_rand(0, pow(10, $length) - 1));
        } else {
            $hash = '';
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
            $max = strlen($chars) - 1;
            for ($i = 0; $i < $length; $i++) {
                $hash .= $chars[mt_rand(0, $max)];
            }
        }
        return $hash;
    }

    /**
     * 	getClicentIp，取客户端公网IP地址
     *
     * 	@return String
     *
     */
    public static function getClicentIp() {
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && preg_match_all('/(\d{1,3}\.){3}\d{1,3}/s', $_SERVER['HTTP_X_FORWARDED_FOR'], $mat)) {
            foreach ($mat[0] as $ip) {
                if (!preg_match('/^(?:10|172\.16|192\.168)\./', $ip)) {
                    return $ip;
                }
            }
            return $ip;
        } elseif (isset($_SERVER["HTTP_FROM"]) && preg_match('/(?:\d{1,3}\.){3}\d{1,3}/', $_SERVER["HTTP_FROM"])) {
            return @$_SERVER["HTTP_FROM"];
        } else {
            return @$_SERVER['REMOTE_ADDR'];
        }
    }



    public static function httpCurl($url, $post = '') {
        $url = trim($url);
        //TODO: 针对特例简单处理
        if (strpos($url, "/") === 0) {
            $url = self::getDomain() .$url;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($ch, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36');
        if (!empty($post)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        return curl_exec($ch);
    }



     public static function  unicode_encode($name)
    {
        $name = iconv('UTF-8', 'UCS-2', $name);
        $len = strlen($name);
        $str = '';
        for ($i = 0; $i < $len - 1; $i = $i + 2)
        {
            $c = $name[$i];
            $c2 = $name[$i + 1];
            if (ord($c) > 0)
            {    // 两个字节的文字
                $str .= '\u'.base_convert(ord($c), 10, 16).base_convert(ord($c2), 10, 16);
            }
            else
            {
                $str .= $c2;
            }
        }
        return $str;
    }


      public static function escape($string, $in_encoding = 'UTF-8',$out_encoding = 'UCS-2') {
        $return = '';
        if (function_exists('mb_get_info')) {
            for($x = 0; $x < mb_strlen ( $string, $in_encoding ); $x ++) {
                $str = mb_substr ( $string, $x, 1, $in_encoding );
                if (strlen ( $str ) > 1) { // 多字节字符
                    $return .= '%u' . strtoupper ( bin2hex ( mb_convert_encoding ( $str, $out_encoding, $in_encoding ) ) );
                } else {
                    $return .= '%' . strtoupper ( bin2hex ( $str ) );
                }
            }
        }
        return $return;
    }




}

?>
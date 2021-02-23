<?php

abstract class base_repository {

    protected $config;

    public function __construct($_config) {
        $this->config = $_config;
    }

    protected function invoke($act, $args = array()) {

        $args['x_epoch'] = time();
        $args['x_exp'] = 5;

        ksort($args);
        $filter = json_encode($args);

        $auth_key    = $this->config['auth_key'];    
        $mod         = $this->config['mod'];    
        $url         = $this->config['url'];
        
        $url_info    = parse_url($url);
        $domain      = $url_info['scheme'] .'://'.$url_info['host'];

        $signed_data = array('filter'=> $filter, 'sign' => md5($domain. $mod. $act. $filter . $auth_key));

        $url  = $url . (strrpos($url,'?') === false ? '?' : '&').'con='.$mod.'&act='.$act;
        $resp = $this->httpCurl($url, $signed_data, false, true, 30);
        $resp = json_decode($resp, true);
        if(!isset($resp['error'])){
            $resp['error'] = 1;
            $resp['error_msg'] = "系统繁忙，请稍后再试";
        }
        return $resp;
    }

    private function httpCurl($url, $post = '') {
        $url = trim($url);
        
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

    protected function getpack($mobile)
    {
        $url = C('crm_member_api').'/'.$mobile."?check_phone=1";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36');
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    protected function savepack($postdata=[])
    {
        $url = C('crm_member_api');
        //$data = $this->httpCurl($url, $postdata, false, true, 30);
        $url = trim($url);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($ch, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36');
        if (!empty($postdata)) {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        }
        //执行命令
        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //关闭URL请求
        curl_close($ch);
        //显示获得的数据
        $datas = explode("\r\n\r\n", $data, 2);
        //返回200/201表示请求成功
        $datas['code'] = $httpCode;
        return $datas;
    }
}


?>
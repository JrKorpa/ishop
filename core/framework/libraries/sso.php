<?php
defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class Sso {
    
    public static function check_token($token, $sso_config) {
        
        $freq_check_token_inm = $sso_config['freq_check_token_inm'];
        $last_check_time = cookie('latest_ck_token');
        if (empty($last_check_time)) $last_check_time = 0;
        
        $time_flush_since_last_check = ceil((time() - $last_check_time) / 60);
        
        if ($time_flush_since_last_check >= $freq_check_token_inm || (mt_rand(1, 11) % 3) == 0) {
            
            $check_url = $sso_config['checkout'];
            $check_url .= $token;
            $resp = Util::httpCurl($check_url);
            
            setNcCookie('latest_ck_token', time(), 3600);
            
            if (empty($resp)) {
                // 连续3次调用失败，则认为token无效, 否则暂时忽略该失败调用
                if ($time_flush_since_last_check >= $freq_check_token_inm * 3) return false;
                return true;
            }
            
            $resp = json_decode($resp, true);
            if (!isset($resp['status'])) {
                // 连续3次调用失败，则认为token无效, 否则暂时忽略该失败调用
                if ($time_flush_since_last_check >= $freq_check_token_inm * 3) return false;
                return true;
            } else if ($resp['status'] <> 1) {
                // 调用成功，但token验证无效
                return false;
            }
                        
            if (isset($resp['data']['token'])) {
                return $resp['data']['token'];
            }
        }
        
        return true;        
    }

}
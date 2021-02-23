<?php
/**
 * 手机短信发送类
 * @author ArimisWang
 * @license GPL
 * @copyright 上海珂兰商贸有限公司
 */

defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class jikeCrm {
    public $smsConfig;
    public $softwareSerialNo; //序列号
    public $key; //随机字符
    public $serialpass; //密码
    protected $sign = ''; //短信签名
    protected $suffix = '退订回N'; //短信后缀
    public $webserviceUrl = 'http://webservice.hdwinfo.cn:8000/sdk/SDKService?wsdl'; //接口地址
    protected $charset = 'UTF-8';
    /**
     * @var \SoapClient
     */
    public $soap;
    static $info = [];
    public static $systemError = [
        0 => "成功",
        -1 => "系统异常",
        -2 => "客户端异常",
        -101 => "命令不被支持",
        -102 => "RegistryTransInfo删除信息失败",
        -103 => "RegistryInfo更新信息失败",
        -104 => "请求超过限制",
        -110 => "号码注册激活失败",
        -111 => "企业注册失败",
        -113 => "充值失败",
        -117 => "发送短信失败",
        -118 => "接收MO失败",
        -119 => "接收Report失败",
        -120 => "修改密码失败",
        -122 => "号码注销激活失败",
        -123 => "查询单价失败",
        -124 => "查询余额失败",
        -125 => "设置MO转发失败",
        -126 => "路由信息失败",
        -127 => "计费失败0余额",
        -128 => "计费失败余额不足",
        -190 => "数据操作失败",
        -1100 => "序列号错误,序列号不存在内存中,或尝试攻击的用户",
        -1102 => "序列号密码错误",
        -1103 => "序列号Key错误",
        -1104 => "路由失败，请联系系统管理员",
        -1105 => "注册号状态异常, 未用 1",
        -1107 => "注册号状态异常, 停用 3",
        -1108 => "注册号状态异常, 停止 5",
        -1131 => "充值卡无效",
        -1132 => "充值密码无效",
        -1133 => "充值卡绑定异常",
        -1134 => "充值状态无效",
        -1135 => "充值金额无效",
        -1901 => "数据库插入操作失败",
        -1902 => "数据库更新操作失败",
        -1903 => "数据库删除操作失败",
        -9000 => "数据格式错误,数据超出数据库允许范围",
        -9001 => "序列号格式错误",
        -9002 => "密码格式错误",
        -9003 => "客户端Key格式错误",
        -9004 => "设置转发格式错误",
        -9005 => "公司地址格式错误",
        -9006 => "企业中文名格式错误",
        -9007 => "企业中文名简称格式错误",
        -9008 => "邮件地址格式错误",
        -9009 => "企业英文名格式错误",
        -9010 => "企业英文名简称格式错误",
        -9011 => "传真格式错误",
        -9012 => "联系人格式错误",
        -9013 => "联系电话",
        -9014 => "邮编格式错误",
        -9015 => "新密码格式错误",
        -9016 => "发送短信包大小超出范围",
        -9017 => "发送短信内容格式错误",
        -9018 => "发送短信扩展号格式错误",
        -9019 => "发送短信优先级格式错误",
        -9020 => "发送短信手机号格式错误",
        -9021 => "发送短信定时时间格式错误",
        -9022 => "发送短信唯一序列值错误",
        -9023 => "充值卡号格式错误",
        -9024 => "充值密码格式错误",
        -9025 => "客户端请求sdk5超时",
    ];

    public function __construct() {
        try {
            $this->soap = new \SoapClient($this->webserviceUrl, array("trace" => 1, "exception" => 0, 'encoding' => 'utf-8'));
            $this->softwareSerialNo = $user_id = urlencode(C('hao_sms_zh')); // 这里填写用户名
            $this->key = C('hao_sms_key'); // 这里填登陆密码
            $this->serialpass = C('hao_sms_pw');
            $this->sign = $GLOBALS['sms_options']['signature'];
        } catch (\SOAPFault $e) {
            throw new \Exception($e->getMessage());
        }
    }

    //注册
    public function register() {
        try {
            $result = $this->soap->__soapCall("registEx", array(
                array(
                    'arg0' => $this->softwareSerialNo,
                    'arg1' => $this->key,
                    'arg2' => $this->serialpass,
                )
            ));
        } catch (\SOAPFault $e) {
            $result = 'Error code：' . $e->getCode() . ',' . $e->getMessage();
        }
        return $this->getMessage($result);
    }

    //获取余额
    public function getBalance() {
        try {
            $result = $this->soap->__soapCall("getBalance", array(
                array(
                    'arg0' => $this->softwareSerialNo,
                    'arg1' => $this->key,
                )
            ));
        } catch (\SOAPFault $e) {
            $result = 'Error code：' . $e->getCode() . ',' . $e->getMessage();
        }
        return $this->getMessage($result);
    }

    //查询单条短信价格
    public function getEachFee() {
        try {
            $result = $this->soap->__soapCall("getEachFee", array(
                array(
                    'arg0' => $this->softwareSerialNo,
                    'arg1' => $this->key,
                )
            ));
        } catch (\SOAPFault $e) {
            $result = 'Error code：' . $e->getCode() . ',' . $e->getMessage();
        }
        return $this->getMessage($result);
    }
    
    function __destruct() {
        if(count(self::$info)) {
            foreach(self::$info as $content=>$mobiles)
            {
                $this->sendBatchImmediately($mobiles,$content);
            }
        }
    }

    /**
     * @param $mobile
     * @param $content
     * @param $sendTime
     * @return boolean|string
     * @throws \Exception
     */
    public function sendImmediately($mobile, $content, $sendTime) {
        
        //检查
        $content = $this->sign . $content . $this->suffix;
        try {
            $res = $this->soap->__soapCall("sendSMS", [
                [
                    'arg0' => $this->softwareSerialNo,
                    'arg1' => $this->key,
                    'arg2' => $sendTime,
                    'arg3' => $mobile,
                    'arg4' => $content,
                    'arg5' => "",
                    'arg6' => "UTF-8",
                    'arg7' => '1',
                    'arg8' => '1'
                ]
            ]);
            $result = $this->getMessage($res);
            $rsCode = is_object($res) ? $res->return : $res;
            if(is_numeric($rsCode) && $rsCode == -1105) {
                $this->register();
            }
            if($rsCode != 0) {
                throw new \Exception("短信发送失败：code - " . $rsCode . ", message - " . $rs);
            }
            $result=true;
        } catch (\SOAPFault $e) {
            $result = 'Error code：' . $e->getCode() . ',' . $e->getMessage();
        }
        return $result;
    }
    
    //发送短信
    /**
     * @param array $mobiles
     * @param $content
     * @param string $sendTime
     * @return array
     */
    public function sendBatchImmediately(array $mobiles = [], $content, $sendTime = '') {
    
        //检查
        $content = $this->sign . $content . $this->suffix;
        $sendMobiles = '';
        $result = [];
        foreach ($mobiles as $key => $value) {
            $sendMobiles .= empty($sendMobiles) ? $value : ',' . $value;
            if (($key % 1000 == 0) || $key == count($mobiles) - 1) {
                try {
                    //短信扣费
                    $res = $this->soap->__soapCall("sendSMS", [
                        [
                            'arg0' => $this->softwareSerialNo,
                            'arg1' => $this->key,
                            'arg2' => $sendTime,
                            'arg3' => $value,
                            'arg4' => $content,
                            'arg5' => "",
                            'arg6' => "UTF-8",
                            'arg7' => '1',
                            'arg8' => '1'
                        ]
                    ]);
                    $result[] = $this->getMessage($res);
                } catch (\SOAPFault $e) {
                    $result[] = 'Error code：' . $e->getCode() . ',' . $e->getMessage();
                }
                $sendMobiles = '';
            }
            }
            return $result;
        }
    

    //充值接口
    public function chargeUp($cardNo, $cardPass) {
        try {
            $result = $this->soap->__soapCall("chargeUp", [
                [
                    'arg0' => $this->softwareSerialNo,
                    'arg1' => $this->key,
                    'arg2' => $cardNo,
                    'arg3' => $cardPass,
                ]
            ]);
        } catch (\SOAPFault $e) {
            $result = 'Error code：' . $e->getCode() . ',' . $e->getMessage();
        }
        return $this->getMessage($result);
    }

    //获取错误
    public function getMessage($k) {
        if (is_object($k) && isset(self::$systemError["{$k->return}"])) {
            if($k->return != 0) {
               return self::$systemError[$k->return];
            }
            else {
               return $k->return;
            }
        } else {
            return $k->return;
        }
    }

}

<?php

/*
 *  -------------------------------------------------
 *   @file      : QExpress.php
 *   @link      : 珂兰钻石 www.kela.cn
 *   @copyright : 2017-2024 kela Inc
 *   @author        : luochuanrong 
 *   @date      : 2017
 *   @update        :
 *  -------------------------------------------------
 */
/**
 * 测试区：http://ediapitest.tswlsys.com/dmp/own/order/acceptGoods
 * 正式区：http://ediapi.tswlsys.com/dmp/own/order/acceptGoods
 * 
$data = array(
    'OrderType'=>1,
    'ConsigneeName'=>'奢侈品',
    'ConsigneePhone'=>'18310818073',
    'ConsigneeAddr'=>'北京大兴区金星路24号',
    'WarehouseCode'=>'34,38',
    'GoodsWeight'=>'100',
    'DeliveryType'=>'scp',
    'GoodsType'=>'shechipin',
    'InsuranceValue'=>'25.25',
    'Remark'=>"贵重物品",    
);
$result = QExpress::makeOrder("LBX0329941115963039",$data);
//print_r($result);
$result = QExpress::getDeliveryTrace('LBX0329941115963039');
print_r($result);

Array
(
    [success] => 1
    [msg] => success
    [data] => Array
        (
            [0] => Array
                (
                    [deliveryName] => 陈晓菁
                    [deliveryPhone] => 运单已到达海口市 海南分拣中心
                    [formCode] => LBX0329941115963039
                    [operatorDesc] => 运单已到达海口市 海南分拣中心
                    [operatorNo] => 683429122
                    [operatorTime] => 20170930211822
                    [status] => 已分拣
                )

            [1] => Array
                (
                    [deliveryName] => 陈晓菁
                    [deliveryPhone] => 运单已从海口市 海南分拣中心发出，下一站陵水黎族自治县市 海南陵水站
                    [formCode] => LBX0329941115963039
                    [operatorDesc] => 运单已从海口市 海南分拣中心发出，下一站陵水黎族自治县市 海南陵水站
                    [operatorNo] => 683481243
                    [operatorTime] => 20170930235904
                    [status] => 已出库
                )

            [2] => Array
                (
                    [deliveryName] => 陈继春
                    [deliveryPhone] => 运单已到达陵水黎族自治县市 海南陵水站 联系电话：
                    [formCode] => LBX0329941115963039
                    [operatorDesc] => 运单已到达陵水黎族自治县市 海南陵水站 联系电话：
                    [operatorNo] => 683569361
                    [operatorTime] => 20171001113052
                    [status] => 已入站
                )

            [3] => Array
                (
                    [deliveryName] => 陈继春
                    [deliveryPhone] => 运单已由配送员：陈继春 送出，联系电话：13976151258
                    [formCode] => LBX0329941115963039
                    [operatorDesc] => 运单已由配送员：陈继春 送出，联系电话：13976151258
                    [operatorNo] => 683571150
                    [operatorTime] => 20171001114141
                    [status] => 已分配
                )

            [4] => Array
                (
                    [deliveryName] => 陈继春
                    [deliveryPhone] => 运单由于延时配送（到货延迟、事故、天气原因、其他）原因，暂滞留站区
                    [formCode] => LBX0329941115963039
                    [operatorDesc] => 运单由于延时配送（到货延迟、事故、天气原因、其他）原因，暂滞留站区
                    [operatorNo] => 683574185
                    [operatorTime] => 20171001115335
                    [status] => 滞留
                )

            [5] => Array
                (
                    [deliveryName] => 陈继春
                    [deliveryPhone] => 运单已送达成功，签收人：本人签收
                    [formCode] => LBX0329941115963039
                    [operatorDesc] => 运单已送达成功，签收人：本人签收
                    [operatorNo] => 683672562
                    [operatorTime] => 20171001184653
                    [status] => 妥投
                )

        )

)
 *
 * -1  入库
10  出库
1   入站
2   出站
3   妥投   //正向物流时，此时表示客户已经签收；上门取货时，此状态表示已经成功取货。
4   滞留
5   拒收


11  返货在途
12  返货入库
14  返货出库
 *
 *
 *
 */
class QExpress {
        /**
         * @var string
         */
        private static $_private_key = '$apr1$6crWQTpA$0P6EJ7Xa.JF/54036hETW0';//密钥
        
        public static function makeOrder($order_sn,$data){
            $stamp = time();
            $url = C("ql_tswlsys_gateway") . "/dmp/own/order/acceptGoods";
            $params = array();  
            $data['request']['OrderCode'] = 'YX'.$order_sn;
            $xmlData = self::arrayToXml($data);
            $params['partner'] = 'qlwl_scp';            
            $params['sign'] = base64_encode(md5(self::$_private_key.$stamp.$xmlData));
            $params['stamp'] = rawurlencode($stamp);
            $params['content'] = rawurlencode($xmlData);
            $url = self::getUrl($url, $params);
            $result = self::post($url);
            
            $result = self::xmlToArray($result);
            if($result['success']=='true'){
                preg_match("/[a-zA-Z0-9]+$/is",$result['error'],$arr);
                if(!empty($arr[0])){
                    $shipingCode = $arr[0];
                }else{
                    $shipingCode =$result['error']; 
                }
                return array('success'=>1,'msg'=>$result['error'],'data'=>$shipingCode);
            }else{
                $result['error'] = $result['error']?$result['error']:'获取失败';
                return array('success'=>0,'msg'=>$result['error'],'data'=>'');
            }           
        }
        
        public static  function getDeliveryTrace($shipping_code){
            $timestamp = time();
            $url = C("ql_tswlsys_gateway") . "/datadmp/own/order/findOrderTrack";
            $params = array();
            $params['timestamp'] = $timestamp;
            $params['sign'] = md5($timestamp.$shipping_code.self::$_private_key.self::reverse($shipping_code));
            $url = self::getUrl($url, $params);
            $data = array(
                'shippingCode'=>$shipping_code,
            );
            $jsonData = json_encode($data);
            $result = self::post($url,$jsonData,'json');
            $result = json_decode($result,true);
            $result = isset($result[0])?$result[0]:$result;
            if($result['isSuccess']=='true'){
                return array('success'=>1,'msg'=>'success','data'=>$result['resultData']);
            }else{
                return array('success'=>0,'msg'=>$result['message'],'data'=>array());
            }
            /**
            OperatorNo	顺序号，每步物流操作顺序号
            OperatorTime	操作时间，格式：YYYY-MM-DD hh:mm:ss
            OperatorDesc	操作描述，如：运单已到达北京分拨中心
            Status	物流状态。ARRIVAL 已入站
                            SENT_SCAN 派件
                            SIGNED 签收
                            FAILED 签收失败（滞留或拒收）
            DeliveryName	派送员姓名
            DeliveryPhone	派送员电话
            ReceiverName	签收人
            Remark	备注信息
            */
        }

        public static function post($url,$body='',$dataType='') 
        { 
             $curlObj = curl_init();
             curl_setopt($curlObj, CURLOPT_URL, $url); // 设置访问的url
             curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1); //curl_exec将结果返回,而不是执行
             if($dataType=="json"){
                 curl_setopt($curlObj, CURLOPT_HTTPHEADER, array(
                 'Content-Type: application/json; charset=utf-8',
                 'Content-Length: ' . strlen($body))
                 );
             }else{
                 curl_setopt($curlObj, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded;charset=UTF-8"));
             }
             curl_setopt($curlObj, CURLOPT_URL, $url);
             curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, FALSE);
             curl_setopt($curlObj, CURLOPT_SSL_VERIFYHOST, FALSE);
             curl_setopt($curlObj, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
            
             curl_setopt($curlObj, CURLOPT_CUSTOMREQUEST, 'POST');      
            
             curl_setopt($curlObj, CURLOPT_POST, true);
             curl_setopt($curlObj, CURLOPT_POSTFIELDS, $body);       
             curl_setopt($curlObj, CURLOPT_ENCODING, 'gzip');

             $res = @curl_exec($curlObj);
             //var_dump($res);
             curl_close($curlObj);

             if ($res === false) {
                   $errno = curl_errno($curlObj);
                   if ($errno == CURLE_OPERATION_TIMEOUTED) {
                       $msg = "Request Timeout:   seconds exceeded";
                   } else {
                       $msg = curl_error($curlObj);
                   }
                   echo $msg;
            } 
            return $res;
        }
        
        public static function getUrl($url,$params){
            $url .= "?";
            foreach ($params as $key=>$val){
                $url.= $key."=".$val."&";
            }
            $url = trim($url,'&');
            return $url;
        }
        
        public static function reverse($str){
            return implode("",array_reverse(str_split($str)));
        }
        public static function arrayToXml($arr){
            $xml = '';
            foreach ($arr as $key=>$val){
                if(is_numeric($key)){
                    $key = "Goods";
                }
                if(is_array($val)){
                    $xml.="<".$key.">".self::arrayToXml($val)."</".$key.">";
                }else{
                    $xml.="<".$key.">".$val."</".$key.">";
                }
            }
            return $xml;
        }
        
        public static function arrayToXml2($arr,$dom=0,$item=0){
            if (!$dom){
                $dom = new DOMDocument("1.0");
            }
            if(!$item){
                $item = $dom->createElement("request");
                $dom->appendChild($item);
            }
            foreach ($arr as $key=>$val){
                $itemx = $dom->createElement(is_string($key)?$key:"item");
                $item->appendChild($itemx);
                if (!is_array($val)){
                    $text = $dom->createTextNode($val);
                    $itemx->appendChild($text);
        
                }else {
                    self::arrayToXml($val,$dom,$itemx);
                }
            }
            $xml = $dom->saveXML();
            $xml = str_replace('<?xml version="1.0"?>','',$xml);
            return preg_replace("/\s+/is",'',$xml);
        }
        
        public static function xmlToArray($xml)
        {
            //禁止引用外部xml实体
            @libxml_disable_entity_loader(true);
            $values = json_decode(json_encode(@simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
            return $values;
        }

        public static function getLatestStatus($shipping_code) {
            $data = self::getDeliveryTrace($shipping_code);
            if(isset($data['data'])) {
                asort($data['data']);
            }
            return array_shift($data['data']);
        }
       
}
?>
<?php

/**
 * 芝麻信用分认证普惠版公用方法
 */

 class ZhimaCreditScoreBriefHelper {
 	const APP_ID_KEY = "";
 	const PRIVATE_KEY_KEY = "";
 	const PUBLIC_KEY_KEY = "";
 	public function __construct() {

 		include_once(dirname(__FILE__) . "/aop/request/ZhimaCreditScoreBriefGetRequest.php");
 		include_once(dirname(__FILE__) . "/aop/AopClient.php");
 	}

 	/**
 	 * 验证用户的芝麻信用分数是否达到标准
     * @param string $id_no
     * @param string $uname
     * @param int  $credit_score
     * @return bool
 	 */
 	public function checkCredit($id_no, $uname, $credit_score) {
 		$aop = new AopClient ();
 		// 正式环境 https://openapi.alipay.com/gateway.do
		$aop->gatewayUrl = C("alipay_gateway");//'https://openapi.alipaydev.com/gateway.do';
		$aop->appId = sys_configModel::getConfig(sys_configModel::CACHE_KEY_SCORE, "zm_app_id"); //'2016090900468535'; //后端可配置
		//后端可配置，当前使用的是share.kela.cn的ssl证书key
		$aop->rsaPrivateKey = sys_configModel::getConfig(sys_configModel::CACHE_KEY_SCORE, "zm_private_key");
        //'MIIEpAIBAAKCAQEAv/DYhMsqsbLZapB77Tn0IGxqJruVf3I7exA/FOL7FGAvlxdb9LCtDq5CmYU/6G+yPhAe35YDsRuHmEC3Tqheg/BbtbWaOwFtL7/vJdYD6hPaaVEwY7nMMYmv/W0GEHLEDwaQ3k+ZA9HD6tc/Kllzf76jqLF8tXq7H0+0rzAPon+DO9I6WlOself3EHdXXQ+JLrfHEqZ+kVJOXFAcLsPHX7Ae0cvGsC9coVB7vTY/i93ouf8DapNUZOxC3ust2F8+pjFbDyiP6U/pYnV0EqDhyzS+lAaWDb7154OiebAA9Rvu91ln33Aur1NwffGT8G5hwMhpDdQY/K9HtwMyexXTawIDAQABAoIBAFwbHjuzJty4/34UsXNoQQ8iF1pY3+eVkJeAd1T1ixj/AzdU/UqRUape0xTKjZ1jpDwGDlqqqUDe/hWA0LYNM7XKrKeo6sbv605jo0YbyisUtdWeIUNp0GPhN7O8EpHoEM9JDlGwDrR3f7V87xHKdRNTD0QPT9HigFMJM6JxxL7r7lvaTN89pd9sqzF4SHuuguwCXDljSz+rgOMjqGTUt4eUaRO6jNjaB0WuI0PzMsDnQm2vEFtqiKtIhSpKRQhozMFkhlIprDNrErMcwRl9Wng3uuPHlVOSGvqBAWIU1IyNSLZC1FWR7mpm8f0a1b/PoDRLDw1sy6xQBF33fkjMjEkCgYEA7vt5zNlveNI8tAOvS04VlHhsxKs/lvCQtpP70453+d2VhgdEZaU3JcLlgKQ3l5/e6YuzxSnjjv6aRutoyIqvUVO1S4wK05z8U45tSX/N9o5H4TTKMMHtbMOogoEcsSzjPGG6NKA+deo+OCMXkc+Ic8LeK37XHHimmohr6U5Ngs0CgYEAzZvTzWPkVMMaXp6ZtrN297YpFExMlRyFcucBTGxzVBZN3+DHZ6hT/mDila/P2jgXGN0PKQkXFdm0nWmzdC+xK9ALWq5VE9pewKsdoVQRVHHob4QwO8Fyz/JGO+xx4gyyqNYUVRDkDGYwqE5fyPJdrfIZ+s4mJGr/TTe93CrpXxcCgYEAiXwlM0N4DNMuVwWzqCgQ5CxvrqkyWRymtFLoUmoo/ZeLuOKfZ7anxlKcg+h1jwZLUKKtVmVl49L3YIt36b9XYvs8Vm0HoXwz8cIDf4BGNmzeT2J2W5yFgcgCPqoWnptLU2aJoFL7/Aw5pUQTADCbuLVH6U/8YwXKGycWpc8FapUCgYAvRdCgvILW6jS55IU6+HA16+/t9sz0y6XzESG/TIAHqUkmXCv8rTsClYwDFnUXmyXUVZ8StEBWicN5HcBgykZV6HNz2fk9o0t9yNn7KbUCUV47pYzhcEzResajKG3Wp0y8fNwX4tZL84Lkb4CeVvsq7ysB+zvRy7f/LP2+DS+y6QKBgQCIgI+Kx+MJAk75nQ23ewSlxP2TzMgceDFVVOjbOlVS0wEhY2XCk9CjTOxYGWiJqlDYnQnGc++vwYb4IFNj2aOQGml3FhaA7sL3YMXiJ5eXic3fVq8jDyf/PUDo6FdCPgJ+Ym44O/UfC4rdE1Tb7J9atcF4TB8bl+DezqwwJOgxjg==';
		//后端可配置，当前使用的是沙箱环境的key
		$aop->alipayrsaPublicKey=sys_configModel::getConfig(sys_configModel::CACHE_KEY_SCORE, "zm_public_key"); //'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAlwAF5gdxQn5rCqhg4/JZGOSOIYpnmfYMn8eikTHXU8fqqiBymFH4fy+JMgIOuISO8DnvH94zgBYYK4pVP1IKo+ekG71wBc7wTZPIEwHBRuioW7hgk1JYPxsy7Hr7XPqZpj0ugZgr47WicwCa3oq86z2PcQXq+hfFmM8mDd0jRE7tu0mALdSA9arsE7cAvHJFGqpB/xC2iH2LQ70K02jRNPT29THWSOxnkGjDfQyj3g0BVKo9ZDeKkkmXv1ahqLYVEwvw1NJ7z5QqzWnxeLB/3wGYr3QcCNPLDG8HaRLaOu/qMj9nwk2hglHw81Bps3ri/+hUJ3ibkICtfho6Nhn1lQIDAQAB';
		$aop->apiVersion = '1.0';
		$aop->signType = 'RSA2';
		$aop->postCharset='UTF-8';
		$aop->format='json';
		$request = new ZhimaCreditScoreBriefGetRequest ();
		$transaction_id = date("Ymd") . $id_no;
		$request->setBizContent("{" .
		"\"transaction_id\":\"{$transaction_id}\"," .  //按日期+身份证号生成
		"\"product_code\":\"w1010100000000002733\"," . //固定不可改变
		"\"cert_type\":\"IDENTITY_CARD\"," . //固定不可改变
		"\"cert_no\":\"{$id_no}\"," .
		"\"name\":\"{$uname}\"," . 
		"\"admittance_score\":{$credit_score}" .
		"  }");
		$result = $aop->execute( $request);
		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$resultCode = $result->$responseNode->code;
		if(!empty($resultCode)&&$resultCode == 10000){
			if(strtoupper($result->$responseNode->is_admittance) == "Y") {
				return true;
			}
			else {
				return false;
			}
		} else {
			return false;
		}
 	}

 }

//  $client = new ZhimaCreditScoreBriefHelper();
//  $rs = $client->checkCredit("422828198409138473", "李某四", 700);
//  var_dump($rs);

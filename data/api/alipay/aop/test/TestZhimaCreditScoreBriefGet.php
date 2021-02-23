<?php

/**
 * Test ZhimaCreditScoreBriefGetRequest interface
 */
include('../AopClient.php');
include('../request/ZhimaCreditScoreBriefGetRequest.php');
$aop = new AopClient ();
$aop->gatewayUrl = 'https://openapi.alipaydev.com/gateway.do'; //https://openapi.alipay.com/gateway.do';
$aop->appId = '2016090900468535';
$aop->rsaPrivateKey = 'MIIEpAIBAAKCAQEAv/DYhMsqsbLZapB77Tn0IGxqJruVf3I7exA/FOL7FGAvlxdb
9LCtDq5CmYU/6G+yPhAe35YDsRuHmEC3Tqheg/BbtbWaOwFtL7/vJdYD6hPaaVEw
Y7nMMYmv/W0GEHLEDwaQ3k+ZA9HD6tc/Kllzf76jqLF8tXq7H0+0rzAPon+DO9I6
WlOself3EHdXXQ+JLrfHEqZ+kVJOXFAcLsPHX7Ae0cvGsC9coVB7vTY/i93ouf8D
apNUZOxC3ust2F8+pjFbDyiP6U/pYnV0EqDhyzS+lAaWDb7154OiebAA9Rvu91ln
33Aur1NwffGT8G5hwMhpDdQY/K9HtwMyexXTawIDAQABAoIBAFwbHjuzJty4/34U
sXNoQQ8iF1pY3+eVkJeAd1T1ixj/AzdU/UqRUape0xTKjZ1jpDwGDlqqqUDe/hWA
0LYNM7XKrKeo6sbv605jo0YbyisUtdWeIUNp0GPhN7O8EpHoEM9JDlGwDrR3f7V8
7xHKdRNTD0QPT9HigFMJM6JxxL7r7lvaTN89pd9sqzF4SHuuguwCXDljSz+rgOMj
qGTUt4eUaRO6jNjaB0WuI0PzMsDnQm2vEFtqiKtIhSpKRQhozMFkhlIprDNrErMc
wRl9Wng3uuPHlVOSGvqBAWIU1IyNSLZC1FWR7mpm8f0a1b/PoDRLDw1sy6xQBF33
fkjMjEkCgYEA7vt5zNlveNI8tAOvS04VlHhsxKs/lvCQtpP70453+d2VhgdEZaU3
JcLlgKQ3l5/e6YuzxSnjjv6aRutoyIqvUVO1S4wK05z8U45tSX/N9o5H4TTKMMHt
bMOogoEcsSzjPGG6NKA+deo+OCMXkc+Ic8LeK37XHHimmohr6U5Ngs0CgYEAzZvT
zWPkVMMaXp6ZtrN297YpFExMlRyFcucBTGxzVBZN3+DHZ6hT/mDila/P2jgXGN0P
KQkXFdm0nWmzdC+xK9ALWq5VE9pewKsdoVQRVHHob4QwO8Fyz/JGO+xx4gyyqNYU
VRDkDGYwqE5fyPJdrfIZ+s4mJGr/TTe93CrpXxcCgYEAiXwlM0N4DNMuVwWzqCgQ
5CxvrqkyWRymtFLoUmoo/ZeLuOKfZ7anxlKcg+h1jwZLUKKtVmVl49L3YIt36b9X
Yvs8Vm0HoXwz8cIDf4BGNmzeT2J2W5yFgcgCPqoWnptLU2aJoFL7/Aw5pUQTADCb
uLVH6U/8YwXKGycWpc8FapUCgYAvRdCgvILW6jS55IU6+HA16+/t9sz0y6XzESG/
TIAHqUkmXCv8rTsClYwDFnUXmyXUVZ8StEBWicN5HcBgykZV6HNz2fk9o0t9yNn7
KbUCUV47pYzhcEzResajKG3Wp0y8fNwX4tZL84Lkb4CeVvsq7ysB+zvRy7f/LP2+
DS+y6QKBgQCIgI+Kx+MJAk75nQ23ewSlxP2TzMgceDFVVOjbOlVS0wEhY2XCk9Cj
TOxYGWiJqlDYnQnGc++vwYb4IFNj2aOQGml3FhaA7sL3YMXiJ5eXic3fVq8jDyf/
PUDo6FdCPgJ+Ym44O/UfC4rdE1Tb7J9atcF4TB8bl+DezqwwJOgxjg==';
$aop->alipayrsaPublicKey='MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAlwAF5gdxQn5rCqhg4/JZGOSOIYpnmfYMn8eikTHXU8fqqiBymFH4fy+JMgIOuISO8DnvH94zgBYYK4pVP1IKo+ekG71wBc7wTZPIEwHBRuioW7hgk1JYPxsy7Hr7XPqZpj0ugZgr47WicwCa3oq86z2PcQXq+hfFmM8mDd0jRE7tu0mALdSA9arsE7cAvHJFGqpB/xC2iH2LQ70K02jRNPT29THWSOxnkGjDfQyj3g0BVKo9ZDeKkkmXv1ahqLYVEwvw1NJ7z5QqzWnxeLB/3wGYr3QcCNPLDG8HaRLaOu/qMj9nwk2hglHw81Bps3ri/+hUJ3ibkICtfho6Nhn1lQIDAQAB';
$aop->apiVersion = '1.0';
$aop->signType = 'RSA2';
$aop->postCharset='UTF-8';
$aop->format='json';
$request = new ZhimaCreditScoreBriefGetRequest ();
$request->setBizContent("{" .
"\"transaction_id\":\"201512100936588040000000465158\"," .
"\"product_code\":\"w1010100000000002733\"," .
"\"cert_type\":\"IDENTITY_CARD\"," .
"\"cert_no\":\"422828198412083938\"," .
"\"name\":\"汪文刚\"," .
"\"admittance_score\":900" .
"  }");
$result = $aop->execute ( $request); 
echo "<pre>";
var_dump($result);
echo "</pre>";
$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
$resultCode = $result->$responseNode->code;
if(!empty($resultCode)&&$resultCode == 10000){
	echo "成功";
} else {
	echo "失败";
}
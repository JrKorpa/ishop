<?php 

class EventEmitter {

    private static $client = null;
    private static $inited = false;
    
    private static function init() {
        if (self::$client == null) {
            global $config;
            $job_servers = isset($config['gearmand']) ? $config['gearmand'] : [];
            if (!empty($job_servers) && is_array($job_servers)) {
                if(!class_exists("GearmanClient",false)){
                    file_put_contents(BASE_DATA_PATH.'/log/gearman.log', "GearmanClient not exists!".PHP_EOL, FILE_APPEND);
                    return null;
                }
                self::$client = new GearmanClient();
                foreach ($job_servers as $serv) {
                    try {
                    	$resp = self::$client->addServer($serv['host'], $serv['port']);
                    	if ($resp === true) self::$inited = true;
                    	else {
                    	    file_put_contents(BASE_DATA_PATH.'/log/gearman.log', "can not add server ".$serv['host'].':'.$serv['port'].PHP_EOL, FILE_APPEND);
                    	}
                    } catch (Exception $e) {
                        file_put_contents(BASE_DATA_PATH.'/log/gearman.log', "exception when adding server ".$serv['host'].':'.$serv['port'].', and error is:'.$e->getMessage().PHP_EOL, FILE_APPEND);
                    }
                }
                //fault tolerance 
                return self::$inited;
            }
        }
        
        return self::$inited;
    }
    
    public static function dispatch($queue, $payload, $scope = 'ishop') {
    	if (self::init()) {
    		$payload['sys_scope'] = $scope;
    		$payload['timestamp'] = date('Y-m-d H:i:s'); //time();
    		$payload['msgId'] = self::getGUID();
    		
            $num = 0;
            self::$client->setTimeout(3000);
    		do {
                self::$client->doBackground($queue, json_encode($payload, JSON_UNESCAPED_UNICODE));
    			if (self::$client->returnCode() == GEARMAN_SUCCESS) {
    				return true;
    			}
    			
    			$num++;
    		} while($num < 3);
    		file_put_contents(BASE_DATA_PATH.'/log/gearman.log', json_encode($payload).PHP_EOL, FILE_APPEND);
    	}
    	return false;
    }
    
    private static function getGUID() {
        if (function_exists('com_create_guid')) {
            return substr(com_create_guid(), 1, 36);
        } else {
            
            if (function_exists('openssl_random_pseudo_bytes') === true) {
                $data = openssl_random_pseudo_bytes(16);
                $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
                $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
                return vsprintf('%s%s%s%s%s%s%s%s', str_split(bin2hex($data), 4));
            }
            
            mt_srand((double) microtime() * 10000);
            $charid = strtoupper(md5(uniqid(rand(), true)));
            
            return substr($charid, 0, 8).substr($charid, 8, 4).substr($charid, 12, 4).substr($charid, 16, 4).substr($charid, 20, 12);
        }
    }
}



?>
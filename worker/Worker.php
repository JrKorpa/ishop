<?php

class Worker {

	private $gworker;
	private $gclient;
	private $job_servers;
	
	public function __construct($job_server_list) {
		
		$this->job_servers = $job_server_list;

		$this->gworker = new GearmanWorker();
		//$this->gclient = new GearmanClient();

		foreach ($job_server_list as $serv) {
			$this->gworker->addServer($serv['host'], $serv['port']);
			//$this->gclient->addServer($serv['host'], $serv['port']);
		}
	}

	public function bind($queue) {
        $that = $this;
        $this->gworker->addFunction($queue, function($job) use($queue, $that) {
			$message = $job->workload();
			echo 'got a message:'.$message.PHP_EOL;
	
			$data = json_decode($message, true);
				    
			try {

			    if (empty($data) || !isset($data['event']) || empty($data['event'])) {
			        echo 'no event exits.'.PHP_EOL;
			        return;
			    }
							
				$event = trim($data['event']);
				$sys_scope = $data['sys_scope'];		
				
				// 加载消息处理函数
				$file = __DIR__ .'/'.$queue.'/'.$event.'.php';
				if (!include_once($file)) {
				    file_put_contents(__DIR__ . '/'.$queue.'_'.date('Ymd').'.log',  $message.PHP_EOL, FILE_APPEND);
				    return;
				}

				if (function_exists($sys_scope.'_on_'.$event)) {
					$mt = $sys_scope.'_on_'.$event;
				} else if (function_exists('on_'.$event)) {
					$mt = 'on_'.$event;
				} else {					
					echo 'no handler exists for this message.'.PHP_EOL;
					return;
				}
				
				$result = $mt($data);
				
				if ($result === true) return;
				
				if ($result !== true) {
					// 严重错误不再重试；
					if ($result == -1) $data['retry'] = 2;
					
					// 当次执行失败，但失败次数在允许范围内
					if (!isset($data['retry']) || $data['retry'] < 2) {
						// 将消息重新入列
						$data['retry'] = isset($data['retry']) ? $data['retry'] + 1 : 1;
						$that->dispatch($queue, $data, $sys_scope);
					} else {
						// 多次尝试仍失败，记录失败消息
						file_put_contents(__DIR__ . '/'.$queue.'_'.date('Ymd').'.fail.log',  $message.PHP_EOL, FILE_APPEND);
					}
				}
							
			} catch (Exception $ex) {
				
				$err = $ex->getMessage();
				echo $err;
				
				file_put_contents(__DIR__ . '/'.$queue.'_'.date('Ymd').'.err.log',  $err.PHP_EOL, FILE_APPEND);
				
				// 当次执行失败，但失败次数在允许范围内
				if (!isset($data['retry']) || $data['retry'] < 2) {
					// 将消息重新入列
					$data['retry'] = isset($data['retry']) ? $data['retry'] + 1 : 1;
					$that->dispatch($queue, $data, $data['sys_scope']);
				}
			}
		});
	}
	
	public function start() {
	    $this->gworker->setTimeout(5000);
	    
		while(true){
			try {
				$this->gworker->work();

			} catch(Exception $ex) {
				echo $ex->getMessage();
				sleep(5);
			}
		}
	}
	
	public function dispatch($queue, $payload, $sys_scope = 'ishop') {
		if (!$this->gclient) {
			$client = new GearmanClient();

			foreach ($this->job_servers as $serv) {
				$client->addServer($serv['host'], $serv['port']);
			}

			$this->gclient = $client;
		}

        if ($this->gclient) {
			
			$payload['sys_scope'] = $sys_scope;
			$payload['timestamp'] = date('Y-m-d H:i:s'); //time();
			$payload['msgId'] = $this->getGUID();
			
			//$this->gclient->setTimeout(5000);
            $i = 0;
			do {
				$handle = $this->gclient->doBackground($queue, json_encode($payload, JSON_UNESCAPED_UNICODE));
				if ($this->gclient->returnCode() == GEARMAN_SUCCESS) return true;
				sleep(3);
			} while(++$i < 3);
		}
		
		echo 'fail to dispatch msg'.PHP_EOL;
		file_put_contents(__DIR__ . '/'.$queue.'_'.date('Ymd').'.dispatch_fail.log', json_encode($payload, JSON_UNESCAPED_UNICODE).PHP_EOL, FILE_APPEND);	
        return false;
	}
	
	private function getGUID() {
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

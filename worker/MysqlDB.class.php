<?php 
/**
 *  -------------------------------------------------
 * 文件说明		Mysql PDO 数据库操作类
 * @file		: MysqlDB.calss.php
 * @author		: quanxyun <quanxyun@gmail.com>
 *  -------------------------------------------------
*/
class MysqlDB { 

    protected $_pdo = null;
    public  $statement = null; 
	protected $_conf = null;
	private $sleep_if_pdo_error = 0;
	private $table_fields =[];

    function __construct($conf) {  
		$this->_conf = $conf;
    }

    public function db()
    {
		if ($this->_pdo == null && $this->_conf != null) {
			try {  
				$conf = $this->_conf;
				$this->_pdo = new PDO($conf['dsn'], $conf['user'], $conf['password'], array(PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES '.$conf['charset']));  
				$this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 

				$this->sleep_if_pdo_error = 0;				
			} catch (PDOException $e) {  
				echo $e->getMessage();
				$this->sleep_if_pdo_error = $this->sleep_if_pdo_error + 5;
			    
				sleep($this->sleep_if_pdo_error);
				return $this->db();
			} 
		}
		return $this->_pdo;
    }
	
	public function dispose() {
		$this->_pdo = null;
	}

	public function selectDB($db)
	{
		return $this->db()->query('use '.$db);
	}
   	
	public function setChar($char){
		return $this->db()->exec('set names '.$char);
	}

	public function insertId()
	{
		return $this->db()->lastInsertId();
	}

	public function prepare($sql){
        if(empty($sql)){return false;}
        $this->statement = $this->db()->prepare($sql);
        return $this->statement;
    }
    //返回影响的行数 [insert/update/delete]
    public function exec($sql)
    {
        if(empty($sql)){return false;}
        try{
            return $this->db()->exec($sql);
        }catch(Exception $e){
            return false;
        }
    }
    //返回PDOStatement,[select]链式操作
    public function query($sql)
    {
        if(empty($sql)){return false;}
        $this->statement = $this->db()->query($sql);
        return $this->statement;
    }

    public function insert($table,$row)
    {
    	if(empty($row) || !is_array($row)){
    		return false;
    	}
    	return $this->autoExec($row,$table);
    }

    public function update($table,$row,$where)
    {
    	if(empty($row) || !is_array($row) || empty($where) || !is_array($row)){
    		return false;
    	}
    	return $this->autoExec($row,$table,'UPDATE',$where);
    }

    public function getAll($sql,$fetch_type = PDO::FETCH_ASSOC)
    {
    	if(empty($sql)){return false;}
    	$data = $this->query($sql)->fetchAll($fetch_type);
    	return $data;
    }

    public function getRow($sql,$fetch_type = PDO::FETCH_ASSOC)
    {
    	if(empty($sql)){return false;}
		$data = $this->query($sql)->fetch($fetch_type);
    	return $data;
    }

    public function getOne($sql,$fetch_type = PDO::FETCH_NUM)
    {
    	if(empty($sql)){return false;}
		$data = $this->query($sql)->fetch($fetch_type);
    	return $data[0];
    }

    public function getFields($table)
	{
		if (array_key_exists($table, $this->table_fields)) {
			return $this->table_fields[$table];
		}
		
		$sql = "DESCRIBE ".$table;
		$res = $this->getAll($sql);
		foreach ($res as $val) {
			$fields[] = $val['Field'];
		}
		
		$this->table_fields[$table] = $fields;
		return $fields;
	}

	/**
	 * 自动 插入/更新
	 * @param array 	$data
	 * @param string	$table
	 * @param string 	$act
	 * @param array 	$where
	 * @return bool
	 */
    public function autoExec($data,$table,$act='INSERT',$where=array()){
    	$fields = $this->getFields($table);
        $param = array();
    	foreach ($fields as $v) {
            if(array_key_exists($v,$data)){
                $param[$v] = ":".$v;
            }
        }
		
		$ignore_keys = array_diff(array_keys($data), array_keys($param));
		if (!empty($ignore_keys)) {
			foreach ($ignore_keys as $key) {
				unset($data[$key]);
			}
		}
		
        /*
        ksort($param);
        ksort($data);
        print_r($param);
        print_r($data);
        */
        
        if ($act == 'INSERT') {
        	$sql = sprintf("INSERT INTO `%s` (%s) VALUES (%s)",$table,implode(',',array_keys($param)),implode(',',$param));
        }
        
        if (($act == 'UPDATE') && !empty($where)) {
            $set = [];
            foreach ($param as $k => $v) {
                $set[] = "`".$k."` = ".$v."";  
            }
            $set = implode(',', $set);
            
            $_where = [];
            foreach ($where as $k=>$v) {
                $_where[] = "`".$k."` = :".$k;
            }
            
            $_where = implode(' AND ', $_where);
            $sql = "UPDATE `".$table."` SET ".$set." WHERE ".$_where;
            
            $data = array_merge($data,$where);
        }

        $_res = $this->prepare($sql);
        
        try {
            $_res->execute($data);
        } catch (PDOException $e) {
			$err = ['sql' => $sql, 'data' => $data, 'err' => $e->getMessage()];
			file_put_contents(__DIR__ . '/'.date('Ymd').'.db.log',  json_encode($err).PHP_EOL, FILE_APPEND);
            return false;
        }

        return true;
    }

    /**
	 * 替换字典
	 * @param array $data
	 * @param array $dict
	 * @return mixed
	 */
    public function replaceDict($data,$dict)
    {
    	foreach ($data as $k=>$row) {
    		foreach ($dict as $lab=>$val) {
    			if(array_key_exists($lab,$row)){
				    $data[$k][$lab] = $val[$data[$k][$lab]];
    			}
    		}
    	}
    	return $data;
    }

    /**
     * 设置默认值
     */
    public function setDefault($data,$default){
		foreach ($data as $k=>$row) {
			foreach ($default as $lab=>$v) {
				$data[$k][$lab] = $v;
			}
		}
		return $data;
	}

	/**
	 * 替换字段名称
	 * @param array	$filter
	 * @return bool|string
	 */
	public function replaceFields($filter){
		if(empty($filter)){return false;}
        $r_sel = '';
        foreach ($filter as $k => $v) {
            $r_sel .= "`".$k."` AS `".$v."`,";
        }
        return rtrim($r_sel,',');
    }

    /**
	 *	批量更新/插入
	 * @param $data
	 * @param $table
	 * @param string $act
	 * @param array $where 现只判断等于情况
	 * @return bool
	 * @note	: 注[data与where 字段不能重复]
	 */
	public function autoExecALL($data,$table,$act='INSERT',$where = array()){
		
		if ($act == 'UPDATE' && count($data) != count($where)) {
			return false;
		} 
		
		foreach ($data as $i => $row) {
			
			$resp = $this->autoExec($row, $table,$act, empty($where) ? $where:  $where[$i]);
			if ($resp === false) {
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * upsert, 支持单条或多条数据的upsert操作
	 */
	public function upsert($data, $table, $duplic_keys = []) {
	   
	    $fields = $this->getFields($table);
	    $param = [];
	    $key_vals = [];
		
		if (count($data) == count($data, 1)) {
		    $list = [];
		    $list[] = $data;
		    $data = $list;
		}
		
	    foreach ($fields as $v) {
	        if(array_key_exists($v,$data[0])) {
	            $param[$v] = ":".$v;
	            if (!in_array($v, $duplic_keys))  $key_vals[] = '`'.$v.'`=:'.$v;
	        }
		}

		$ignore_keys = array_diff(array_keys($data[0]), array_keys($param));
		if (!empty($ignore_keys)) {
			foreach ($ignore_keys as $key) {
				foreach ($data as $d) {
					unset($d[$key]);
				}
			}
		}
	        
	    $sql = "INSERT INTO `".$table."` (".implode(',', array_keys($param)).") VALUES(".implode(',', array_values($param)).")
                 ON DUPLICATE KEY UPDATE ".implode(',', array_values($key_vals));
	    	    
	    foreach ($data as $values) {
	    
    	    $_res = $this->prepare($sql);
    	    try {
    	        $_res->execute($values);
    	    } catch (PDOException $e) {
    	        $err = ['sql' => $sql, 'data' => $values, 'err' => $e->getMessage()];
    	        file_put_contents(__DIR__ . '/'.date('Ymd').'.db.log',  json_encode($err).PHP_EOL, FILE_APPEND);
    	        return false;
    	    }
	    }
	    
	    return true;
	}

	/**
	 * 合并三维数组
	 */
	public function arr_merge($n,$m){
		return array_merge($n,$m);
	}

	public function beginTransaction() {
		$this->db()->beginTransaction();
	}
	
	public function rollback() {
	    if ($this->db()->inTransaction()) {
		  $this->db()->rollback();
		  $this->db()->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
	    }
	}
	
	public function commit() {
	    if ($this->db()->inTransaction()) {
		  $this->db()->commit();
		  $this->db()->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
	    }
	}
} 

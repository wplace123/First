<?php 
header("Content-Type:text/html; charset=UTF8");
class MyPDO extends PDO{
	private $_dsn = 'mysql:host=localhost;dbname=test';	//port如果為3306可以省略
	private $_user = 'root';
	private $_passwd = '';
	private $_encode = 'utf8';
	private $_stmt;
	private $_data = [];
	private $_last_insert_id;

	function __set($name,$value){
		$this->_data[$name] = $value;
	}

	function __get($name){
		if(isset($this->_data[$name])){
			return $this->_data[$name];
		}
		return false;
	}

	function __construct(){
		try{
			Parent::__construct($this->_dsn,$this->_user,$this->_passwd);
			$this->_setEncode();
		}catch(Exception $ex){
			print_r($ex);
		}
	}

	private function _setEncode(){
		$this->query("SET NAMES '{$this->_encode}'");
	}

	// function bindQuery($sql,array $bind = []){
	// 	$this->_stmt = $this->prepare($sql);
	// 	$this->_bind($bind);
	// 	$this->_stmt->excute();
	// 	return $this->_stmt->fetchAll();
	// }

	private function _bind($bind){
		foreach($bind as $key => $value){
			$this->_stmt->bindValue($key, $value, is_numeric($value)?PDO::PARAM_INT : PDO::PARAM_STR);	//(第三個參數)轉成相對應的數值或字串
		}
	}

	function error(){
		$error = $this->_stmt->errorinfo();
		if($error[0] != 00000){
			echo 'errorCode:'.$error[0].'<br>';
			echo 'errorString:'.$error[2].'<br>';
		}
	}

	function getData(){
		return $this->_data;
	}

	function insert($table, array $param = []){
		$data = array_merge($this->_data, $param);
		$columns = array_keys($data);
		$values = [];
		$bind_data = [];
		foreach($data as $key => $value){
			$values[] = ":{$key}";
			$bind_data[":{$key}"] = $value;
		}
		$sql = "INSERT INTO {$table} (" .implode(',',$columns) .") VALUES (" .implode(',',$values) .")";
		$this->_stmt = $this->prepare($sql);
		$this->_bind($bind_data);
		$this->_stmt->execute();
		return $this->_last_insert_id = $this->lastInsertId();
	}

	function getInsertId(){
		return $this->_last_insert_id;
	}

	function update($table, array $param = [], $whereSql = ''){	//第三個參數改為SQL較好?
		if($id = false) return false;
		$data = array_merge($this->_data, $param);
		$bind_temp = [];
		$bind_data = [];
		foreach($data as $key => $value){
			$bind_temp[] = "{$key} = :{$key}";
			$bind_data[":{$key}"] = $value;
		}
		$sql = "UPDATE {$table} SET " .implode(',', $bind_temp) ." $whereSql";
		$this->_stmt = $this->prepare($sql);
		$this->_bind($bind_data);
		$this->_stmt->execute();
	}
}


?>
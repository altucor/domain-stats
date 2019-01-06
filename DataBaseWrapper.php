<?php

require "rb-mysql.php";

class DataBaseWrapper
{
	function __construct($dbName, $login, $pass)
	{
		$this->db = $dbName;
		$this->table = 'stats';
		
		R::setup('mysql:host=localhost;dbname='.$dbName, $login, $pass);
		R::debug(false);
		
		$this->checkTable();
	}
	
	function __destruct()
	{
		R::close();
	}
	
	public function checkTable(){
		$sql = "SHOW TABLES LIKE '" . $this->table . "'";
		$result = R::exec( $sql );
		
		if( $result == 0 ){
			$sql = 'CREATE TABLE ' . $this->table . ' (
			id int(100) NOT NULL AUTO_INCREMENT,
			scheme VARCHAR(10) NOT NULL,
			domain VARCHAR(100) NOT NULL,
			uri VARCHAR(1000) NOT NULL,
			method VARCHAR(10) NOT NULL,
			ip VARCHAR(50) NOT NULL,
			port int(10) NOT NULL,
			browserName VARCHAR(50) NOT NULL,
			browserVersion VARCHAR(50) NOT NULL,
			time int(50) NOT NULL,
			operationSystem VARCHAR(50) NOT NULL,
			userFingerPrint VARCHAR(50) NOT NULL,
			uniqueRowHash VARCHAR(50) NOT NULL,
			UNIQUE KEY id (id)
			) ENGINE=MyISAM';
			
			R::exec($sql);
		}
	}
	
	public function optimizeTable(){
		$sql = 'OPTIMIZE TABLE '. $this->table . ';';
		R::exec($sql);
	}
	
	public function insertArray($userData)
	{		
		$sql =  'INSERT INTO '.$this->table.' (scheme, domain, uri, method, ip, port, browserName, browserVersion, time, operationSystem, userFingerPrint, uniqueRowHash)';
		$sql .= ' Values ( ';
		$sql .= "'{$userData['scheme']}', ";
		$sql .= "'{$userData['domain']}', ";
		$sql .= "'{$userData['uri']}', ";		
		$sql .= "'{$userData['method']}', ";		
		$sql .= "'{$userData['ip']}', ";
		$sql .= "'{$userData['port']}', ";
		$sql .= "'{$userData['browserName']}', ";
		$sql .= "'{$userData['browserVersion']}', ";
		$sql .= "'{$userData['time']}', ";
		$sql .= "'{$userData['operationSystem']}', ";
		$sql .= "'{$userData['userFingerPrint']}', ";
		$sql .= "'{$userData['uniqueRowHash']}'";
		$sql .= ');';
		
		R::exec($sql);
	}
}

?>
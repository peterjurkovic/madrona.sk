<?php
	session_start();
	
	require_once "../../config.php";
		
	function __autoload($class){
		require_once "../class.".$class.".php";
	}
	
	
	try{
		$conn = Database::getInstance($config['db_server'], $config['db_user'], $config['db_pass'], $config['db_name']);
		$auth = new Authenticate($conn);
		$auth->logout();
		header("Location: ./../../");		
	}catch(MysqlException $e){
		exit( "Vyskytol sa problém s databázou." );
	}
	exit;

		
?>
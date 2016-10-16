<?php
	session_start();
	header('X-Frame-Options: deny');
	
	ini_set("display_errors", 1);
	ini_set('log_errors', 1);
	ini_set('error_log', dirname(__FILE__).'/logs/php_errors.txt');
	
	require_once "./config.php";
	
	try{
		$conn = Database::getInstance($config['db_server'], $config['db_user'], $config['db_pass'], $config['db_name']);
	

	$auth = new Authenticate($conn);
	
	if($auth->isLogined()){
		include "./inc/main.index.php";
	}else{
		include "./inc/login/page.php";
	}
		
	}catch(MysqlException $ex){
		exit( "Vyskytol sa problém s databázou." );
	}
	exit;
?>
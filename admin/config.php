<?php
	$config["version"]		= "3.0.0";
	$config["deployed"]		= "";
	$config["adminPagi"]	= 10; // strankovanie articles
	$config["galleryPagi"]	= 15; // strankovanie galeria
	
	$config["db_server"]	= "localhost";		
	$config["db_user"]		= "root";			
	$config["db_pass"]		= "";		
	$config["db_name"]		= "madrona";	
	
	
	$config["ga_user"]		= "petojurkovic@gmail.com";
	$config["ga_pass"]		= "eG9NKnBvTU45M29JZA==";
	$config["ga_profile"]	= "ga:19643449";
	
	$config["article_langs"]	= "sk";
	$config['root_dir'] = dirname(__FILE__) ;
		
	$config['shop_prefix'] = "ponuka";			
	
	function classAutoLoad($class) {
	    require_once 'inc/class.'.$class.'.php';
	}

	spl_autoload_register('classAutoLoad');

?>

<?php
/*
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__).'/log.txt');
error_reporting(E_ALL);
*/
		 
include_once './../inc/class.Image.php';	


$args = explode( "/", $_GET['q']  );

$params = explode("-", $args[0]);

$image = new Image("../../data/".$args[1]."/".$args[2].(isset($args[3]) ? "/".$args[3] : "" ));	

$image->resizeImage($params[0], $params[1], $params[2], false, true);

?>

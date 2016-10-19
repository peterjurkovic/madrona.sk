<?php
require_once "../admin/config.php";
require_once "../admin/inc/fnc.main.php";
require_once "../admin/page/fnc.page.php";


$name 		= trim($_POST['name']);
$email 		= trim($_POST['email']);
$message 	= trim($_POST['message']);

if(strlen($name) < 2 || strlen($message) < 10 || strlen($email) < 3){
	error( "Prosím, vyplňte formulár" );
	exit;
}

if( ! isEmail($email) ){
	error( "Neplatná e-mailová adresa");
	exit;
}
$conn = Database::getInstance($config['db_server'], $config['db_user'], $config['db_pass'], $config['db_name']);
$conf = getConfig($conn, "config", "full");

$mail = PHPMailer::createInstance();
$mail->SetFrom($email , $name);
$mail->Subject = "Správa z madrona.sk";
$mail->Body = $message;
$mail->isHTML(false);
$m = $name .'//'. $email . '//'. $message;
$sent = $mail->Send();
if( ! $sent ) {
    error( "Správu sa nepodarilo odoslať");
}else{
	ok();
}
$conn->insert('INSERT INTO `emails`(`user_agent`, `ip_address`, `msg`, `sent`) VALUES (?,?,?,?)',
		array($_SERVER['HTTP_USER_AGENT'], $_SERVER['REMOTE_ADDR'], $m, $sent )
);
exit;
function error($msg){
	echo json_encode(array( 
		"sent" => false,  
		"error" => $msg
	));
}

function ok(){
	echo json_encode(array( "sent" =>  true));
}
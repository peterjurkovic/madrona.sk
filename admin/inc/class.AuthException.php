<?php

class AuthException extends Exception{

    public function __construct($message, $save = false){
        if($save){
			$logFile = dirname(__FILE__)."/../logs/auth_log.txt";
			if($fp = fopen( $logFile, 'a' )){
				 $msgToLog = date("[Y-m_d H:i:s]"). "|  $message \n";
				 fwrite($fp, $msgToLog);
				 fclose($fp);
			}
		}
        parent::__construct( $message );
    } 
}

?>
<?php

class FileException extends Exception{

    public function __construct($script, $line, $message, $no = 0){
        $logFile = dirname(__FILE__)."/../logs/file_errors.txt";
        if($fp = fopen( $logFile, 'a' )){
             $msgToLog = date("[Y-m_d H:i:s]"). "| Script: $script |  Line : $line | NO : $no |  MSG: $message \n";
             fwrite($fp, $msgToLog);
             fclose($fp);
        }
        parent::__construct( $message );
    } 
}

?>
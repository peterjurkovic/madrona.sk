<?php
/**
* Google Analytics Exception class
*
* @author Peter Jurkovic (into@peterjurkovic.sk)
* @version 20110411
*/
class GoogleAnalyticsException extends exception{

	public function __construct($message = false){
		global $config;
		$lf = $config['root_dir']."/logs/ga.errorLog.txt";
		if($fp = fopen($lf, 'a')){
			$log_msg = date("[Y-m_d H:i:s]"). "- Message: $message \n";
			fwrite($fp, $log_msg);
			fclose($fp);
		}
		
		parent:: __construct( $message);
	
	}

} 


?>
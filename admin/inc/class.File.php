<?php



class File
{

	private $file;


	public function createDir($url, $dir_name){
	  $fullurl = $url.$dir_name."/";
		if(!file_exists($fullurl)){
			mkdir($fullurl);
			chmod($fullurl, 0775);
		}
	  return $fullurl;
	}
	
	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
	
	public function upload($url, $file, $rewrite = true, $filter = null){
	
		$this->file = $file;
		 
		if($this->file['error'] > 0){
			throw new FileException(basename(__FILE__) , __LINE__ , $this->nameOfError($this->file['error']). ' ('.$this->file['name'].')', $this->file['error'] );
		}
		 
		if($filter != null){
			if(!$this->FileFilter($filter)){
				throw new FileException(basename(__FILE__) , __LINE__ , 'Nahrávaný súbor: '.$this->file['name'].' nie je typu: '.$filter);
			}
		}
		 
		
		$this->file['name'] = $this->renameFile($this->file['name']);
		 if(!$rewrite){
				$this->FileExists($url);
			}
		 if(!is_uploaded_file($this->file['tmp_name'])){
			throw new FileException(basename(__FILE__) , __LINE__ , 'Nepodarilo sa nahrať súbor: '.$this->file['name']);
		 }
		 if(!move_uploaded_file($this->file['tmp_name'], $url.$this->file['name'])){
				throw new FileException(basename(__FILE__) , __LINE__ , 'Presunutie súboru zlyhalo: '.$this->file['name']);
			}
		 chmod($url.$this->file['name'], 0775);
	  return true;
	  }
	  
	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
	
	
	public function scanFolder($url, $formats = "*"){
		$array = array();
		if(!is_dir($url)){ return array(); }
	
		$files = glob($url.$formats);
	
		if($files == false) { return array();}
		foreach($files as $file){  
			array_push($array,  substr($file, (strrpos($url, "/")+1)));  
		}  
		return $array;
	}
	
	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
	
	
	public function scanDirs($url){
		return glob($url .'*', GLOB_ONLYDIR);
	}
	
	
	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
	
	public function deleteFile($url){
	  if(file_exists($url)){
		  unlink($url);
		  return true;
	  }
	  return false;
	}
	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
	
	public function deleteDir($url){
	  if(is_dir($url)){
		  
		$file_array = $this->ScanFolder($url);
		foreach($file_array as $file_name){
		   if(!$this->DeleteFile($url.$file_name)){
				return false;
			}
		}
		  if(!rmdir($url)){
			 return false;
		  }
		  return true;
	  }else{
		return false;
	  }
	}
	
	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
	public static function renameFile($string){
		$orig = array("Á", "Č", "D", "É", "E", "Í", "L", "Ľ", "N", "Ó", "R", "R", "Š", "T", "Ú", "Ž", "Ď", "Ť", "Ň");
		$repl = array("á", "c", "d", "é", "e", "í", "l", "l", "n", "ó", "r", "r", "š", "t", "ú", "ž", "ď", "ť", "ň");
		$string = trim(str_replace($orig, $repl, $string));
		$orig = array(" ", "l", "š", "č", "t", "ž", "ý", "á", "í", "é", "e", "ä", "ú", "ô","ó","ď", "ľ", "n", "ť", "ň", "!", "?", "'","%","&","@","*","€","/");
		$repl = array("-", "l", "s", "c", "t", "z", "y", "a", "i", "e", "e", "a", "u", "o","o","d", "l", "n", "t", "n", "", "", "", "", "","","","euro","-" );
		$string = strtolower(str_replace($orig, $repl, $string)); 
		return preg_replace ("/[-]+/", "-", $string);
	}
	
	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
	
	private function fileExists($url, $i = 0){   
		if(file_exists($url.$this->file['name'])){							   
			$format = substr($this->file['name'], strrpos($this->file['name'], "."));  
			$this->file['name']	= str_replace( "(".($i-1).")".$format, $format, $this->file['name']);
			$this->file['name'] = str_replace($format, "(".$i.")".$format, $this->file['name']);
			$i++;
			$this->FileExists($url, $i);
		  }
	}     
	
	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
	
	public function readText($url){
		if (file_exists($url))
			{
				$f =fopen($url, 'r');
				$s = "";
				while (!feof($f)) $s.=fgets($f);
				fclose($f);
				return $s;
			}
	}
	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
	public function writeText($url, $data){
		if (file_exists($url))
			{
			  $f = fopen($url, 'w');
			  fwrite($f, $data);
			  fclose($f);
			  return 'ok';
			}
	}
	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
	
	public function randFile($url){
		  $this->ScanFolder($url);
		  $j=-1;
		  $farray = array();
		  foreach($this->file_array as $file_name => $alt){
				$j++;
				$farray[$j] = $file_name;
		  }
		  return $randfile = $farray[rand(0, $j)];
	}
	
	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
	
	private function fileFilter($type){
		
		switch ($type){
			case 'images' : 
				if( !preg_match('/^(image)/', $this->file['type']) || 
					!preg_match('/(gif|jpe?g|png)$/i', $this->file['name']) ){
					return false;
				}	
			break;
			case 'music' : 
				if(!preg_match('/(mp3|ogg|wav|wma)$/i', $this->file['name'])){
					return false;
				}	
			break;
			}
		
		return true;
	}
	
	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
	
	public function nameOfError($no){
		
		switch((int)$no) {
			case 1:
				$error = 'Natala chyba. Maximálna povolená veľkosť nahrávaného súbora je: '.$this->toNum(ini_get('upload_max_filesize')). " KB";
				break;
			case 2:
				$error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
				break;
			case 3:
				$error = 'The uploaded file was only partially uploaded';
				break;
			case 4:
				$error = 'Súbor sa nepodarilo nahrať.';
				break;

			case 6:
			case 7:
				$error = 'Nastala chyba na strane servera.';
				break;
			case 8:
				$error = 'File upload stopped by extension';
				break;
			case 999:
			default:
				$error = 'Súbor sa nepodarilo nahrať.';
		}
		return $error;
	}
	
	//This function transforms the php.ini notation for numbers (like '2M') to an integer (2*1024*1024 in this case)
	private function toNum($v){ 
		$l = substr($v, -1);
		$ret = substr($v, 0, -1);
		switch(strtoupper($l)){
			case 'M':
				$ret *= 1024;
			case 'K':
				$ret *= 1;
				break;
			}
    	return $ret;
	}
	public function getFileName(){
		return $this->file['name'];
	}
	
}
?>

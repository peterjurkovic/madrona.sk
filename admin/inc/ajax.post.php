<?php
	session_start();
	function __autoload($class){
		include_once "./class.".$class.".php";
	}
	require_once "../config.php";
	include "./fnc.article.php";
	include "./fnc.main.php";
	ini_set("display_errors", 0);
	ini_set('log_errors', 1);
	ini_set('error_log', $config['root_dir'].'/logs/php_errors.txt');

	$data = array( "err" => 1, "msg" => "Operáciu sa nepodarilo vykonať, skúste to znova." );
	
try{
	$conn = Database::getInstance($config['db_server'], $config['db_user'], $config['db_pass'], $config['db_name']);
	
	function resizeImageFile($file, $toSize = 900){
		$image = new Image($file);
		$arr = $image->GetImageSize();
		if(($arr["width"] > $toSize) || ($arr["height"] > $toSize)){
			$image->resizeImage($toSize, $toSize);
			$image->saveImage($file);
		}
	}


	$auth = new Authenticate($conn);
	if(!$auth->isLogined()){  
		$data["msg"] = "Pre dlhú nečinnosť ste boli odhlásený.";
		echo json_encode($data); 
		exit();
	} 
	
	if(isset($_POST['id'])) $_POST['id'] = (int)$_POST['id'];
	
	switch((int)$_POST['act']){
		
		case 7 : 
			$lng = $_POST['lang'];
			//print_r($_POST);
			$a = $conn->select("SELECT `sub_id` FROM `article` WHERE `id_article`=? LIMIT 1", array( $_POST['id'] ));
			$a = $conn->simpleQuery("SELECT count(*) FROM `article` WHERE `id_article`!=".$_POST['id']." AND `sub_id`=".$a[0]["sub_id"]." AND `link_$lng`='".SEOlink($_POST["title_"])."'");
			if($a[0]["count(*)"] != 0 ){
				$data = array( "err" => 1, "msg" => "V kategórii sa už stránka s názvom: <strong>".$_POST["title_"]."</strong> nachádza." );
				break;
			}

			$conn->update("UPDATE `article` SET `type`=?, `edit`=?, `active`=?, `title_$lng`=?,`link_$lng`=?, `subtitle_$lng`=?, `header_$lng`=?, `content_$lng`=?, `editor`=? WHERE `id_article`=?", 
			array((int)$_POST['type'], time(), (int)$_POST['active'], $_POST["title_"], SEOlink($_POST["title_"]),  $_POST["subtitle_"], $_POST["header_"], $_POST["content_"], $_SESSION['id'], $_POST["id"])
			);
			$data = array( "err" => 0, "msg" => "Zmeny boli úspešne uložené" );	
		break;
		
		
		
		case 8 : 
				$lng = $_POST['lang'];
				$arr = $conn->select("SELECT `title_${lng}`, `subtitle_${lng}`, `header_${lng}`, `content_${lng}` FROM `article` WHERE `id_article`=? LIMIT 1", array( $_POST['id'] ) );
				$data = array();
				$arr[0] = cleanArticle($arr[0]);
				foreach ($arr[0] as $v){
					$data[] = $v;
				}
				
		break;
		
		// AVATARS upload ----------------
		case 10 : 
			if(isset($_FILES)){
				$data["err"] = 0;
				$file = new File();
				$sql = "UPDATE `".$_POST['table']."` SET ";
				$sqlData = array();
				$cols = array();
	
				try{
					foreach ($_FILES as $key => $f){
						if(strlen($_FILES[$key]['name']) > 4){
							$file->upload($config['fileDir']."/data/avatars/", $_FILES[$key], false, "images");
							resizeImageFile($config['fileDir']."/data/avatars/".$file->getFileName());
							$data[$key] = $file->getFileName();
							$cols[] =  '`'.$key.'`=? ';	
							$sqlData[] = $file->getFileName();
						}
					}
				
				}catch( FileException $e ){
					$data["msg"] = $e->getMessage();
					$data["err"] = 1;
					unset($_FILES);
					break;
				}
				
				if(count($sqlData) !=0  && isset($_POST['table']) && isset($_POST['id'])){
					$sqlData[] = $_SESSION['id'];
					$sqlData[] = $_POST['id'];
					if($conn->update($sql.implode(",", $cols).", `edit`=".time().", `editor`=? WHERE `id_".$_POST['table']."`=? LIMIT 1", $sqlData)){
						$data["msg"] = "Avaváry boli úspešne nahraté.";
					}
				}
			}else{
				$data["msg"] = "Vyberte aspoň jeden obrázok.";	
			}
		unset($_FILES);
		break;
		
		// GALLERY upload ----------------
		case 13 : 
			if(isset($_FILES)){		
				if(!isset($_POST['dirName']) || !isset($_POST['id'])){
					break;
				}
				$file = new File();
				$url = $file->createDir($config['fileDir'].'/data/'.$_POST['dirName'].'/', $_POST['id']);
				try{
					foreach ($_FILES as $key => $f){
						if(strlen($_FILES[$key]['name']) > 4){
						$file->upload($url , $_FILES[$key], false, "images");
						resizeImageFile($url.$file->getFileName());
						}
					}
					echo gallery($config, $_POST['id'], $_POST['dirName']);
					exit;	
				}catch( FileException $e ){
					$data["msg"] = $e->getMessage();
					break;
				}
			}else{
				$data["msg"] = "Vyberte aspoň jeden obrázok.";	
			}
		break;

	}
	echo json_encode($data);
	exit();
}catch(MysqlException $ex){
	echo json_encode($data);
}
?>
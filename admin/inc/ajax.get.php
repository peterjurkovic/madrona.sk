<?php
	session_start();
	if(!isset($_GET['act'])){
		die('ERROR.');	
	}
	function __autoload($class){
		include_once "./class.".$class.".php";
	}
	
	function updateConfig($arr){
		global $conn;
		foreach($arr as $key => $val){
			if(strpos($key, "c_") !== false){
				if(strlen($val) != 0){
					$val = substr($val, 0, 254);
				}
				$conn->update("UPDATE `config` SET `val`=? WHERE `key`=? LIMIT 1", array( $val , $key));
			}
		}
	}
	require_once "../config.php";
	include "./fnc.article.php";
	include "./fnc.user.php";
	include "./fnc.main.php";
	ini_set("display_errors", 1);
	ini_set('log_errors', 1);
	ini_set('error_log', $config['root_dir'].'/logs/php_errors.txt');
	try{
		$conn = Database::getInstance($config['db_server'], $config['db_user'], $config['db_pass'], $config['db_name']);
	
		$data = array( "err" => 1, "msg" => "Operáciu sa nepodarilo vykonať, skúste to znova." );
		
		$auth = new Authenticate($conn);
		if(!$auth->isLogined()){  
			$data["msg"] = "Pre dlhú nečinnosť ste boli odhlásený.";
			echo $_GET["cb"] . "(" .json_encode( $data ) . ")"; 
			exit();
		} 
	
		if(isset($_GET['id'])) $_GET['id'] = (int)$_GET['id'];
	
	switch((int)$_GET['act']){
		
		
		// AKTIVNOST -------------------------------------
		case 1 :
				switch($_GET['table']){
					case "user" :
						$type = getUserById($conn, intval($_SESSION['id']), "id_user_type");
						if($type["id_user_type"] <= 2){
							$data["msg"] = "Užívateľia typu <strong>Editor</strong> nemajú právo meniť aktivitu uživateľov.";
							break;
						}
						if($conn->update("UPDATE `user` SET `active`=?, `edit`=".time()." WHERE `id_user`=".$_GET['id']." LIMIT 1", array( $_GET['status']))){
							$data = array( "err" => 0, "msg" => "Aktivita bola úspešne zmenená.");
					}	
					break;
					case "shop_variant" :
					case "shop_delivery" :
					case "shop_payment" :
						if($conn->update("UPDATE `".$_GET['table']."` SET `active`=? WHERE `id_".$_GET['table']."`=".$_GET['id']." LIMIT 1", array( $_GET['status']) )){
							$data = array( "err" => 0, "msg" => "Aktivita bola úspešne zmenená.");
						}	
						
					break;
					default:
					if($conn->update("UPDATE `".$_GET['table']."` SET `active`=?, `edit`=".time().", `editor`=? WHERE `id_".$_GET['table']."`=".$_GET['id']." LIMIT 1", array( $_GET['status'], (int)$_SESSION['id']) )){
						$data = array( "err" => 0, "msg" => "Aktivita bola úspešne zmenená.");
					}			
				}		
		break;
			
			
		// PORADIE  -------------------------------------		
		case 2 :
				$arr = explode( "id" , $_GET['orderStr']);		
				if(count($arr) ==0 ){
					break;
				}
				for($i = 1; $i < count($arr); $i++){
					$split = explode("-", $arr[$i]);
					if(count($split) != 2){
						break;
					}
					if($conn->simpleQuery("UPDATE `".$_GET['table']."` SET `order`=".intval($split[1]). ", `edit`=".time().", `editor`=".intval($_SESSION['id'])." WHERE `id_".$_GET['table']."`=".$split[0]. " LIMIT 1")){
						$data = array( "err" => 0, "msg" => "Poradie bolo úspešne zmenené.");
					}
				}
		break;
		
		
		// MAZANIE  -------------------------------------		
		case 3 :	
				switch($_GET["table"]){
					case "article":
						if(count($conn->select("SELECT `id_article` FROM `article` WHERE `sub_id`=? LIMIT 1", array($_GET['id']))) == 0){
							$art = $conn->select("SELECT `avatar1`,`avatar2`,`avatar3` FROM `article` WHERE `id_article`=? LIMIT 1", array($_GET['id']));
							if(count($art) == 1){
								$conn->delete("DELETE FROM `article` WHERE `id_article`=? LIMIT 1", array($_GET['id']));
								$file = new File();
								$file->deleteDir("../../data/gallery/".$_GET['id']."/");
								if(isset($art[0]["avatar1"]) && $art[0]["avatar1"] !="") $file->deleteFile("../../data/avatars/".$art[0]["avatar1"]);
								if(isset($art[0]["avatar2"]) && $art[0]["avatar2"] !="") $file->deleteFile("../../data/avatars/".$art[0]["avatar2"]);
								if(isset($art[0]["avatar3"]) && $art[0]["avatar3"] !="") $file->deleteFile("../../data/avatars/".$art[0]["avatar3"]);
								$data = array( "err" => 0, "msg" => "Položka bola úspešne odstránená." );	
							}
						}else{
							$data = array( "err" => 1, "msg" => "Stránku nemožno odstrániť, obsahuje podstránky." );
							break;	
						}
					break;
					case "user" :
						$type = getUserById($conn, intval($_SESSION['id']), "id_user_type");
						if($type["id_user_type"] <= 2){
							$data["msg"] = "Užívateľ typu <strong>Editor</strong>, nemá právo mazať uživateľov.";
							break;
						}
						$conn->delete("DELETE FROM `user` WHERE `id_user`=? LIMIT 1", array($_GET['id']));
						$data = array( "err" => 0, "msg" => "Položka bola úspešne odstránená." );	
					break;
					case "shop_order" :
						$conn->delete("DELETE FROM `shop_item` WHERE `id_shop_order`=?", array($_GET['id']));
						$conn->delete("DELETE FROM `shop_order` WHERE `id_shop_order`=? LIMIT 1", array($_GET['id']));
						$data = array( "err" => 0, "msg" => "Položka bola úspešne odstránená." );	
					break;
					case "shop_payment" :
						if(isUsed("shop_order", "id_shop_payment", $_GET['id'])){
							$data['msg'] = "Položku nie je možné zmazať, pretože je použítá v objednávke.";
							break;	
						}
						$conn->delete("DELETE FROM `shop_payment` WHERE `id_shop_payment`=? LIMIT 1", array($_GET['id']));
						$data = array( "err" => 0, "msg" => "Položka bola úspešne odstránená." );	
					break;
					
					case "shop_delivery" :
						if(isUsed("shop_order", "id_shop_delivery", $_GET['id'])){
							$data['msg'] = "Položku nie je možné zmazať, pretože je použítá v objednávke.";
							break;	
						}
						$conn->delete("DELETE FROM `shop_delivery` WHERE `id_shop_delivery`=? LIMIT 1", array($_GET['id']));
						$data = array( "err" => 0, "msg" => "Položka bola úspešne odstránená." );	
					break;
					
					case "shop_product" :
						if(isUsed("shop_item", "id_shop_product", $_GET['id'])){
							$data['msg'] = "Položku nie je možné zmazať, pretože je použítá v objednávke.";
							break;	
						}
						$art = $conn->select("SELECT `avatar1`,`avatar2`,`avatar3` FROM `shop_product` WHERE `id_shop_product`=? LIMIT 1", array( $_GET['id'] ));
						$file = new File();
						$file->deleteDir("../../data/shop/".$_GET['id']."/");
						if(isset($art[0]["avatar1"]) && $art[0]["avatar1"] !="") $file->deleteFile("../../data/avatars/".$art[0]["avatar1"]);
						if(isset($art[0]["avatar2"]) && $art[0]["avatar2"] !="") $file->deleteFile("../../data/avatars/".$art[0]["avatar2"]);
						if(isset($art[0]["avatar3"]) && $art[0]["avatar3"] !="") $file->deleteFile("../../data/avatars/".$art[0]["avatar3"]);
						$conn->delete("DELETE FROM `shop_variant` WHERE `id_shop_product`=".$_GET['id']);
						$conn->delete("DELETE FROM `shop_attr` WHERE `id_shop_product`=".$_GET['id']);
						$conn->delete("DELETE FROM `shop_product` WHERE `id_shop_product`=".$_GET['id']." LIMIT 1");
						$data = array( "err" => 0, "msg" => "Položka bola úspešne odstránená." );	
					break;
					
					case "shop_manufacturer" :
						if(isUsed("shop_product", "id_shop_manufacturer", $_GET['id'])){
							$data['msg'] = "Položku nie je možné zmazať, pretože je použítá v produktoch.";
							break;	
						}
						$conn->delete("DELETE FROM  `".$_GET['table']."` WHERE `id_".$_GET['table']."`=? LIMIT 1", array($_GET['id']));
						$data = array( "err" => 0, "msg" => "Položka bola úspešne odstránená." );	
					break;
					
					case "shop_product_status" :
						if(isUsed("shop_product", "id_shop_product_status", $_GET['id'])){
							$data['msg'] = "Položku nie je možné zmazať, pretože je použítá v produktoch.";
							break;	
						}
						$conn->delete("DELETE FROM  `".$_GET['table']."` WHERE `id_".$_GET['table']."`=? LIMIT 1", array($_GET['id']));
						$data = array( "err" => 0, "msg" => "Položka bola úspešne odstránená." );	
					break;
					case "shop_product_avaibility" :
						if(isUsed("shop_product", "id_shop_product_avaibility", $_GET['id'])){
							$data['msg'] = "Položku nie je možné zmazať, pretože je použítá v produktoch.";
							break;	
						}
						$conn->delete("DELETE FROM  `".$_GET['table']."` WHERE `id_".$_GET['table']."`=? LIMIT 1", array($_GET['id']));
						$data = array( "err" => 0, "msg" => "Položka bola úspešne odstránená." );	
					break;
					
					case "shop_category" :
						if(count($conn->select("SELECT `id_shop_category` FROM `shop_category` WHERE `sub_id`=? LIMIT 1", array($_GET['id']))) == 0){
							if(isUsed("shop_product", "id_shop_category", $_GET['id'])){
								$data['msg'] = "Kategóriu nie je možné zmazať, pretože je použítá v produktoch.";
								break;	
							}
							$conn->delete("DELETE FROM  `".$_GET['table']."` WHERE `id_".$_GET['table']."`=? LIMIT 1", array($_GET['id']));
							$data = array( "err" => 0, "msg" => "Položka bola úspešne odstránená." );
						}else{
							$data['msg'] = "Kategóriu nie je možné zmazať, pretože <strong>obsahuje podkategórie.</strong>";
						}
					break;
					
					case "shop_variant" :
						if(isUsed("shop_item", "id_shop_variant", $_GET['id'])){
							$data['msg'] = "Variantu nie je možné zmazať, pretože je použítá v produktoch.";
							break;	
						}
						$conn->delete("DELETE FROM  `".$_GET['table']."` WHERE `id_".$_GET['table']."`=? LIMIT 1", array($_GET['id']));
						$data = array( "err" => 0, "msg" => "Položka bola úspešne odstránená." );	
					break;
					
					default :
						$conn->delete("DELETE FROM  `".$_GET['table']."` WHERE `id_".$_GET['table']."`=? LIMIT 1", array($_GET['id']));
						$data = array( "err" => 0, "msg" => "Položka bola úspešne odstránená." );	
								
				}
				
						
		break;
		
		// PRIDANIE artiklu  -------------------------------------		
		case 4 :	
				$sub_id = $_GET['id'];
				
				$a = $conn->simpleQuery("SELECT `id_article` FROM `article` WHERE  `sub_id`=".$sub_id." AND `link_sk`='".SEOlink($_GET["title_sk"])."' LIMIT 1");
				
				if(count($a) == 1 ){
					$data = array( "err" => 1, "msg" => "V kategórii sa už stránka s názvom: <strong>".$_GET["title_sk"]."</strong> nachádza." );
					break;
				}

				$order = $conn->simpleQuery("SELECT MAX(`order`), count(*) FROM `article` WHERE `sub_id`=".$sub_id);
				
				$arr = array( $sub_id, ($order[0]["MAX(`order`)"]+1), $_SESSION['id'], time(), $_GET['title_sk'], SEOlink($_GET['title_sk']));
				$conn->insert("INSERT INTO `article` (`sub_id`, `order`, `id_user`, `create`, `title_sk`, `link_sk`) VALUES (?,?,?,?,?,?)", $arr  );
				
				$count = count($conn->simpleQuery("SELECT `id_article` FROM `article` WHERE sub_id=".$sub_id));
				
				if($order[0]["count(*)"] < $config['adminPagi']){
					$config['offset'] =  0 ;
					$s = 0;
				}else{
					$s = floor($order[0]["count(*)"] / $config['adminPagi']);
					$config['offset'] = $s  * $config['adminPagi'] ;
				}
						
				$nav = new Navigator($count, $s+1 , '/index.php?p=article',  $config["adminPagi"]);
				$nav->setSeparator("&amp;s=");
				$data['pagi'] = $nav->simpleNumNavigator();
				$data['html'] = printArticles($conn, $sub_id, $config);
				$data['err'] = 0;
				$data['msg'] = "Stranka bola úspešne pridana.";						
		break;
		
		// AUTOCOMPLETE  -------------------------------------		
		case 5 :	
				if(isset($_GET["term"]))
					{
						$cols = explode("-", $_GET['table']);
						if(count($cols) != 2){
							break;
						}
						$id = "id_".$cols[0];
						$val = $cols[1];
						$data = $conn->select("SELECT `".$val."` FROM `".$cols[0]."` WHERE `".$val."` REGEXP ? GROUP BY `".$val."` LIMIT 8", array( $_GET["term"] ));
						
						for($i=0; $i < count($data); $i++){
							$result[$i] = array($id => null, $val  => $data[$i][$val ]); 
						}
						
						echo  $_GET["cb"] . "(" . json_encode($result) . ")";  
						exit;  
					}
						
		break;
		
		// SEARCH  -------------------------------------		
		case 6 :
				switch($_GET['table']){
					case "article" :
						$data['html'] = printArticles($conn, null, $config,  $_GET['q']);
					break;
					case "user" :
						$data['html'] = printUsers($conn, $config, $_GET['q']);
					break;
					case "user_log" :
						$data['html'] = printLogs($conn, $config, $_GET['q']);
					break;
					
				}	
				$data['err'] = 0;
		break;
		
		
		// KEYWORDS  -------------------------------------		
		case 9 :
				if($conn->update("UPDATE `article` SET `keywords`=?, `edit`=".time().", `editor`=? WHERE `id_article`=? LIMIT 1", array($_GET['keywords'],(int)$_SESSION['id'], $_GET['id']  )))
				{
					$data = array( "err" => 0, "msg" => "Úspešne uložené." );		
				}
		break;
		
		// AVATAR IMAGES  -------------------------------------		
		case 11 :
				 echo '<a href="/data/avatars/'.$_GET['img'].'" title="Zobraziť obrázok" class="show hidden"></a>'.
                      '<a href="#aid'.$_GET['aid'].'" title="article-'.$_GET['eid'].'-'.$_GET['img'].'" class="del hidden"></a>'. 
					  '<img src="./inc/img.php?url=/data/avatars/'.$_GET['img'].'&amp;w=100&amp;h=100&amp;type=crop"  class="img" alt="" />';
				exit;
		break;
		
		// DELETING AVATAR IMAGES  -------------------------------------		
		case 12 :
				if(count($_GET['info']) == 3){
					$file = new File();
					if( $file->deleteFile("../../data/avatars/".$_GET['info'][2])){
						$conn->delete("UPDATE `".$_GET['info'][0]."` SET `".$_GET['info'][1]."`=?, `edit`=".time().", `editor`=? WHERE `id_".$_GET['info'][0]."`=? LIMIT 1", array( "", (int)$_SESSION['id'], $_GET['id'] ));
						$data = array( "err" => 0, "msg" => "Odstrácené" );
					}
				}

		break;
		
		// DELETING GALLERY IMAGES  -------------------------------------		
		case 14 :
				if(isset($_GET['url'])){
					$file = new File();
					if($file->deleteFile($_GET['url'])){
						$data = array( "err" => 0, "msg" => "Fotka bola  úspešne odstránená.");
						break;
					}
				}
				$data["msg"] = "Nastala chyba, fotografiu sa nepodarilo ostráníť.";
		break;
		
		
		// SAVE EDITED USER  -------------------------------------		
		case 15 :
				if (!filter_var($_GET['email'], FILTER_VALIDATE_EMAIL)) {
					$data["msg"] = "Neplatná e-mailová adresa.";
					break;
				}elseif(!checkUserRights($conn, intval($_SESSION['id']), $_GET['id_user_type'], $_GET['id'])){
					$data["msg"] = "Nemáte oprávnenie pridelovať vyžšie práva, než sú vaše.";
					break;
				}elseif(checkUserEmail($conn, $_GET['email'], $_GET['id'])){
					$data["msg"] = "E-mail ".$_GET['email']." sa už v databáze nachádza.";
					break;
				}
				$arr = array( $_GET['id_user_type'], $_GET['active'] ,$_GET['email'] ,$_GET['givenname'] ,$_GET['surname'] , time() , $_GET['id'] );
				$conn->update("UPDATE `user` SET `id_user_type`=?, `active`=?, `email`=?, `givenname`=?, `surname`=?, `edit`=? WHERE `id_user`=? LIMIT 1", $arr );
				$data = array( "err" => 0, "msg" => "Úprava úžívateľa prebehla úspešne." );
		break;
		
		
		// CHANGE USER PASS  -------------------------------------		
		case 16 :
				if(strlen($_GET['oldpass']) < 5 || strlen(trim($_GET['newpass1'])) < 5 || strlen($_GET['newpass2']) < 5){
					$data["msg"] = "Heslo musí masť minimálne 5 znakov.";
					break;
				}else if($_GET['newpass1'] != $_GET['newpass1']){
					$data["msg"] = "Nové heslá sa nezhodujú.";
					break;
				}else if($_SESSION['login'] = "demo"){
					$data["msg"] = "Heslo v demo účte nie je možné meniť.";
					break;
				}else if($_SESSION['id'] != $_GET['id'] && $_SESSION['type'] <= 2){
					$data["msg"] = "Užívateľ typu editor, nemôže meniť heslo ostatným užívateľom.";
					break;
				}else{
					$result = $conn->select("SELECT `salt` FROM `user` WHERE `id_user`=? LIMIT 1", array($_GET['id']));
					$salt = $result[0]['salt'];
					if( count($conn->select("SELECT `id_user` FROM `user` WHERE `pass`=? AND `id_user`=? ", array( hash_hmac('sha256', $_GET['oldpass'], $salt), $_GET['id']))) != 1){
						$data["msg"] = "Chybne zadané súčastné heslo.";
						break;
					}
				}
				$conn->update("UPDATE `user` SET `pass`=?, `edit`=? WHERE `id_user`=? LIMIT 1", array( hash_hmac('sha256', $_GET['newpass1'], $salt), time(), $_GET['id'] ) );
				$data = array( "err" => 0, "msg" => "Heslo bolo úspešne zmenené." );
		break;
		
		
		// NEW USER  -------------------------------------		
		case 17 :
				if(strlen($_GET['pass1']) < 5 || strlen(trim($_GET['pass2'])) < 5){
					$data["msg"] = "Heslo musí masť minimálne 5 znakov.";
					break;
				}elseif($_GET['pass1'] != $_GET['pass2']){
					$data["msg"] = "Heslá sa nezhodujú.";
					break;
				}elseif (!filter_var($_GET['email'], FILTER_VALIDATE_EMAIL)) {
					$data["msg"] = "Neplatná e-mailová adresa.";
					break;
				}elseif(checkUserEmail($conn, $_GET['email'])){
					$data["msg"] = "E-mail ".$_GET['email']." sa už v databáze nachádza.";
					break;
				}elseif(!preg_match("/^\w*$/", $_GET['login'])){
					$data["msg"] = "Login obsahuje nepovolené znaky.";
					break;
				}elseif(!checkUserRights($conn, intval($_SESSION['id']), $_GET['id_user_type'])){
					$data["msg"] = "Nemáte oprávnenie pridelovať vyžšie práva, než sú vaše.";
					break;
				}elseif(loginExists($conn, $_GET['login'])){
					$data["msg"] = "Uívateľ s loginom: <strong>".$_GET['login']. "</strong> sa už v databáze nachádza.";
					break;
				}elseif($_SESSION['type'] <= 2){
					$data["msg"] = "Uživatelia typu <strong>Editor</strong> nemajú právo pridávať užívateľov.";
					break;
				}
				$salt = createSalt();
				$conn->insert("INSERT INTO `user` (`id_user_type`, `login`, `pass`, `salt`, `active`, `blocked`, `reg_time`, `email`, `givenname`, `surname`) VALUES (?,?,?,?,?,?,?,?,?,?)", 
						array( 	$_GET['id_user_type'], 
								$_GET['login'], 
								hash_hmac('sha256', $_GET['pass1'], $salt), 
								$salt, 
								$_GET['active'], 
								0,
								time(), 
								$_GET['email'], 
								$_GET['givenname'],
								$_GET['surname']) 
						);
				$data = array( "err" => 0, "msg" => "Užívateľ (<strong>".$_GET['login']."</strong>) bol úspešne vytvorený." );
		break;
		
		// SETTINGS - seo  -------------------------------------		
		case 18 :
				$type = getUserById($conn, intval($_SESSION['id']), "id_user_type");
				if($type["id_user_type"] <= 2){
					$data["msg"] = "Nemáte právo editovať nastavania.";
					break;
				}
				updateConfig($_GET);
				$data["err"] = 0;
				$data["msg"] = "Zmeny boli úspešne uložené.";
		break;
		
		// SETTINGS - sys  -------------------------------------		
		case 19 :
				$type = getUserById($conn, intval($_SESSION['id']), "id_user_type");
				if($type["id_user_type"] <= 2){
					$data["msg"] = "Nemáte právo editovať nastavania.";
					break;
				}elseif(!isInt($_GET['c_pagi']) || !isInt($_GET['c_pagi_g'])){
					$data["msg"] = "Neplatné hodnoty pre stránkovanie.";
					break;
				}elseif (!isMail($_GET['c_email'])){
					$data["msg"] = "Neplatná e-mailová adresa.";
					break;
				}
				updateConfig($_GET);
				$data["err"] = 0;
				$data["msg"] = "Zmeny boli úspešne uložené.";
		break;
		
		// SETTINGS - state  -------------------------------------		
		case 20 :
				$type = getUserById($conn, intval($_SESSION['id']), "id_user_type");
				if($type["id_user_type"] <= 3){
					$data["msg"] = "Nemáte právo editovať nastavania.";
					break;
				}
				updateConfig($_GET);
				$data["err"] = 0;
				$data["msg"] = "Zmeny boli úspešne uložené.";
		break;
		
		// VALIDATE UNIQUE   -------------------------------------		
		case 21 :
				if($_GET['id'] == -1 || $_GET['id'] == 0){
					$r =  $conn->select("SELECT count(*) FROM `".$_GET['table']."` WHERE `".$_GET['coll']."`=? LIMIT 1", array( $_GET["val"] ));
				}else{
					$r =  $conn->select("SELECT count(*) FROM `".$_GET['table']."` WHERE `".$_GET['coll']."`=? AND `id_".$_GET['table']."`<>? LIMIT 1", array( $_GET["val"], $_GET['id'] ));
				}
				$a["count"] = $r[0]["count(*)"];
				echo json_encode($a);
				exit;
		default :
	}
	}catch(MysqlException $ex){}
	
	echo $_GET["cb"] . "(" .json_encode( $data ) . ")";
	
	exit();
?>
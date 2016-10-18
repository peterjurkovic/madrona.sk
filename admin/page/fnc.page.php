<?php 

// ----------------------------------------------------------
function article_breadcrumb($id, $char, $arr = array() ){
	
}

// ----------------------------------------------------------
function test404($page){
	if($page == null){ 
		header("Location: /error/page.php?p=404");
		exit;
	}
}

function MAIN(){
	global $conn, $lang, $config;
	$page = array( array() );	
	
	$first = $conn->select("SELECT `id_article` FROM `article` WHERE `sub_id`=0 AND `active`=1 ORDER BY `order` LIMIT 1");
	
	$conf = getConfig($conn, "config", "full");
	/*
	if(count($first) != 1 && $_GET['p'] != $config['shop_prefix']){
		return $conf;
	}
	*/
	$conf["fid"] = $first[0]["id_article"];

	if($_GET['p'] != $config['shop_prefix'])
	{
		$q = "SELECT `id_article`, `sub_id`, `type`, `title_${lang}`, `subtitle_${lang}`, `header_${lang}` FROM `article` ";
		if($_GET['p'] == "home" || $_GET['p'] == "search")
		{
			
			$page = $conn->select("$q WHERE `id_article`=? AND `active`=1 LIMIT 1", array( $conf["fid"] ));
			$page[0]['title_sk'] = $conf['c_title'];
			//$page[0]['id_article'] = 0;
			$page[0]['id_shop_category'] = 0;
			$page[0]['sub_id'] = 0;
			$page[0]['type'] = 1;
			$page[0]['parentID'] = 0;
			$page[0]['parentSubID'] = 0; 
			$page[0]["header_${lang}"] = $conf["c_descr"];
		}else{

			$q = "SELECT `id_article`, `sub_id`, `type`, `title_${lang}`, `subtitle_${lang}`, `header_${lang}` FROM `article` ";
			if($_GET['a'] == "index")
			{
				$page = $conn->select("$q WHERE `link_${lang}`=? AND `active`=1 LIMIT 1", array( $_GET['p'] ));		
			}else{
				$page = $conn->select("$q WHERE `id_article`=? AND `active`=1 LIMIT 1", array( $_GET['a'] ));
			}
			test404($page);	
		}
		
	}else{ // shop index
			$q = "SELECT `id_shop_category`, `sub_id`, `category_name`, `link_sk`, `label` FROM `shop_category` WHERE";
		if($_GET['cn'] == "home" || $_GET['cn'] == "search" || $_GET['cn'] == "kosik" || $_GET['cn'] == "kosik2"){
			$page[0]['id_shop_category'] = 0;
			$page[0]['sub_id'] = 0;
			$page[0]['parentID'] = 0;
			$page[0]['parentSubID'] = 0;
			$page[0]["header_sk"] = $conf["s_descr"]; 
			$page[0]["title_sk"] = $conf["s_title"];
		}elseif($_GET['cn'] == "pview"){
			// PRODUCT ---
			$page = getProduct("full", $_GET['pid']);
			test404($page);
			$p = parentMETA($page[0]["id_shop_category"]);
			$page[0]['parentID'] = $p["id_shop_category"];
			$page[0]['parentSubID'] = $p["sub_id"];
			$page[0]["header_sk"] = substr($page[0]["header_sk"], 0, 200); 
			$page[0]["title_sk"] = (isset($page[0]["shop_manufacturer_name"]) ? $page[0]["shop_manufacturer_name"]." " :  "").$page[0]["title_sk"];
		}elseif($_GET['cid'] == 0){
			// main category ---
			$page = $conn->select("$q `link_sk`=? AND `active`=1 LIMIT 1", array( $_GET['cn'] ));
			test404($page);
			$page[0]['parentID'] = $page[0]["id_shop_category"];
			$page[0]['parentSubID'] = $page[0]["sub_id"];
			$page[0]["header_sk"] = substr($page[0]["label"], 0, 200); 
			$page[0]["title_sk"] = $page[0]["category_name"];
		}else{
			// SUB CATEG ---
			$page = $conn->select("$q `id_shop_category`=? AND `active`=1 LIMIT 1", array( $_GET['cid'] ));
			test404($page);
			$p = parentMETA($page[0]["sub_id"]);
			$page[0]['parentID'] = $p["id_shop_category"];
			$page[0]['parentSubID'] = $p["sub_id"];
			$page[0]["header_sk"] = substr($p["label"], 0, 200); 
			$page[0]["title_sk"] = $p["category_name"]." - ". $page[0]["category_name"];
		}
	}	
	$conf = array_map("clean", $conf);
	$page[0] = cleanArticle($page[0]);
	return  array_merge($page[0], $conf);
	
}

// ----------------------------------------------------------

function getArticle($type = "basic", $id = null, $lang = "sk"){
	global $conn;
	
	switch($type){
		case "full" :
			$article = $conn->select("SELECT `id_article`, `sub_id`, `type`, `avatar1`, `avatar2`, `avatar3`, `edit`, `create`, `hits`, `title_$lang`, `subtitle_$lang`, `header_$lang`, `content_$lang`, `link_$lang` FROM  `article` WHERE `id_article`=?  LIMIT 1", array( $id ));
			$article[0] = cleanArticle($article[0]);
			break;
	
		case "basic" :
			$article = $conn->select("SELECT `id_article`, `type`, `avatar1`, `title_$lang`, `header_$lang`, `link_$lang` FROM `article` WHERE `id_article`=?  LIMIT 1", array( $id ));
			$article[0] = cleanArticle($article[0]);
			break;
		
		case "categ" :
			$article = $conn->select("SELECT `id_article`, `sub_id`, `type`, `title_$lang`,`header_$lang`, `link_$lang`, `avatar1` FROM `article` WHERE `sub_id`=? AND `active`=1 ORDER BY `order`", array( $id ));
			break;
			
		case "link" :
			$article = $conn->select("SELECT `id_article`, `sub_id`, `type`, `title_$lang`, `link_$lang` FROM  `article` WHERE `id_article`=? LIMIT 1", array( $id ));
			$article[0] = cleanArticle($article[0]);
			break;	
	}
	
	return $article;
}

// ----------------------------------------------------------

	function printMenu($subID, $class, $currentID = -1, $showSub = false){
		global $conn, $lang;
		$html = "";
		
		$categ = getArticle("categ", $subID, $lang);
		for($i =0; $i < count($categ); $i++ ){
			if(strlen($categ[$i]["title_${lang}"]) == 0) continue;
			$renderSubnav = false;
		
			if($showSub && count($conn->simpleQuery("SELECT 1, `id_article` FROM `article` WHERE `active`=1 AND `sub_id`=".$categ[$i]["id_article"]." LIMIT 1")) > 0 ){
				$renderSubnav = true;
			}

			$html .= '<li '.($renderSubnav ? 'class="dropdown"' : '').'><a href="'.linker($categ[$i]["id_article"], $categ[$i]["type"], $lang).'" '.($categ[$i]["id_article"] == $currentID ? ' class="active"' : '').'>'.$categ[$i]["title_${lang}"].'</a>';
			if($renderSubnav){
				$html .= '<ul class="dropdown-menu">'.printMenu($categ[$i]["id_article"], $class."-child",  $currentID).'</ul>';
			}
			$html .= '</li>';		
		}
		return $html;
	}

// ----------------------------------------------------------


function linker($aid, $type, $lang = "sk"){
	global  $meta, $conn;
	
	if($meta['fid'] == $aid && $lang == "sk"){ return "/"; }
	
	/*if($aid == 0 && $lang != "sk") {
		return "/".$lang;
	}elseif($aid == 0 && $lang == "sk"){
		return "/";	
	}
	

	if($type == 2){
		$ids = $conn->select("SELECT `id_article` FROM `article` WHERE `sub_id`=? LIMIT 1" , array( $aid ));
		if(count($ids) !=0){
			$aid = $ids[0]["id_article"];
		}
	}
	*/
	$article =  getArticle("link", $aid, $lang);
	return href($article, $lang);
}

function href($article , $lang){
	if($article[0]["sub_id"] == 0){ 
	   return  "/".$article[0]["link_${lang}"]; 
	}else{
	   return  "/".parentPage($article[0]["sub_id"], $lang)."/".$article[0]["id_article"]."/".$article[0]["link_${lang}"]; 
	}
}

// ---------------------------------------------------------- 


function parentPage($sub_id, $lang){
	global $conn;
	$article = getArticle("link", $sub_id); 
	if($article[0]["sub_id"] == 0){
		return ($article[0]["link_${lang}"]);
	}else{
		return(parentPage($article[0]['sub_id'], $lang)); 
	}
}

function parentMETA($subID){
	global $conn;
	$cat = getCateg($type = "link", $subID);
	if($cat[0]["sub_id"] == 0){
		return $cat[0];
	}else{
		return(parentMETA($cat[0]['sub_id'])); 
	}
}

function isPositiveInt($n, $min = 0, $max = 2){
	return (preg_match ("/^[0-9]{".$min.",".$max."}$/" ,$n) == 1);
}


function isEmail($email){
	return (preg_match ("/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,3})$/i" ,$email) == 1);
}


function getLangText($lang){
	global $conn; 
	$texts = array();
	$arr = $conn->select("SELECT * FROM `lang_$lang`");
	for($i = 0; $i < count($arr); $i++){
		$texts[$arr[$i]["key"]] = $arr[$i]["val"];	
	}
	return $texts;
}

function printPageImages($array, $id, $size, $title = ""){
			$html = "";
			foreach($array as $name){
				$html .='<a data-lightbox="roadtrip" href="/data/gallery/'.$id.'/'.$name.'" class="thumb" >'.
						'<img src="/i/'.$size.'-'.$size.'-crop/gallery/'.$id.'/'.$name.'" alt="'.$title.'" /></a>';
			}
			return $html;
	}



function bc($id, $char){
	global $conn, $lang, $meta;
	$article =  getArticle("link", $id, $lang);
	if($article == null){
		return;
	}
	if($meta['id_article'] == $article[0]['id_article']){
		$html = '<li class="active">'.$article[0]["title_$lang"].'</li>';	
	}else{
		$html = '<li><a href="'.href($article, $lang).'" >'.$article[0]["title_$lang"].'</a></li>';
	}
	
	
	if($article[0]['sub_id'] != 0){
		 return bc($article[0]['sub_id'], $char).' '.$html;
	}
	return $html;
}
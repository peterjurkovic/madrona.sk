<?php
function nav($id){
	global  $conn, $config;
	$html = "";
	$arr = getCateg( "categ", 0);		  
		for($i = 0; $i < count($arr); $i++){
			$html .= '<li'.
			($id == $arr[$i]['id_shop_category'] ? ' curr' : '').'><a href="/'.$config['shop_prefix'].'/'.$arr[$i]['link_sk'].'"  title="'.$arr[$i]['category_name'].'" >'.$arr[$i]['category_name'].'</a>';	
		}
	return $html;
}



function subNav($subId, $class = ""){
	global  $conn, $meta;
	$html = "";
	$arr = getCateg("categ", $subId);			  
	for($i = 0; $i < count($arr); $i++){
		$html .= '<li><a href="'.shopLinker($arr[$i]['id_shop_category']).'" class="'.$class.' '.
				 ($meta["id_shop_category"] == $arr[$i]['id_shop_category'] ? " cr" : '').'">'.$arr[$i]['category_name'].'</a></li>';	
	}
	return $html;
}



function getProduct($type = "basic", $id = null){
	global $conn;
	
	switch($type){
		case "link" :
			return $conn->select("SELECT `id_shop_product_status`, `id_shop_category`, `title_sk`, `header_sk` FROM  `shop_product` WHERE `id_shop_product`=? AND `active`=1 LIMIT 1", array( $id ));
		case "basic" :
			return $conn->select("SELECT `id_shop_product_status`, `id_shop_category`, `title_sk`, `header_sk`, `price`, `avatar1` FROM `shop_product` WHERE `id_shop_product`=? AND active=1", array( $id ));
		case "categ" :
			return $conn->select("SELECT `id_shop_product`,`id_shop_product_status`, `id_shop_category`, `title_sk`, `price`, `price_sale`, `avatar1` 
								  FROM `shop_product` 
								  WHERE `id_shop_category`=? AND active=1".($home ? " AND `home`=1" : ""), array( $id ));
		case "full" :
		default: 
			$p = $conn->select("SELECT * FROM  `shop_product` WHERE `id_shop_product`=? AND `active`=1 LIMIT 1", array( $id ));
			if(!$p) break; 
			$p[0]['shop_manufacturer_name'] = getManufacturerById( $p[0]['id_shop_manufacturer'] );
			return $p;		
	}
}

// ----------------------------------------------------------

function getCateg($type = "categ", $id = NULL){
	global $conn;
	
	switch($type){
		default:
		case "full" :
			return $conn->select("SELECT * FROM  `shop_category` WHERE `id_shop_category`=? AND `active`=1 LIMIT 1", array( $id ));	
		case "categ" :
			return $conn->select("SELECT `id_shop_category`,`category_name`, `link_sk` FROM `shop_category` WHERE `sub_id`=? AND active=1", array( $id ));		
		case "link" :
			return $conn->select("SELECT `id_shop_category`,`category_name`, `link_sk`, `sub_id`, `label` FROM  `shop_category` WHERE `id_shop_category`=? AND `active`=1 LIMIT 1", array( $id ));	
	}
}

// ----------------------------------------------------------


function shopLinker($cid){
	global  $meta, $conn,$config;
	
	$cat = getCateg($type = "link", $cid);	
	
	if($cat[0]["sub_id"] == 0){
	   return  "/".$config['shop_prefix']."/".$cat[0]["link_sk"]; 
	}else{
	   return  "/".$config['shop_prefix']."/".parentCat($cat[0]["sub_id"])."/".$cat[0]["id_shop_category"]."/".$cat[0]["link_sk"]; 
	}
	
}

// ----------------------------------------------------------

function parentCat($subID){
	global $conn;
	$cat = getCateg($type = "link", $subID);
	if($cat[0]["sub_id"] == 0){
		return ($cat[0]["link_sk"]);
	}else{
		return(parentCat($cat[0]['sub_id'])); 
	}
}

// ----------------------------------------------------------

function getManufacturerById($id){
	if(!isset($id) || $id == 1 || $id == 0) return;
	global $conn;
	$r = $conn->select("SELECT `shop_manufacturer_name` FROM `shop_manufacturer` WHERE `id_shop_manufacturer`=? LIMIT 1", array( $id ));
	return $r[0]['shop_manufacturer_name'];
}

// ----------------------------------------------------------

function printPrice($data){
	global $meta;
	if(($data['id_shop_product_status'] == 2 || $data['id_shop_product_status'] == 3) && $data['price_sale'] != 0){
		return number_format($data['price_sale'] *  ($meta['s_dph'] / 100 + 1), 2).' '.$meta['s_currency'];
	}else{
		return number_format($data['price'] *  ($meta['s_dph'] / 100 + 1), 2).' '.$meta['s_currency'];
	}
}

// ----------------------------------------------------------

function printVariants($pid, $dph, $normalPrice){
	global $conn;
	$html = "";
	$dph = ($dph / 100 + 1);
	$data = $conn->select("SELECT `home` FROM `shop_product` WHERE `id_shop_product`=? LIMIT 1", array( $pid ));
	if(!$data){ return; }
	if($data[0]['home'] != 1){
		
		$data = $conn->select("SELECT * FROM `shop_product_variant`");
		for($i=0; $i < count($data); $i++) {   
			$html .= "<option value=\"".$data[$i]["id_shop_product_variant"]."\">".htmlspecialchars($data[$i]["name"]).
					 ' - ('.number_format($normalPrice * $dph ,2)."&euro;)</option>\n";
		}
	}
	
	$data = $conn->select("SELECT * FROM `shop_variant` WHERE `id_shop_product`=? AND `active`=1", array( $pid ));
	for($i=0; $i < count($data); $i++) {   
		$html .= "<option value=\"".$data[$i]["id_shop_variant"]."\">".htmlspecialchars($data[$i]["shop_variant_name"]).
				'  - ('.number_format($data[$i]["price"] * $dph,2)."&euro;)</option>\n";
	}
	
	return $html; 
}

// ----------------------------------------------------------

function printStatus($id){
	if($id == 2){
		return '<img class="s" src="/img/s/2.png" alt="Akcia!" />'; // akcia
	}elseif($id == 3){
		return '<img class="s" src="/img/s/3.png" alt="Výpredaj!" />'; // vypredaj
	}elseif($id == 4){
		return '<img class="s" src="/img/s/1.png" alt="Novinka!" />'; // novinka
	}
}

function noDPH($price){
	global $meta;
	return round(($price / (100 + $meta['s_dph'])) * 100 , 2);
}

// ----------------------------------------------------------
function filter($filter){
		$where = "";
		$qs = array();
		if( isset($filter['priceFrom']) && $filter['priceFrom'] != 0) { 
			$where .= " AND p.`price` >=".noDPH($filter['priceFrom'])." "; 
			$qs[] = "priceFrom=".$filter['priceFrom'];
		}	
		if ( isset($filter['priceTo']) && $filter['priceTo'] != 300) {
			$where .= " AND p.`price` <=".noDPH($filter['priceTo'])." ";
			$qs[] = "priceTo=".$filter['priceTo'];
		}
		$_SERVER['QUERY_STRING'] .= implode("&amp", $qs);
		return $where;
}

function colorFilter(){
	$_SERVER['QUERY_STRING'] = '';
	if(!isset($_GET['c'])) return '';
	$a = explode(",", $_GET['c']);
	array_pop($a);
	$sql = '';
	if(count($a) > 0){
		$_SERVER['QUERY_STRING'] .= 'c='.$_GET['c'].'&amp;';
		for($i = 0 ; $i < count($a); $i++){
			$sql .= ' INNER JOIN `shop_product_color` c'.$i.' ON p.`id_shop_product`=c'.$i.'.`id_shop_product` AND c'.$i.'.`id_shop_color`='.$a[$i];
		}		
	}
	return $sql;
}

// ----------------------------------------------------------
function printPageProducts($type = "home", $limit = null, $small = false){
	global $conn, $meta, $config;
	$html = "";
	switch ($type ){
		case "normal" :
			if($small) $meta["s_shopPagi"] = 20;
			if(!isset($meta["id_shop_category"])) $meta["id_shop_category"] = 0;
			$ids = getChildersID($meta["id_shop_category"]);
			$where = colorFilter()." WHERE p.`active`=1 AND (p.`id_shop_category`=".implode(" OR p.`id_shop_category`=", $ids).") ".filter($_GET);
			
			$data = $conn->select("SELECT count(*) FROM `shop_product` p ".$where);
			$count = $data[0]["count(*)"];
			if($count == 0){
				return '<p class="alert">Požiadavke nevyhovuje žiadny záznam.</p>';
			}
			$offset = ($_GET['s'] == 1 ? 0 :  ($_GET['s'] * $meta["s_shopPagi"]) - $meta["s_shopPagi"]);
			$data = $conn->select("SELECT p.`id_shop_product`, p.`id_shop_product_status`, p.`id_shop_category`, p.`title_sk`, p.`price`, p.`price_sale`, p.`avatar1` , p.`header_sk` 
								   FROM `shop_product` p $where ORDER BY p.`id_shop_product` DESC LIMIT $offset, ".$meta["s_shopPagi"]);
								   
			if(strlen($_SERVER['QUERY_STRING']) > 0) $_SERVER['QUERY_STRING'] = "?".$_SERVER['QUERY_STRING'];
			 $nav = new Navigator($count, $_GET['s'], shopLinker($meta["id_shop_category"]) , $meta["s_shopPagi"], $_SERVER['QUERY_STRING']);

			 $nav->setLabelNext("ďalšie &raquo;");
			 $nav->setLabelPrev("&laquo; predošlé");
			 $nav = $nav->smartNavigator();
			 $html .= $nav;
		break;
		case "new":
			$data = $conn->select("SELECT `id_shop_product`,`id_shop_product_status`, `id_shop_category`, `title_sk`, `price`, `price_sale`, `avatar1`, `header_sk` 
							   FROM `shop_product` 
							   WHERE `id_shop_category`<>48 AND `id_shop_category`<>47 AND `id_shop_category`<>46 AND active=1 
							   ORDER BY `id_shop_product` DESC
							   LIMIT ".$limit);
		break;
		case "top":
			$data = $conn->select("SELECT `id_shop_product`,`id_shop_product_status`, `id_shop_category`, `title_sk`, `price`, `price_sale`, `avatar1`, `header_sk` 
							   FROM `shop_product` 
							   WHERE `id_shop_category`<>9 AND `id_shop_category`<>48 AND `id_shop_category`<>47 AND `id_shop_category`<>46 AND active=1
							   ORDER BY `hits` DESC
							   LIMIT ".$limit);
		break;
		default:
			$data = $conn->select("SELECT `id_shop_product`,`id_shop_product_status`, `id_shop_category`, `title_sk`, `price`, `price_sale`, `avatar1` , `header_sk` 
							   FROM `shop_product` 
							   WHERE `home`=1 AND active=1
							   LIMIT ".$meta["s_shopPagi"]);
	}
	if($small){
		$html .= '<table cellspacing="0" cellpadding="0" id="zakusky">';
		for($i = 0; $i < count($data); $i++){
		$html .='<tr>
			<td class="id c">ID '.$data[$i]['id_shop_product'].'</td>'.
			'<td class="photo c"><a href="/data/avatars/'.$data[$i]['avatar1'].'" class="thumb" title="'.$data[$i]['title_sk'].' | '.$data[$i]['price'].' 
			EUR / ks"><img src="/i/70-40-auto/avatars/'.$data[$i]['avatar1'].'" alt="'.$data[$i]['title_sk'].'" /></a></td>'.
			'<td class="title">'.$data[$i]['title_sk'].'</td>'.
			'<td class="we c">'.getAttrById($data[$i]['id_shop_product']).' g </td>'.
			'<td class="p c">'.printPrice($data[$i]).'</td>'.
			'<td class="input"><input type="text" value="1" name="count" maxlength="4" /><a href="#" class="addToBasket c" id="p'.$data[$i]['id_shop_product'].'" title="Vložiť do košíka ?">Do košíka</a></td>'.
		  '</tr>';     
		}
		$html .= '</table>';
	}else{
		for($i = 0; $i < count($data); $i++){
			$html .= '<div class="sitem"><div class="ihead"><a href="/'.$config['shop_prefix'].'/'.$data[$i]['id_shop_product'].'/'.SEOlink($data[$i]['title_sk']).'" ><strong>'.$data[$i]['title_sk'].'</strong></a>'.
				'<span>ID torty: '.$data[$i]['id_shop_product'].'</span></div>'.
				'<a href="/data/avatars/'.$data[$i]['avatar1'].'" class="thumb">'.'<img src="/i/110-95-auto/avatars/'.$data[$i]['avatar1'].'" alt="'.$data[$i]['title_sk'].'" /></a>'.
				'<p>'.crop($data[$i]['header_sk'], 145).'</p>'.
				'<div class="price"><em>od</em> '.printPrice($data[$i]).'</div><a href="#'.$data[$i]['id_shop_product'].'" class="buy">Kúpiť</a><div class="clearfix"></div></div>';
		}
	}
	if(isset($nav)) $html .= '<div class="clearfix"></div>'.$nav;
	return $html;
}

// ----------------------------------------------------------

function getChildersID($id){
	global $conn;
	$ids = array( $id );
	if(hasChild($id)){
		$ids = iterate($id, $ids);
	}
	return $ids;	
}

function iterate($id, $ids){
	global $conn;
	$data =  $conn->select("SELECT `id_shop_category`, `sub_id` FROM `shop_category` WHERE `sub_id`=? AND `active`=1", array( $id) );
	for($i =0; $i < count( $data ); $i++){
		$ids[] = $data[$i]['id_shop_category'];
		if(hasChild($data[$i]['id_shop_category'])){
			$ids = iterate( $data[$i]['id_shop_category'], $ids);
 		}
	}
	return $ids;	
}

function hasChild($id){
	global $conn;
	return (count($conn->select("SELECT `id_shop_category` FROM `shop_category` WHERE `sub_id`=".intval($id). " AND `active`=1 LIMIT 1")) == 1 ? true : false);
}

// ----------------------------------------------------------
// BACKET fncs
	function getDeliveryPrice($id){
		global $conn; 
		$data = $conn->select("SELECT `price` FROM `shop_delivery` WHERE `id_shop_delivery`=? LIMIT 1", array( $id ));
		return $data[0]['price'];
	}

	function getBacketItem($pid, $vid){
		global $conn;
		
		if($vid == 0 || $vid < 50){
			$data = $conn->select("SELECT `title_sk`, `avatar1` FROM `shop_product` WHERE `id_shop_product`=? LIMIT 1", array( $pid ));
			if( $vid != 0){
				$data2 = $conn->select("SELECT `name` FROM `shop_product_variant` WHERE `id_shop_product_variant`=? LIMIT 1", array( $vid ));
				$data[0]['shop_variant_name'] = $data2[0]['name'];
			}
		}else{
			$data = $conn->select("SELECT p.`title_sk`, p.`avatar1`, v.`shop_variant_name` FROM `shop_product` p, `shop_variant` v WHERE 
			p.`id_shop_product`=? AND v.`id_shop_variant`= ? LIMIT 1", array( $pid , $vid ));
		}
		return $data[0];
	}
	
	
	function printBacket($dph, $curr){
		$html = "";
		foreach($_SESSION['cart'] as $item => $val){
			$i = explode("-", $item);
			$v = explode("-", $val);
			$data = getBacketItem($i[0], $i[1]);
			$html .= 
			'<tr>
				<td><img src="/i/30-30-crop/avatars/'.($data['avatar1'] != "" ? $data['avatar1'] : "noimage.jpg").'" alt="'.($data['avatar1'] != "" ? $data['avatar1'] : "noimage.jpg").'" /></td>
				<td>'.$data['title_sk'].(isset($data['shop_variant_name']) ? ', '.$data['shop_variant_name'] : '').'</td>
				<td class="c">'.number_format($v[1] * ($dph / 100 + 1), 2).' '.$curr.'</td>
				<td><input type="text" maxlength="4" value="'.$v[0].'"/><a href=#'.$item.' class="edit" title="Upraviť počet kusov?"></a></td>
				<td class="c"><a href=#'.$item.' class="del" title="Odstrániť položku z košíka?"></a></td>
			</tr>';
		}
		return $html;
	}
	
	
	function sum($dph, $curr,  $totalPrice){
		return '<table><tr><td class="w200">Celková sumna: </td><td class="w100">'.$totalPrice.' '.$curr.'</td></tr>'.
				'<tr><td class="w200">DPH '.$dph.'%:</td><td class="w100">'.number_format($totalPrice * ($dph / 100 + 1) - $totalPrice, 2).' '.$curr.'</td></tr>'.
				'<tr><td class="w200">Celková suma s DPH:</td><td class="w100">'.number_format(($totalPrice * ($dph / 100 + 1 ) ), 2).' '.$curr.'</td></tr></table><div class="clearfix"></div>';
	}

// ----------------------------------------------------------

function getMailText($tID){
	global $conn;
	$r = $conn->select("SELECT `val` FROM `shop_config_text` WHERE `key`='$tID' LIMIT 1");
	return $r[0]["val"];
}

function printMailItems($dph, $curr, $totalPrice){
	$html = '<br><h3>Položky objednávky</h3><table border="1" cellspacing="0" cellpadding="0"><tr><td align="left"><b>Názov tovaru</b></td><td align="right"><b>Cena s DPH</b></td><td><b>Počet</b></td><td align="right"><b>Cena spolu</b></td></tr>';
	
	foreach($_SESSION['cart'] as $item => $val){
			$i = explode("-", $item);
			$v = explode("-", $val);
			$data = getBacketItem($i[0], $i[1]);
			$html .= 
			'<tr>'.
				'<td>'.$data['title_sk'].(isset($data['shop_variant_name']) ? ', '.$data['shop_variant_name'] : '').'</td>'.
				'<td align="right">'.number_format($v[1] * ($dph / 100 + 1), 2).' '.$curr.'</td>'.
				'<td align="center">'.$v[0].'</td>'.
				'<td align="right">'.number_format( $v[1] * ($dph / 100 + 1) * $v[0], 2).' '.$curr.'</td>'.
			'</tr>';
		}
		
		$d = explode("-", $_SESSION['dp']);
		if($d[0] > 1){	
		$html .= '<tr>'.
				 '<td>'.getDeliveryName( $d[0] ).'</td>'.
				 '<td align="right">'.($d[0] == 2 ? $d[2] : number_format($d[2] * ( $dph / 100 + 1), 2)).' '.$curr.'</td>'.
				 '<td align="center">1</td>'.
				 '<td align="right">'.($d[0] == 2 ? $d[2] : number_format($d[2] * ( $dph / 100 + 1), 2)).' '.$curr.'</td></tr>';	 
		}
		
		$html .= '<tr><td><b>Celkom k úhrade:</b></td><td align="right"><b>'.number_format(getTotalOrderPrice($dph, $totalPrice, $d[0],  $d[2]), 2).' '.$curr.'</b></td></tr>';
		return $html.'<table>';
	
}

function search($q){
	global $conn;
	return $conn->select("SELECT `id_shop_product`,`id_shop_product_status`, `id_shop_category`, `title_sk`, `price`, `price_sale`, `avatar1` , `header_sk` 
							   FROM `shop_product` 
							   WHERE active=1 AND ".
							   ( is_numeric($q) ? "`id_shop_product`=".intval($q)." LIMIT 1" : "`title_sk` LIKE '%${q}%' LIMIT 30"));	
}

function getTotalOrderPrice($dph, $itemsPrice, $dID, $dPrice){
		if($dID == 2){
			return $itemsPrice * ($dph / 100 + 1) + $dPrice;
		}
		return ($itemsPrice + $dPrice) * ($dph / 100 + 1);
	}


function getDeliveryName( $id ){
	global $conn;
	$r =  $conn->select("SELECT `delivery_name` FROM `shop_delivery` WHERE `id_shop_delivery`=? LIMIT 1", array( $id ) );	
	return $r[0]['delivery_name'];
}

function getAttrById($id){
	global $conn;
	$r = $conn->simpleQuery("SELECT `val` FROM `shop_attr` WHERE `id_shop_product`=$id AND `key`='hmotnost' LIMIT 1");
	return $r[0]['val'];
}


function getCategory($id){
	global $conn;
	$r =  $conn->select("SELECT `link_sk`, `category_name` FROM `shop_category` WHERE `id_shop_category`=? LIMIT 1", array( $id ) );	
	return $r[0];
}
?>

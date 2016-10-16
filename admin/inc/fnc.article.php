<?php 
// ----------------------------------------------------------

function articleTree($conn, $id, $cid = NULL){
	$html = "";
	$data =  $conn->select("SELECT `id_article`, `sub_id`, `title_sk`, `active` FROM `article` WHERE `sub_id`=? ORDER BY `order`", array( $id) );	
	for($i =0; $i < count( $data ); $i++){
		$data[$i] = array_map("clean", $data[$i]);
		$html .= '<li><a class="'.($cid == (int)$data[$i]["id_article"] ? "curr" : "").( $data[$i]["active"] == 0 ? ' i' : '').'" title="ID: '.$data[$i]["id_article"].'" href="./index.php?p=article&amp;aid='.$data[$i]["id_article"].'" >'.
			     $data[$i]["title_sk"]."</a>";
		
		if($conn->simpleQuery("SELECT `id_article` FROM `article` WHERE `sub_id`=".$data[$i]['id_article']. " LIMIT 1")){
			$html .= '<ul>'.articleTree($conn, $data[$i]['id_article'], $cid).'</ul>';
 		}
		
		$html .= '</li>';		
	}
	return $html;
}

// ----------------------------------------------------------

function articleAdminBC($conn, $id, $char){
	
	$data =  $conn->select("SELECT `id_article`, `sub_id`, `title_sk` FROM `article` WHERE `id_article`=? LIMIT 1", array( $id ));	
	if($data == null){
		return;
	}
	$data[0] = array_map("clean", $data[0]);
	$html = $char .' <a href="./index.php?p=article&amp;aid='.$data[0]['id_article'].'" >'.$data[0]['title_sk'].'</a>';
	
	if($data[0]['sub_id'] != 0){
		 return articleAdminBC($conn, $data[0]['sub_id'], $char).' '.$html;
	}
	return $html;
}

// ----------------------------------------------------------

function printArticles($conn, $id, $p, $q = null){
	
	if($q != null){
		$data = $conn->select("SELECT `id_article`, `active`, `order`, `hits`, `create`, `title_sk` FROM `article` WHERE ".
							 (is_numeric($q) ? "`id_article`=" : "`title_sk` REGEXP ")."? LIMIT 0, 15", array( $q ));
		
	}else{
		$data = $conn->select("SELECT `id_article`, `active`, `order`, `hits`, `create`, `title_sk` FROM `article` WHERE `sub_id`=? ORDER BY `order` LIMIT ".
								$p['offset'].", ".$p['adminPagi'], array( $id ));
	}
	if(count($data) == 0){
		return "<p class=\"alert\">Požiadavke nevyhovuje žiadny záznam</p>";
	}
	
	$html = "";

	for($i = 0; $i < count($data); $i++ ){
		$data[$i] = array_map("clean", $data[$i]);
		$html .= '<tr id="id'.$data[$i]['id_article'].'"  class="o'.$data[$i]['order'].'"><td class="c w45">'.$data[$i]['id_article'].'</td>'.
				 '<td class="w250"><a class="edit" title="Upraviť stránku ?" href="./index.php?p=article&amp;aid='.$data[$i]['id_article'].'">'.$data[$i]['title_sk'].'</a></td>'.
				 '<td class="c order w45"></td>'.
				 '<td class="c w45"><a href="#id'.$data[$i]['id_article'].'" title="Zmeniť aktivnosť ?" class="'.($data[$i]['active'] == 1 ? "a1" : "a0" ).'" ></a></td>'.
				 '<td class="c">'.$data[$i]['hits'].'</td>'.
				 '<td class="c">'.strftime("%d.%m.%Y/%H:%M", $data[$i]['create']).'</td>'. 
				 '<td><a class="del" title="Odstrániť stránku ?" href="#id'.$data[$i]['id_article'].'" ></a></td></tr>';
	}
	return $html; 
}

// ----------------------------------------------------------

function printLangs($config, $id){
		$html = "";
		$array = explode("," , $config["article_langs"]);
		foreach ($array as $val){
			$html .= '<a href="#aid'.$id.'" title="'.$val.'" class="btn2 '.($val == "sk" ? ' sel' : "").'" >'.$val.'</a>';
		}
		return $html;
}

?>
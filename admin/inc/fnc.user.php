<?php

function printUsers($conn, $p, $q = null){
	
	if($q != null){
		$sql = "SELECT u.`id_user`, t.`name`, u.`login`, u.`active`, u.`reg_time`, u.`givenname`, u.`surname`
				FROM  `user` u
				INNER JOIN `user_type` t
				ON u.`id_user_type`=t.`id_user_type`
				WHERE u.".(is_numeric($q) ? "`id_user`=" : "`login` REGEXP ")."? AND u.`id_user_type`!=5 
				LIMIT 20";
		$data = $conn->select($sql, array( $q ));
	}else{
		$sql = "SELECT u.`id_user`, t.`name`, u.`login`, u.`active`, u.`reg_time`, u.`givenname`, u.`surname`
				FROM  `user` u
				INNER JOIN `user_type` t
				ON u.`id_user_type`=t.`id_user_type`
				WHERE u.`id_user_type`!=5 
				ORDER BY u.`reg_time` DESC
				LIMIT ".$p['offset'].", ".$p['adminPagi'];
		$data = $conn->select($sql);
	}
	
	
	if(count($data) == 0){
		return "<p class=\"alert\">Požiadavke nevyhovuje žiadny záznam</p>";
	}
	
	$html = "";

	for($i = 0; $i < count($data); $i++ ){
		$data[$i] = array_map("clean", $data[$i]);
		$html .= '<tr id="id'.$data[$i]['id_user'].'" ><td class="c w45">'.$data[$i]['id_user'].'</td>'.
				 '<td class="w200"><a class="edit" title="Upraviť užívateľa ?" href="./index.php?p=user&amp;uid='.$data[$i]['id_user'].'">'.$data[$i]['login'].'</a></td>'.
				 '<td class="c w45"><a href="#id'.$data[$i]['id_user'].'" title="Zmeniť aktivnosť ?" class="'.($data[$i]['active'] == 1 ? "a1" : "a0" ).'" ></a></td>'.
				 '<td class="c">'.strftime("%d.%m.%Y/%H:%M", $data[$i]['reg_time']).'</td>'. 
				 '<td class="c">'.$data[$i]['name'].'</td>'.
				 '<td class="c">'.($data[$i]['givenname'] != "" ? substr($data[$i]['givenname'],0 , 1).'. ' : "" ).$data[$i]['surname'].'</td>'.	 
				 '<td><a class="del" title="Odstrániť stránku ?" href="#id'.$data[$i]['id_user'].'" ></a></td></tr>';
	}
	return $html; 
}


function getBrowser($u_agent)
{
    $bname = 'Nezistené';
    $platform = 'Nezistená';
    $version= "";

    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'Linux';
    }
    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'Mac';
    }
    elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'Windows';
    }
   
    // Next get the name of the useragent yes seperately and for good reason
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
    {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    }
    elseif(preg_match('/Firefox/i',$u_agent))
    {
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    }
    elseif(preg_match('/Chrome/i',$u_agent))
    {
        $bname = 'Google Chrome';
        $ub = "Chrome";
    }
    elseif(preg_match('/Safari/i',$u_agent))
    {
        $bname = 'Apple Safari';
        $ub = "Safari";
    }
    elseif(preg_match('/Opera/i',$u_agent))
    {
        $bname = 'Opera';
        $ub = "Opera";
    }
    elseif(preg_match('/Netscape/i',$u_agent))
    {
        $bname = 'Netscape';
        $ub = "Netscape";
    }
   
    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }
   
    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
            $version= $matches['version'][0];
        }
        else {
            $version= $matches['version'][1];
        }
    }
    else {
        $version= $matches['version'][0];
    }
   
    // check if we have a number
    if ($version==null || $version=="") {$version="?";}
   
    return array(
        'userAgent' => $u_agent,
        'name'      => $bname,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'    => $pattern
    );
}

function printLogs($conn, $p, $q = null){
	
	if($q != null){
		$sql = "SELECT  l.`id_user`, l.`user_agent`, l.`ip`, l.`time`, u.`login`
				FROM  `user_log` l
				INNER JOIN `user` u
				ON l.`id_user`=u.`id_user`
				WHERE u.".(is_numeric($q) ? "`id_user`=" : "`login` REGEXP ")."?
				ORDER BY l.`id_user_log` DESC
				LIMIT 30";
		$data = $conn->select($sql, array( $q ));
	}else{
		$sql = "SELECT  l.`id_user`, l.`user_agent`, l.`ip`, l.`time`, u.`login`
				FROM  `user_log` l
				INNER JOIN `user` u
				ON l.`id_user`=u.`id_user`
				ORDER BY l.`id_user_log` DESC
				LIMIT ".$p['offset'].", ".$p['adminPagi'];
		$data = $conn->select($sql);
	}
	
	
	if(count($data) == 0){
		return "<p class=\"alert\">Požiadavke nevyhovuje žiadny záznam</p>";
	}
	
	$html = "";

for($i = 0; $i < count($data); $i++ ){
		$b = getBrowser( $data[$i]['user_agent'] );
		$html .= '<tr id="id'.$data[$i]['id_user'].'" ><td class="edit"><a title="Zobraziť ?" href="./index.php?p=user&sp=edit&uid='.$data[$i]['id_user'].'">'.$data[$i]['login'].'</a></td>'.
				 '<td class="c">'.strftime("%d.%m.%Y/%H:%M", $data[$i]['time']).'</td>'. 
				 '<td>'.$data[$i]['ip'].'</td>'.
				 '<td>'.($b['name'] === 'Nezistené' ? $b['userAgent'] : ( $b['name'] .' - '. $b['version'] . " na platforme " .$b['platform']) ) . '</td></tr>';
	}
	return $html; 
}

?>
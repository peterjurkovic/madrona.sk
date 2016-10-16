<?php
	ob_start();
	if(!$auth->isLogined()){ die("Neautorizovaný prístup."); }
	require_once "./inc/fnc.main.php";
	$page = "404";
    	if(!isset($_GET['p'])){
			$page = "article";
		}elseif( in_array( $_GET['p'] , array("article", "user", "settings", "help", "stats"))){
			$page = $_GET['p'];
		}
	$aid = 0;
	$uid = 0;
	$s = 1;
	if(isset($_GET['aid'])) $aid = (int)$_GET['aid'];
	if(isset($_GET['uid'])) $uid = (int)$_GET['uid'];
	if(isset($_GET['s'])) $s = (int)$_GET['s'];
	
	if(!isset($_GET['m']) && $_SESSION['login'] == "demo")
	{	
		$_GET['m'] = "shop";
		$_GET['c'] = "order";
		$_GET['sp'] = "view";
	}
?>
<!DOCTYPE HTML>
<html>
<head>
	<title>ADMIN 3</title>
    <meta charset="utf-8" />
    <meta name="robots" content="noindex,nofollow"/>
    
    <!-- styles & js -->
    <link rel="stylesheet" href="./css/main.css" /> 
    <link rel="stylesheet" href="./css/ui-lightness/jquery-ui-1.8.16.custom.css" />
    <link rel="stylesheet" href="./css/jquery.treeview.css" /> 
     <!--[if IE]>
		  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
      <![endif]-->
    
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script>!window.jQuery && document.write('<script src="./js/jquery.min.js"><\/script>')</script>
    <script src="./js/jquery-ui-1.8.16.custom.min.js"></script>
    <script src="./js/jquery.form.min.js"></script>
    <?php 
	  if(isset($_GET['m'])){ 
				$page = "modul";
				include './modules/'.$_GET['m'].'/inc/js.php';
	  }else{
	
		switch($page){ 
			case "article" :
			?>
			<script src="./js/jquery.cookie.js"></script>
			<script src="./js/jquery.treeview.js"></script>
            <script src="./js/jquery.tablednd_0_5.js"></script>
            <script src="./ckeditor/ckeditor.js"></script>
            <script src="./ckfinder/ckfinder.js"></script>
            <script src="./js/scripts.js"></script>
            <script src="./js/scripts.article.js"></script>
			<?php
			break;
			case "intro" :
			case "stats" :
				echo '<script src="https://www.google.com/jsapi"></script>';
				echo '<script src="./js/scripts.js"></script>';
			break;
			default :
				echo '<script src="./js/scripts.js"></script>';
     	} 
		 	
	 }
    ?>
     
     
    
	
</head>
<body>
	<header>
    	<a href="./" title="Ovládací panel"><img src="./img/logo.jpg" alt="" /></a>
        <div>
        	<p>Vitajte <a  href="./index.php?p=user&amp;sp=edit&amp;uid=<?php echo $_SESSION['id']; ?>"><?php echo $_SESSION['login']; ?></a> ! <img src="./img/avatar.jpg" alt="ADMIN" /></p>
        	<a class="logout" href="./inc/login/logout.php">odhlásiť</a>
        </div>
    </header>
    <div id="status"></div>
    
   	
	<?php	
	if(!isset($_GET['m'])){
		include "./inc/section/nav.php";
		include "./inc/section/".$page.".php";		
	}else{
		include "./modules/".$_GET['m']."/index.php";
	}
	
	ob_end_flush(); 
	?>

</body>
</html>

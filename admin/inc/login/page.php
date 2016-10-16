<!DOCTYPE HTML>
<html>
<head>
	<title>ADMIN 3</title>
    <meta charset="utf-8" />
    <meta name="robots" content="noindex,nofollow"/>
 
    <!-- styles & js -->
    <link rel="stylesheet" href="./css/main.css" /> 
    <link rel="stylesheet" href="./css/login.css" /> 
     <!--[if IE]>
		  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
      <![endif]-->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script>!window.jQuery && document.write('<script src="./js/jquery.min.js"><\/script>')</script>
    <script src="./js/jquery.form.min.js"></script>
    <script src="./js/jquery-ui-1.8.16.custom.min.js"></script>
 	<script src="./js/scripts.min.js"></script>
    <script>
	$(function() {
		$('form[name=login]').submit(function (){
			if(!validate($(this))){
				return false;
			}
		});
		$('input[name=login]').focus();
	});
	</script>
 
</head>
<body>
	<div id="status"></div>
        <div id="form">
        	<h1></h1>
            <form method="POST" action="./inc/login/" name="login">
            	<?php echo (isset($_SESSION["status"]) ? '<p class="error">'.$_SESSION["status"].'</p>' : ""); unset($_SESSION["status"]); ?>
                <div><label>Prihlasovacie meno: </label><input type="text" name="login"  class="w200 required" /></div>
                <div><label>Prihlasovacie heslo: </label><input type="password" name="pass" class="w200 required fiveplus" /></div>
                <input type="hidden" name="token" value="<?php echo session_id(); ?>" / >
                <input type="submit" name="btn" value="Prihlásiť" class="ibtn" />
                <div class="clear"></div>
            </form>
        </div>
  	
</body>
</html>
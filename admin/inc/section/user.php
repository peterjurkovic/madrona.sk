
<section>
<?php 
	try{
		include "./inc/fnc.user.php";
		if(!isset($_GET['sp'])){
				include "user.view.php";
		}else{
			switch ($_GET['sp']){
				case "new" :
						include dirname(__FILE__)."/user.new.php";
					break;
				case "edit" :
						include dirname(__FILE__)."/user.edit.php";
					break;
				case "add" :
						include dirname(__FILE__)."/user.add.php";
					break;
				case "logs" :
						include dirname(__FILE__)."/user.logs.php";
					break;
				default :
					echo "<strong class=\"error\">404 - Požadovaná stránka sa nenašla</strong>";
			
			}
		}
	}catch(MysqlException $ex){
		echo "<strong class=\"error\">Vyskytol sa problém s databázou, operáciu skúste zopakovať</strong>";
	}
	
?>
</section>
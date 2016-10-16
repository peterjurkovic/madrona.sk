<?php 	include "./inc/fnc.article.php";  ?>
<section>
	<?php
        try{
            if(!isset($_GET['sp']) || $_GET['sp'] == "edit"){
                include "article.edit.php";
            }else{
                echo "<strong class=\"error\">404 - Požadovaná stránka sa nenašla</strong>";
            }
        }catch(MysqlException $ex){
            echo "<strong class=\"error\">Vyskytol sa problém s databázou, operáciu skúste zopakovať</strong>";
        }
    ?>
</section>
<?php if(!$auth->isLogined()){ die("Neautorizovaný prístup."); } ?>
<div class="breadcrumb">
	Nachádzate sa:
	<a href="./index.php">Domov</a> &raquo;
    <a href="./index.php?p=user">Správa užívateľov</a> &raquo;
    <a href="./index.php?p=logs">Logy užívateľov</a>
</div>
<strong class="h1">Správa užívateľov</strong>

<div class="left">
		<?php include dirname(__FILE__)."/user.nav.php" ?>
</div>

<div class="right">       
      	<div class="cbox">
    <form class="search">
    	<input type="text" name="q" id="user-login" />
        <input type="submit" class="ibtn"  value="Hladať" />
    </form>
            <strong class="h img article">Logy užívateľov</strong>
             <?php
                $count = $conn->simpleQuery("SELECT count(*) FROM `user_log`");
                $count = $count[0]["count(*)"];
                $config['offset'] = ($s == 1 ? 0 :  ($s * $config["adminPagi"]) - $config["adminPagi"]);    
            ?>
            <table class="tc" id="dnd" >
              <thead>
                  <tr>
                    <th scope="col">&nbsp;Uživateľ</th>
                    <th scope="col">&nbsp;Čas</th>
                    <th scope="col">&nbsp;IP adresa</th>
                    <th scope="col">&nbsp;Prehliadač</th>
                  </tr>
              </thead>
              <tbody class="user_log">
                <?php  echo ( printLogs($conn, $config) ) ; ?>
             </tbody>
        </table>
        	 <?php 
				$nav = new Navigator($count, $s , '/index.php?'.preg_replace("/&s=[0-9]/", "", $_SERVER['QUERY_STRING']) , $config["adminPagi"]);
				$nav->setSeparator("&amp;s=");
				echo $nav->smartNavigator();
				?>
        	<div class="clear"></div>
        </div>

</div>
<div class="clear"></div>



<?php 
	if(!$auth->isLogined()){ die("Neautorizovany pristup."); }
	include_once "./inc/mod_ga/class.GoogleAnalytics.php";
	include_once "./inc/mod_ga/class.GoogleAnalyticsException.php";
	include_once "./inc/mod_ga/fnc.GoogleAnalytics.php";
?>
<section>
    	<strong class="h1">Administračný systém webu <?php echo $_SERVER['SERVER_NAME']; ?></strong>
    	<strong class="h">Ovládací panel</strong>
    	<?php
            	if(isset($_SESSION['status'])){
					echo '<p class="alert">'.$_SESSION['status'].'</p>';
					unset($_SESSION['status']);
				}
			?>
        <div id="dash">
            <a href="./index.php?p=article" class="article">Správa obsahu</a>
            <a href="./index.php?p=user" class="users">Správa užívateľov</a>
            <a href="./index.php?p=settings" class="set">Nastavenia</a>
            <a href="./index.php?p=help" class="help">Pomocník</a>
            <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>" class="view">Zobraziť web</a>
            <a href="./index.php?p=stats" class="stats">Štatistiky</a>
            <!-- <a href="./index.php?m=shop&amp;c=order&amp;sp=view" class="shop">E-shop</a>-->
            <a href="./inc/login/logout.php" class="logout">Odhlásiť</a>
            <div class="clear" ></div>
		</div>
        
        
        <strong class="h">Štatistiky</strong>
    	<div id="stats">
            <div id="chart_div">
                <?php
                try {
                    $oAnalytics = new GoogleAnalytics($config['ga_user'], author($config['ga_pass']));
                    $oAnalytics->useCache();
                    $oAnalytics->setProfileById($config['ga_profile']);
                    $oAnalytics->setDateRange(date('Y-m-d', time() - 2678400), date('Y-m-d', time() - 86400));
                    $arr = $oAnalytics->getData(array( 'dimensions' => 'ga:date','metrics' => 'ga:visits','sort' => 'ga:date'));									
                    $ar2 = $oAnalytics->getData(array( 'dimensions' => 'ga:date','metrics' => 'ga:pageviews','sort' => 'ga:date'));					
              } catch (GoogleAnalyticsException $e) { 
                  echo '<p class="error">Nastala chyba v spracovaní dát zo služby Google Analytics.</p>'; 
              } 
            ?>
            <script>
                google.load("visualization", "1", {packages:["corechart"]});
                google.setOnLoadCallback(drawChart);
                function drawChart() {
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'D8tum');
                data.addColumn('number', 'Návštevy');
                data.addColumn('number', 'Zobrazenia');
                data.addRows([<?php  $data = chartData($arr, $ar2); echo $data[0];?> ]);
                var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
                chart.draw(data, {width: 700, height: 300, title: 'Štatistiky za posledných 30 dní',
                                  hAxis: {title: 'Year', titleTextStyle: {color: '#f6f6f6'}}
                                 });
                }
            </script>
          </div>
            <table>

    	<tr class="odd">
        	<td class="l">Návštevy za 30 dní:</td>
            <td  class="r"><?php echo $data[1]; ?></td>
        </tr>
        <tr>
        	<td class="l">Zobrazenia za 30 dní:</td>
            <td class="r"><?php echo $data[2]; ?></td>

        </tr>
        <tr class="odd">
        	<td class="l">Počet aktívnych stránok:</td>
            <td class="r"><?php $arr = $conn->simpleQuery("SELECT count(*) FROM `article` WHERE `active`=1");  echo $arr[0]["count(*)"]; ?></td>
        </tr>
         <tr>
        	<td class="l">Počet registrovaných úžívateľov:</td>

            <td class="r"><?php $arr = $conn->simpleQuery("SELECT count(*) FROM `user` WHERE `id_user_type`!=4");  echo $arr[0]["count(*)"]; ?></td>
        </tr>
        <tr class="odd">
        	<td class="l">Z toho s prístupom do administrácie:</td>
            <td class="r"><?php $arr = $conn->simpleQuery("SELECT count(*) FROM `user` WHERE `id_user_type`>1 AND `id_user_type`!=4");  echo $arr[0]["count(*)"]; ?></td>
        </tr>
         <tr>

        	<td class="l">Neaktivní užívateľia:</td>
            <td class="r"><?php $arr = $conn->simpleQuery("SELECT count(*) FROM `user` WHERE `active`=0");  echo $arr[0]["count(*)"]; ?></td>
        </tr>
         <tr class="odd">
        	<td class="l">Počet komentárov:</td>
            <td class="r">0</td>
        </tr>

         <tr>
        	<td class="l">Počet blokovaných komentárov:</td>
            <td class="r">0</td>
        </tr>
    </table>
        <div class="clear" ></div>
		</div>
        
        <strong class="h">Podpora</strong>
        	<div id="support">
                <p>
                    Peter Jurkovič<br />
                    E-mail: <a href="mailto:info@peterjurkovic.sk?Subject=Podpora-<?php echo $_SERVER['SERVER_NAME']; ?>">info@peterjurkovic.sk</a><br />
                    Mobil: +421 904 938 419
                </p>
                <em>V príprade problémov alebo nejasností ma môžte rýchlo kontaktovať prostredníctvom tohto formulára, odpoveď bude doručená na vašu registračnú e-mailovú adresu.</em>
                <textarea name="support" cols="9" rows="5">Formulár je dočasne vypnutý</textarea><a href="#"  class="btn" title="Odolsať">Odoslať</a>
                <div class="clear" ></div>
        	</div>
    </section>
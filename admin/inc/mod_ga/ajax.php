<?php
	session_start();
	
	function __autoload($class){
		include_once "../class.".$class.".php";
	}
	require_once "../../config.php";
	$conn = Database::getInstance($config['db_server'], $config['db_user'], $config['db_pass'], $config['db_name']);
	require dirname(__FILE__)."/class.GoogleAnalytics.php"; 
	require dirname(__FILE__)."/class.GoogleAnalyticsException.php"; 
	require dirname(__FILE__)."/fnc.GoogleAnalytics.php"; 
 
	function validateDate($date){
		return preg_match("(\d{4}\-\d{1,2}\-\d{1,2})" , $date);
	}
	
	if(!validateDate($_POST['from']) || !validateDate($_POST['to'])){
		die();
	}
	
	$from 	= $_POST['from'];
	$to 	= $_POST['to'];
	try{ 
		include dirname(__FILE__)."/ga.init.php";
?>

            
       <script>
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Dátum');
        data.addColumn('number', 'Návštevy');
		data.addColumn('number', 'Zobrazenia');
        data.addRows([<?php $data = chartData($visitors, $pageviews); echo $data[0]; ?>]);
        var chart = new google.visualization.AreaChart(document.getElementById('chart_div2'));
        chart.draw(data, {width: 1000, height: 300, title: '', hAxis: {title: 'Year', titleTextStyle: {color: '#f6f6f6'}}  }); 
      }
    </script>

	 <div id="chart_div2"></div>
     <div id="statbox">
                      <img src="http://chart.apis.google.com/chart?chs=600x275&chtt=Štatistika+webových+prehliadačov&cht=p<?php echo browsers($browsers); ?>&chma=|0,5" width="600" height="275" alt="" />
                       <table>
                        <tr class="odd">
                            <td class="l">Návštevy (<?php echo printDate($from)." - ". printDate($to); ?>) :</td>
                            <td  class="r"><?php  echo $data[1]; ?></td>
                        </tr>
                        <tr>
                            <td class="l">Zobrazenia (<?php echo printDate($from)." - ". printDate($to); ?>) :</td>
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
                </div>
              
              <strong class="h">Ostatné štatistiky</strong>
               <table class="statst tc">
                        <tr>
                            <th>Zdroje návštevnosti</th>
                            <th>Počet návštev</th>
                        </tr>
                            <?php echo getTableData($sources); ?>
                        </table>
                        
                        <table class="statst tc">
                         <tr>
                            <th>Klúčové slová zadané do vyhľadávača</th>
                            <th>Počet návštev</th>
                        </tr>
                            <?php echo getTableData($keywords); ?>
                </table>
          <?php
      } catch (GoogleAnalyticsException $e) { 
		  echo '<p class="error">Nastala chyba v spracovaní dát zo služby Google Analytics</p>'; 
	  } 
	   ?>
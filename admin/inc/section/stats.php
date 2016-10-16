<section>
	<?php if(!$auth->isLogined()){ die("Neautorizovaný prístup."); } ?>
    <div class="breadcrumb">
        Nachádzate sa:
        <a href="./index.php">Domov</a> &raquo;
        <a href="./index.php?p=stats">Štatistiky</a>
    </div>
	<strong class="h1">Štatistiky</strong>
    
      	<div id="body">
    <?php 	
	$from = date('Y-m-d', time() - 2678400);
	$to =  date('Y-m-d',  time() - 86400);
	include_once "./inc/mod_ga/class.GoogleAnalytics.php";
	include_once "./inc/mod_ga/class.GoogleAnalyticsException.php";
	include_once "./inc/mod_ga/fnc.GoogleAnalytics.php";
	

	?>
   
    <script>
		jQuery.extend({
			getNewData: function(data) {
				$("#data").load( "./inc/mod_ga/ajax.php", data, function() { drawChart(); $('.loader').hide();createClasses();  });
				return false;
			}
		});
		
		$(function() {
			$("#dp1, #dp2").datepicker({
				dayNamesMin: ['Ne', 'Po', 'Út', 'St', 'Št', 'Pi', 'So'], 
				monthNames: ['Január','Február','Marec','Apríl','Máj','Jún','Júl','August','September','Október','November','December'], 
				maxDate: -1,
				autoSize: false,
				dateFormat: 'yy-mm-dd',
				firstDay: 1});
				
			$('a#use').click(function() {
				$('.loader').show();
				$('#data').html('');
				$('#chart_div2').remove();
				$.getNewData({ to : $("#dp2").val(), from : $("#dp1").val()});
				return false;
			 }); 
		});
	</script>
     <p class="info">Dáta sú sťahované zo skužby <strong>Google Analytics</strong>, preto môže generovanie štatistík chvíľu trvať.</p>
     <div id="ga1">
             <p>Zobraziť štaitistiky za obdobie od:</p>
             <input id="dp1" type="text" class="c" value="<?php echo $from; ?>">
             <p>  od: </p>
             <input id="dp2" type="text"  class="c"  value="<?php echo $to; ?>">
             <div class="bx"><a href="#" id="use" class="btn">Zobraziť</a></div>
             	<img  class="loader" src="/img/ajax-loader.gif" alt="Načítavam..." />
             <div class="clear"></div>
     </div>
    
        <?php try{ 
			require "./inc/mod_ga/ga.init.php";
		?>
      <strong class="h">Štatistika návštev a zobrazenia stránok</strong>      
      <div id="chart_div2"></div>
         <div id="data" class="clear"> 
         	<strong class="h">Štatistika prístupov z webových prehliadačov</strong>
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
    
       </div> <!-- end data -->
       <?php
       	 } catch (GoogleAnalyticsException $e) { 
		  echo '<p class="error">Nastala chyba v spracovaní dát zo služby Google Analytics</p>'; 
	  } 
	   ?>
        <div class="clear"></div>
</section>
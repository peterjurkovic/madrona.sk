<?php

// DATA API / data feed: http://code.google.com/intl/sk-SK/apis/analytics/docs/gdata/gdataReferenceDataFeed.html
// query explorer http://code.google.com/intl/sk-SK/apis/analytics/docs/gdata/gdataExplorer.html

		  
		  $oAnalytics = new GoogleAnalytics($config['ga_user'],  author($config['ga_pass']));
          $oAnalytics->useCache();
          $oAnalytics->setProfileById($config['ga_profile']);
	//$oAnalytics->setProfileByName("podrezvanie-muriva.sk");

		  
		  // $oAnalytics->setMonth(date('n'), date('Y'));
		  
		 $oAnalytics->setDateRange($from, $to);
		 
		 
		 // pocet navstev, utriedenych podla datumu (dni)
		 $visitors = $oAnalytics->getData(array( 'dimensions' => 'ga:date',
												 'metrics'    => 'ga:visits',
												 'sort'       => 'ga:date'));
		
		 // pocet zobrazeni stranok, utriedenzch podla datumu (dni)										
		 $pageviews = $oAnalytics->getData(array( 'dimensions' => 'ga:date',
		 										  'metrics'    => 'ga:pageviews',
												  'sort'       => 'ga:date'));
		
		 // statistiky prehliadacov									
		 $browsers = $oAnalytics->getData(array( 'dimensions' => 'ga:browser',
		 										 'metrics'    => 'ga:visits'));
		
		 // zadane slova do vyhladavacov									
		 $sources = $oAnalytics->getData(array( 'dimensions' => 'ga:source',
		 										'metrics'    => 'ga:visits',
												'max-results' => '20',
												'sort'       => '-ga:visits')); 
		
		// priemerna dlzka navstevnika na stranke
		$durations = $oAnalytics->getData(array( 'dimensions' => 'ga:year',
		 										 'metrics'    => 'ga:avgTimeOnSite'));	
		
		// avg pocet zobrazenych stranok navstevnikom										
		$pageviewsPerVisit = $oAnalytics->getData(array('dimensions' => 'ga:year',
		 												'metrics'    => 'ga:pageviewsPerVisit'));
		
		// novi navstevnici											
		$percetNewVisits = $oAnalytics->getData(array( 	'dimensions' => 'ga:year',
		 												'metrics'    => 'ga:percentNewVisits'));
													
		$keywords = $oAnalytics->getData(array( 'dimensions' => 'ga:keyword',
		 										'metrics'    => 'ga:visits',
												'max-results' => '20',
												'sort'       => '-ga:visits'));					
	 
?>
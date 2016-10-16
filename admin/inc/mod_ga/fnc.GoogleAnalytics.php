<?php
	/** -------------------------------------------
	*	Google Analytics format functions
	*
	*	@version 02.02.2011
	*	@author Peter Jurkovic
	*   @link http://code.google.com/intl/sk-SK/apis/chart/
	*/

	
	function isNumeric($value){
		if(is_numeric($value)){
			return $value;	
		}
	}
	
	function printDate($d){
		return $d[8].$d[9].".".$d[5].$d[6].".".$d[0].$d[1].$d[2].$d[3]; 
	}
	

	function editChartDate($d){
		return $d[6].$d[7].".".$d[4].$d[5];
	}
	



	/** ----------------------------------------------------
	* Funkcia ktora spocita navstevnikov, zobrazenia a  
	* naformatuje data pre Google Chart API.
	* ['10.03',14, 42] - [dd.yy , visits, pageviews]  
	*
	* @param array - navstevnici
	* @param array - zobrazenia 
	* @return array  -  index 0 String data pre Google Chart API
	*					index 1 int sucet navstevnikov
	*					index 2 int sucet zobrazeni  
	*/
	function chartData($visitors, $pageviews){
		
		if(
			(!is_array($visitors) || count($visitors) == 0) &&
			(!is_array($pageviews) || count($pageviews) == 0)
		){ 
			return;
		}
		
		// prefiltruje data
		$visitors = array_map("isNumeric", $visitors);
		$pageviews = array_map("isNumeric", $pageviews);
		
		$data[0] = "";
		$data[1] = 0;
		$data[2] = 0;
		$i = 1;
		foreach ($visitors as $key => $val){
			$data[0] .= "['". editChartDate((string)$key)."',".$val.", ".$pageviews[$key]."]";	
			$data[1] = $data[1] + (int)$val;
			$data[2] = $data[2] + (int)$pageviews[$key];
			if($i != count($visitors)) { $data[0] .= ", "; }
			$i++;
		}	
		return $data;
	}	
	
	
	
	
	
	/** ----------------------------------------------------
	* Funkcia a spriemeruje hodnoty pola
	* 
	* @param array 
	* @return Number 
	*/
	function avgData($data){
		// ak je pole praydne skonci
		if(!is_array($data) || count($data) == 0){ 
			return "-";
		}
		// prefiltruje data
		$data = array_map("isNumeric", $data);
		// spocita hodnoty
		$count = array_sum($data);
		// zaokruhli a vrati vysledok
		return round($count / count($data), 2);
	}






	/** ----------------------------------------------------
	* Funkcia vrati naformatovanu html tabulku
	* 
	*
	* @param array - pole, kde kluc je String a hodnota Number
	* @return String - (HTML) tabulka bez root (<table>) elementov
	*/
	function getTableData($data){
		if(!is_array($data) || count($data) == 0){ 
			return "-";
		}
		$html = "";
		foreach ($data as $keyword => $count){
			$html = $html."<tr><td>".
					str_replace("(direct)","Priama návštevnosť", str_replace("(not set)","Nezistené",$keyword)).
					"</td><td class=\"r c\">".$count."</td></tr>";	
		}
		return $html;
	}
		
		
		
		
		
	/** ----------------------------------------------------
	* Funkcia, ktora spocita priemernu dlzku navstevnika 
	* na stranke. vystupny format (int) min (int) sek
	* @param array 
	* @return String  - <min> min <sek> sek
	*/	 	 
	function duration($data){
		if(!is_array($data) || count($data) == 0){ 
			return "-";
		}
		$data = array_map("isNumeric", $data);
		$count = array_sum($data);
		return floor($count / count($data) / 60) ." min " .( $count / count($data) % 60 ) . " sek";
	}
	
	
	
	
	
	/** ----------------------------------------------------
	* Funkcia, vzgeneruje parametre URL ku Google Chart API
	* Example: https://chart.googleapis.com/chart?cht=<chart_type>&chd=<chart_data>&chs=<chart_size>&...additional_parameters...
	*
	* @link http://code.google.com/intl/sk-SK/apis/chart/docs/making_charts.html#introduction 
	* @param array - key:String, val:Number
	* @return String  - url
	*/
	 function browsers($browsers){
		if(!is_array($browsers) || count($browsers) == 0){ 
			return;
		}
		
		 $otherBrowsersCount = 0;
		 $count = 0;
		 
		 // prefiltruje pole, zanecha len zname prehliadace
		 foreach ($browsers as $browserName => $value){
			$count += (int)$value;
			if(
				($browserName == 'Chrome') ||
				($browserName == 'Firefox') ||
				($browserName == 'Internet Explorer') ||
				($browserName == 'Opera') ||
				($browserName == 'Safari') 		
			){
				continue;
			}else{
				$otherBrowsersCount += (int)$value;		
				unset($browsers[$browserName]);
			}
			
			if($otherBrowsersCount != 0){	
				$browsers["Ostatné"]= $otherBrowsersCount; 	
			}
		}
		
		 // konfiguracia grafu, viac tu: http://code.google.com/intl/sk-SK/apis/chart/docs/data_formats.html
		 // a tu http://code.google.com/intl/sk-SK/apis/chart/docs/making_charts.html#introduction
		 $data  = "&chd=t:"; // hodnoty 
		 $label = "&chl="; //  popisky
		 $i = 1;
		 
		 
		// generovanie dat pre Google Chart API
		foreach ($browsers as $browserName => $value){
			$data .= round(($value / $count) * 100, 0);
			$label.= str_replace(" ", "+", $browserName. " (".round(($value / $count) * 100, 2)."%)");
			if($i != count($browsers)) { $data .= ",";$label.= "|"; }
			$i++;
		} 
		// query example:  https://chart.googleapis.com/chart?cht=<chart_type>&chd=<chart_data>&chs=<chart_size>&...additional_parameters...
		return $data.$label;
	}	

?>
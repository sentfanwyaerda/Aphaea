<?php
require_once('php-sun-almanak.php');
require_once('php-moon-phase/moon-phase.php');
class weather {
	var $debug = NULL;
	

	# Data Input

	# Analysing Weather Data

	# ICONS:
	private $icon_set = NULL;
	private $icon_set_alias = array(/*JSON*/);
	function load_icon_set($directory){
		if(file_exists($directory) && is_dir($directory) && file_exists($directory.'/weather.alias')){
			$this->icon_set = $directory;
			$this->icon_set_alias = json_decode( file_get_contents($directory.'/weather.alias') );
		} else { return /*error*/ FALSE; }
	}
	private $last_icon = FALSE;
	/*URI*/ function get_icon_url($wcode=NULL){
		if($wcode == NULL){ $wcode = $this->_calculate_current_weather_code(); }
		$match = array();
		/* search weather.alias {$this->icon_set_alias} for best match, list on match%, return match[0] */
		if(is_array($this->icon_set_alias)){foreach($this->icon_set_alias as $i=>$item){
			if(is_object($item)){ $match[$i] = $this->_calculate_weather_code_match($item->code, $wcode); }
		}}
		#/*debug*/ print_r($match);
		/*debug*/ foreach($match as $i=>$value){ $this->debug .= $wcode."\t".$this->icon_set_alias[$i]->code."\t(".$value.")%\t".$this->icon_set_alias[$i]->src."\n"; }
		$maximum = $this->_get_maximum( $match , FALSE);
		$this->last_icon = array("id" => $maximum, "score" => $match[$maximum]);
		return /*get first: best result*/ $this->icon_set_alias[ $maximum ]->src;
	}
	function get_last_icon_score(){
		return (isset($this->last_icon["score"]) ? $this->last_icon["score"] : FALSE);
	}
	private /*string [1-3][0-3]{4}[0-9] */ function _calculate_current_weather_code(){ return '121001'; }
	private /*double*/ function _calculate_weather_code_match($a, $b){
		if(!preg_match("#[1-3][0-3]{4}[0-9]#", $a)){ return -1; }
		if(!preg_match("#[1-3][0-3]{4}[0-9]#", $b)){ return -2; }
		#/*debug*/ return ((int) $a + (int) $b);
		$a = (string) $a;
		$b = (string) $b;
		$m = 0;
		if($a{0} === $b{0}){ $m += 3; }
		if($a{0} === '3' || $b{0} === '3'){ $m += 1.5;}
		if($a{5} === $b{5}){ $m += 5; }
		
		#compare ${1} clouds
		if($a{1} === $b{1}){ $m += 1; }
		elseif($a{1} === '0' || $b{1} === '0'){ $m += 0; }
		else{
			$m += ($a{1} > $b{1} ? (($a{1}-$b{1}) / 3) : (($b{1}-$a{1}) / 3) );
			#/*debug*/ print $a{1}.':'.$b{1}.' = '.(($a{1}-$b{1}) / 3)."\n";
		}
		#compare ${2} rain
		if($a{2} === $b{2}){ $m += 1.5; }
		elseif($a{2} === '0' || $b{2} === '0'){ $m += 0; }
		else{
			$m += ($a{2} > $b{2} ? (($a{2}-$b{2}) / 3) + 0.5 : (($b{2}-$a{2}) / 3) + 0.5 );
			#/*debug*/ print $a{2}.':'.$b{2}.' = '.(($a{2}-$b{2}) / 3)."\n";
		}
		#compare ${3} snow
		if($a{3} === $b{3}){ $m += 1.7; }
		elseif($a{3} === '0' || $b{3} === '0'){ $m += 0; }
		else{ $m += ($a{3} > $b{3} ? (($a{3}-$b{3}) / 3) + 0.7 : (($b{3}-$a{3}) / 3) + 0.7 ); }
		#compare ${4} hail / ice
		if($a{4} === $b{4}){ $m += 1.6; }
		elseif($a{4} === '0' || $b{4} === '0'){ $m += 0; }
		else{ $m += ($a{4} > $b{4} ? (($a{4}-$b{4}) / 3) + 0.6 : (($b{4}-$a{4}) / 3) + 0.6 ); }
		
		$m = round( (($m / 15.3) * 100) , 1); #Normalize for a result between [0,1]
		return $m;
	}
	private function _get_maximum($ar=array(), $result='key'){
		if(is_array($ar)){
			$max_value = FALSE;
			$max_key = FALSE;
			foreach($ar as $key=>$value){
				if($max_value === FALSE || $value > $max_value){
					#/*debug*/ print $key.' ('.$value.') > '.$max_key.' ('.$max_value.")\n";
					$max_value = $value;
					$max_key = $key;
				}
			}
			return (($result == 'key' || $result === FALSE) ? $max_key : $max_value);
		}
		else{ return FALSE; }
	}
}
?>
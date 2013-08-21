<?php
class SunAlmanak {
	private $longitude; #South negative
	private $latitude; #West negative
	private $zenith = array();
	private $today;
	private $twilight = NULL;
	
	function __construct( $pdate=NULL ){
		if(is_null($pdate)){ $pdate = time(); }
		$this->today = $pdate;
		$this->longitude = ini_get("date.default_longitude");
		$this->latitude = ini_get("date.default_latitude");
		$this->zenith = array('sunrise' => ini_get("date.sunrise_zenith"), 'sunset' => ini_get("date.sunset_zenith") );
		#$this->zenith = array('sunrise' => 90.0, 'sunset' => 90.0 );
		
		$this->twilight = NULL; #(civil|nautical|astronomical|)
		$this->today = $this->daystart($this->today);
	}
	function set_location($longitude, $latitude=NULL){
		/*debug*/ print "location:\t".$longitude.' '.$latitude."\n";
		#example of Nijmegen: 51&deg;50'N, 5°52'E 
		if(is_double($longitude) && is_double($latitude)){
			$this->longitude = $longitude;
			$this->latitude = $latitude;
			return TRUE;
		}
		$degstr = '[0-9.\*\'\"&deg; ]+'; # [\+\-]?
		if($latitude === NULL && preg_match("#^(".$degstr."[NSZ][B]?)[,]?\s+(".$degstr."[EWO][L]?)$#i", $longitude, $buffer)){
			$this->longitude = $this->_degree2double($buffer[1]);
			$this->latitude  = $this->_degree2double($buffer[2]);
			return TRUE;
		}
	}
	private function _degree2double($str){
		if(preg_match("#^([+-]?)([0-9]+([.][0-9]+)?)([NSZWEO][BL]?)?$#", $str, $buffer)){
			$double = (double) $buffer[2];
			$double = $double * (in_array($buffer[4], array('N','NB', 'E','O','OL')) ? 1 : -1 ) * ($buffer[1] == '-' ? -1 : 1);
			return $double;
		} elseif(preg_match("#^([+-]?)([0-9]+)(&deg;|\*|°)\s?([0-9]+)(['])\s?(([0-9]+)([\"]))?\s?([NSZWEO][LB]?)?$#", $str, $buffer)){
			#/*debug*/ print $str." &rArr; ".print_r($buffer, TRUE)."\n";
			$double = (double) $buffer[2];
			if(isset($buffer[4])) $double += ( (double) $buffer[4] / 60 );
			if(isset($buffer[7])) $double += ( (double) $buffer[7] / (60*60) );
			$double = $double * (in_array($buffer[9], array('N','NB', 'E','O','OL')) ? 1 : -1 ) * ($buffer[1] == '-' ? -1 : 1);
			return (double) $double;
		}
		return FALSE;
	}
	
	function daystart($when=NULL){
		if(is_null($when)){ $when = $this->today; }
		return $daystart = mktime(0,0,0,date('m', $when),date('d', $this->today),date('Y', $when));
	}
	function sunrise($when=NULL, $mode=SUNFUNCS_RET_STRING){
		if(is_null($when)){ $when = $this->today; }
		#return date_sunrise($when, $mode, $this->latitude, $this->longitude, $this->zenith['sunrise'], (date("Z", $when)/(60*60)) );
		return $this->_sunfuncs_ret_rewrite( $this->_date_sun_info_get($this->_get_twilight_alias('sunrise'), $when), SUNFUNCS_RET_TIMESTAMP, $mode);
	}
	function sunset($when=NULL, $mode=SUNFUNCS_RET_STRING){
		if(is_null($when)){ $when = $this->today; }
		#return date_sunset($when, $mode, $this->latitude, $this->longitude, $this->zenith['sunset'], (date("Z", $when)/(60*60)) );
		return $this->_sunfuncs_ret_rewrite( $this->_date_sun_info_get($this->_get_twilight_alias('sunset'), $when), SUNFUNCS_RET_TIMESTAMP, $mode);
	}
	function daylength($when=NULL, $mode=SUNFUNCS_RET_STRING){
		if(is_null($when)){ $when = $this->today; }
		return $this->_sunfuncs_ret_rewrite( ( $this->sunset($when, SUNFUNCS_RET_TIMESTAMP) - $this->sunrise($when, SUNFUNCS_RET_TIMESTAMP) ) , SUNFUNCS_RET_TIMESTAMP, $mode);
	}
	function noon($when=NULL, $mode=SUNFUNCS_RET_STRING){
		if(is_null($when)){ $when = $this->today; }
		return $this->_sunfuncs_ret_rewrite( round( $this->sunrise($when, SUNFUNCS_RET_TIMESTAMP) + (0.5 * $this->daylength($when, SUNFUNCS_RET_TIMESTAMP)) ) , SUNFUNCS_RET_TIMESTAMP, $mode);
	}
	function date_sun_info($when=NULL /*, $longitude=NULL, $latitude=NULL */){
		if(is_null($when)){ $when = $this->today; }
		return date_sun_info($when, $this->longitude, $this->latitude);
	}
	private function _date_sun_info_get($key, $when=NULL){
		$dsi = $this->date_sun_info($when);
		if( isset($dsi[$key]) ){ return $dsi[$key]; }
		else { return FALSE; }
	}
	function set_twilight($twilight=FALSE){
		if($twilight === NULL){ $this->twilight = NULL; }
		elseif(preg_match("#^(civil|nautical|astronomical)$#", $twilight)){ $this->twilight = strtolower($twilight); }
		else{ return FALSE; }
	}
	private function _get_twilight_alias($item, $twilight=FALSE){
		if($twilight === FALSE){ $twilight = $this->twilight; }
		if(preg_match("#^(civil|nautical|astronomical)_twilight_(begin|end)$#i", $item)){ return strtolower($item); }
		switch(strtolower($item)){
			case 'transit': return strtolower($item); break;
			case 'sunset': case 'sunrise':
				if( $twilight !== NULL ){
					return $twilight.'_twilight_'.(strtolower($item) == 'sunrise' ? 'begin' : 'end');
				}
				else{ return strtolower($item); }
			break;
			default:
				return FALSE;
		}		
	}
	private function _sunfuncs_ret_rewrite($value, $input=SUNFUNCS_RET_DOUBLE, $output=SUNFUNCS_RET_DOUBLE){
		$daystart = mktime(0,0,0,date('m', $this->today),date('d', $this->today),date('Y', $this->today));
		if($input === $output){ return $value; }
		switch($input){
			case SUNFUNCS_RET_DOUBLE :
				if(is_double($value) && $value >= 0.0 && $value < 24.0){
					$value = ($value * (60*60)) + $daystart;
				} else { return /**/ FALSE; }
				break;
			case SUNFUNCS_RET_STRING :
				if(preg_match("#^([0-1]?[0-9]|2[0-3])[:]([0-5][0-9])$#", $value, $buffer)){
					$value = mktime($buffer[1],$buffer[2],0,date('m', $this->today),date('d', $this->today),date('Y', $this->today));
				} else { return /*error*/ FALSE; }
				break;
			case SUNFUNCS_RET_TIMESTAMP :
				$value = $value; #already a timestamp
				break;
			default:
				return /*error*/ FALSE;
		}
		switch($output){
			case SUNFUNCS_RET_DOUBLE : return date("H:i", $value); break;
			case SUNFUNCS_RET_STRING : return (($value - $daystart) / (60*60)); break;
			case SUNFUNCS_RET_TIMESTAMP : return $value; break;
			default: return /*error*/ FALSE;
		}
		break;
	}
}
?>
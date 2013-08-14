<?php
class SunAlmanak {
	private $longitude;
	private $latitude;
	private $zenith = array();
	private $today;
	private $twilight = NULL;
	
	function __construct( $pdate=NULL ){
		if(is_null($pdate)){ $pdate = time(); }
		$this->today = $pdate;
		$this->longitude = ini_get("date.default_longitude");
		$this->latitude = ini_get("date.default_latitude");
		$this->zenith = array('sunrise' => ini_get("date.sunrise_zenith"), 'sunset' => ini_get("date.sunset_zenith") );
		
		$this->twilight = NULL; #(civil|nautical|astronomical|)
	}
	
	function sunrise($when=NULL, $mode=SUNFUNCS_RET_STRING){
		if(is_null($when)){ $when = $this->today; }
		return date_sunrise($when, $mode, $this->latitude, $this->longitude, $this->zenith['sunrise'], (date("Z", $when)/(60*60)) );
	}
	function sunset($when=NULL, $mode=SUNFUNCS_RET_STRING){
		if(is_null($when)){ $when = $this->today; }
		return date_sunset($when, $mode, $this->latitude, $this->longitude, $this->zenith['sunset'], (date("Z", $when)/(60*60)) );
	}
	function daylength($when=NULL, $mode=SUNFUNCS_RET_STRING){
		if(is_null($when)){ $when = $this->today; }
		return $this->_sunfuncs_ret_rewrite( ( $this->date_sunset($when, SUNFUNCS_RET_TIMESTAMP) - $this->date_sunrise($when, SUNFUNCS_RET_TIMESTAMP) ) , SUNFUNCS_RET_TIMESTAMP, $mode);
	}
	function noon($when=NULL, $mode=SUNFUNCS_RET_STRING){
		if(is_null($when)){ $when = $this->today; }
		return $this->_sunfuncs_ret_rewrite( ( $this->sunrise($when, SUNFUNCS_RET_TIMESTAMP) + (0.5 * $this->daylength($when)) ) , SUNFUNCS_RET_TIMESTAMP, $mode);
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
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
	
	function sunrise($when=NULL, $mode=SUNFUNCS_RET_DOUBLE){
		if(is_null($when)){ $when = $this->today; }
		return date_sunrise($when, $mode, $this->latitude, $this->longitude, $this->zenith['sunrise'], (date("Z", $when)/(60*60)) );
	}
	function sunset($when=NULL, $mode=SUNFUNCS_RET_DOUBLE){
		if(is_null($when)){ $when = $this->today; }
		return date_sunset($when, $mode, $this->latitude, $this->longitude, $this->zenith['sunset'], (date("Z", $when)/(60*60)) );
	}
}
?>
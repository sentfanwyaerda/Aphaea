<?php
require_once(dirname(__FILE__)."/php-sun-almanak.php");

$datestring = "Y-m-d H:i:s O";
$sa = new SunAlmanak();

print '<pre>';

#date_default_timezone_set('Europe/Amsterdam');
$sa->set_location("51&deg;50'N, 5&deg;52'E"); #Nijmegen
#$sa->set_location("52&deg;22'N, 4&deg;54'O"); #Amsterdam
#$sa->set_location("51&deg; 31' NB, 0&deg; 7' WL"); #Londen
#$sa->set_location(0.0,0.0);

$sa->set_twilight('civil');

print_r($sa);
foreach(array('daystart'=>"Daystart:",'sunrise'=>"Sunrise:",'sunset'=>"Sunset: ",'daylength'=>"Daylength:",'noon'=>"Noon:   ") as $key=>$value){
	$buffer = $sa->$key(NULL, SUNFUNCS_RET_TIMESTAMP);
	print $value."\t".$buffer."\t(".date($datestring, $buffer).")\n";
}
#print "Sunrise:\t".$sa->sunrise()."\n";
#print "Sunset: \t".$sa->sunset()."\n";
#print "Daylength:\t".$sa->daylength()."\n";
#print "Noon:   \t".$sa->noon()."\n";
print "Timezone:\t".date_default_timezone_get()."\n";
print "Now:    \t".date('U')." \t(".date($datestring).")\n";
print "Sun Info:\t\n";
foreach($sa->date_sun_info() as $key=>$value){
	print "\t".$key.": \t".$value." \t(".date($datestring, $value).")\n";
}
print '</pre>';
?>
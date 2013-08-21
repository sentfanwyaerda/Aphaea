<?php
require_once(dirname(__FILE__)."/php-moon-phase/moon-phase.php");

$datestring = "Y-m-d H:i:s O";
$mp = new MoonPhase();


print '<pre>';

print_r($mp);

#''=>"",

foreach(array('new_moon'=>"New Moon:   ",'first_quarter'=>"First Quarter:",'full_moon'=>"Full Moon:  ",'last_quarter'=>"Last Quarter: ",'next_new_moon'=>"Next new Moon:") as $key=>$value){
	$buffer = $mp->$key();
	print $value."\t".$buffer."\t(".date($datestring, $buffer).")\n";
}
print "Phase:      \t".$mp->phase()."\n";
print "Illumination:\t".($mp->illumination()*100)."%\n";
print '</pre>';
?>
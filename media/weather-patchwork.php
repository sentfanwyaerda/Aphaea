<?php
$with_remote = FALSE;
$width = 4*8;
$local = './';
$remote = 'http://img.meteogroup.com/typo3conf/ext/mg_locationweather/res/images/32px/';
$db = array();

for($g=0;$g<=9;$g++){ $db[] = 'weather/30000'.$g;}
for($w=-2;$w<=15;$w++){ $db[] = 'compass/./'.$w; }

for($a=1;$a<=3;$a++){
for($b=0;$b<=3;$b++){
for($c=0;$c<=3;$c++){
for($d=0;$d<=3;$d++){
for($e=0;$e<=3;$e++){
for($f=0;$f<=1;$f++){
	$db[] = 'weather/'.$a.$b.$c.$d.$e.$f;
}}}}}}


$i=0; $previous = './other/000000'; $counter = 0;
print "<style>\ntd { width: 32px; height: 0px; margin: 0; padding: 0; font-size: 5pt; color: #FFF; text-align: center; }\n.color-white { }\n.color-1 { background-color: #FBB; }\n.color-2 { background-color: #BFB; }\n.color-3 { background-color: #BBF; }\n.border-0 { border: 0px solid #FFF;}\n.border-1 { border: 1px solid #AAA;}\n.border-2 { border: 1px solid #555;}\n.border-3 { border: 2px solid #000; }\n.border-weather {border: 1px solid #DDD;}\n</style>";
print "<table>\n";
print "<tr>\n"; for($z=0;$z<$width;$z++){ print "\t<th>".($z < 16 ? '0' : NULL).base_convert($z, 10, 16)."</th>\n"; } print "</tr>\n";
foreach($db as $item){
	if($item{8} != $previous{8} ){ print "</tr>\n<tr>\n"; $i=0; }
	print "\t";
	if(file_exists("./".$item.".png")){ print '<td class="color-white border-weather">'.'<img src="'.$local.$item.'.png" border="1" style="border: 1px solid white;" alt="'.preg_replace("#^(.*)[/]([^/]+)$#", "\\2", $item).'" title="'.preg_replace("#^(.*)[/]([^/]+)$#", "\\2", $item).'" />'; $counter++; }
	else { print '<td class="color-'.$item{8}.' border-'.$item{9}.' empty">'.substr($item, -6); }
	if($with_remote === TRUE) print '<img src="'.$remote.substr($item, -6).'.png" border="1" style="border: 1px solid gold;" />';
	print '</td>'."\n";
	if(!(($i+1) % $width)){ print "</tr>\n<tr>\n"; }
	$previous = $item;
	$i++;
}
print "</table>";
print "<br/><strong>search for:</strong> ".count($db)." generated weather types, only ".$counter." exist.";
?>
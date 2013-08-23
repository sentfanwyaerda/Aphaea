<?php
require_once(dirname(__FILE__)."/weather.php");

/* #TESTING# */
$library = array(); #dirname(dirname(__FILE__));
$library[] = './../media/icons/climacons/';
$library[] = './../media/icons/compass/';
$library[] = './../media/icons/weather/32px/';
$library[] = './../media/icons/weather/64px/';
$library[] = './../media/icons/weezle-d3stroy';
$library[] = './../media/icons/magic-weather-iconka/48x48'; #/48x48 /128x128 /256x256
$library[] = './../media/icons/magic-weather-iconka/128x128'; #/48x48 /128x128 /256x256
$library[] = './../media/icons/magic-weather-iconka/256x256'; #/48x48 /128x128 /256x256
$library[] = './../media/icons/weather-kidaubis';
$library[] = './../media/icons/weather-umutavci';
print '<form method="GET"><input style="width: 60px;" maxlength="6" name="code" value="'.(isset($_GET['code']) ? $_GET['code'] : '121001').'" /><select name="library">';
foreach($library as $i=>$lib){ if(file_exists($lib) && is_dir($lib)){ print '<option value="'.$i.'"'.(isset($_GET['library']) && $_GET['library'] == $i ? ' selected="true"' : NULL).'>'.(preg_match("#^([0-9]+px|[0-9]+x[0-9]+)$#", basename($lib)) ? basename(dirname($lib)).' ('.basename($lib).')' : basename($lib)).'</option>'; } }
print '</select><input type="submit" value="&rarr;"/></form>';
$liburl = (isset($_GET['library']) ? $library[$_GET['library']] : $library[0]);
print '<pre>';
$weather = new weather();
$weather->load_icon_set($liburl); 

$icon = $weather->get_icon_url((isset($_GET['code']) ? $_GET['code'] : '121001'));

print '#SET: '.$liburl."\n";
print '#ICON: '.$icon."\n".' <img src="'.$liburl.'/'.$icon.'" />'."\n\n";

print $weather->debug;

print_r($weather);

print '</pre>';
?>
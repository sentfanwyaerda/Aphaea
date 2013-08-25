<?php
require_once(dirname(dirname(__FILE__)).'/library/math/weather.php');
/*# /tool/get_weather_icon.php?theme=()&code=()&output=(original*|json)&size=(original|[0-9]+px)  */

//*debug*/ $_GET['output'] = 'JSON';
$default_code = '121000';

#analyse /media/icons/iconpacks.json

$theme = (isset($_GET["theme"]) ? $_GET["theme"] : (isset($_GET["iconpack"]) ? $_GET["iconpack"] : 'weather/64px'));
$liburl = dirname(dirname(__FILE__)).'/media/icons/'.$theme.'/';

if(!file_exists($liburl) || !is_dir($liburl) || !file_exists($liburl.'weather.alias')){
	print 'ERROR: '.$theme.' not found or valid!'; exit;
}

$code = (isset($_GET['code']) ? $_GET['code'] : $default_code);
$score = NULL;

$weather = new weather();
$weather->load_icon_set($liburl); 
$icon = $weather->get_icon_url($code);
$score = $weather->get_last_icon_score();
$file = '/media/icons/'.$theme.'/'.$icon;



$output_mode = (isset($_GET['output']) ? $_GET['output'] : 'original');
switch(strtolower($output_mode)){
	case 'json':
		print '{"icon": "'.$icon.'", "iconpack": "'.$theme.'", "code": '.$code.', "score": "'.$score.'", "src": "'.$file.'"}'; exit;
		break;
	default:
		header("Content-type: ".(preg_match("#.png$#", $file) ? 'image/png' : 'image/svg'));
		print file_get_contents(dirname(dirname(__FILE__)).$file); exit;
}
?>
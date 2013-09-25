<?php
$debug = FALSE;
if(!class_exists('Morpheus')){
	require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/Morpheus/Morpheus.php');
}

//defaults:
$source = 'weer.nl';
$selection = 'nijmegen';
$date = (isset($_GET['date']) ? $_GET['date'] : date('Y-m-d') );
$hour = (isset($_GET['hour']) ? $_GET['hour'] : date('H') );

$file = './'.$source.'-'.$selection.'['.$date.']['.$hour.'].json';

//JSON Selector

//JSON Reader

$raw = file_get_contents($file);
$json = json_decode($raw, TRUE);

//Output
$template_src = dirname(dirname(dirname(__FILE__))).'/media/html/weer.nl-weekprognoses-template.html';
# if($debug === TRUE){ print $template_src."\n<br/>\n"; }
$str = file_get_contents($template_src);
# if($debug === TRUE){ print $str."\n<br/>\n"; }

#$str = Morpheus::basic_parse_str($str, $json['today']);

function json_parse_translate($json, $prefix=NULL){
	$set = array('Aphaea:root'=>'/development/Aphaea','Aphaea:theme'=>'weather/64px'); #weather/64px #weather-umutavci
	foreach($json as $tag=>$val){
		switch(strtolower($tag)){
			case 'today':
				if(is_array($val)){
					$set = array_merge($set, $val);
				}
				break;
			default:
				if(is_array($val)){
					$set = array_merge($set, json_parse_translate($val, $prefix.$tag.':'));
				} else {
					$set[$prefix.$tag] = $val;
				}
		}
	}
	return $set;
}
$set = json_parse_translate($json);
#if($debug === TRUE){ print '<pre>'; print_r($set); print "</pre>\n<br/>\n"; }
$str = Morpheus::basic_parse_str($str, $set);

print $str;

//Debug
if($debug === TRUE){
	print '<pre style="block: table;">';
	print_r($json);
	print '</pre>';
}
?>

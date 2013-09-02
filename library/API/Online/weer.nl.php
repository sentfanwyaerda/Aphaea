<?php
$source = 'http://www.weer.nl/wereldweer/actueel-en-verwacht-weer/stad/31X2902/nijmegen.html';
$temp = dirname(__FILE__).'/'.basename(__FILE__, '.php').'-'.basename($source, ".html").'.tmp';

function unlink_string($str){
	if( preg_match("#<a([^>]+)>([^¤]+)¤#", str_replace('</a>', '¤', $str), $buffer) ){
		$str = str_replace($buffer[0], $buffer[2], str_replace('</a>', '¤', $str));
		$str = str_replace('¤', '</a>', $str);
		return unlink_string($str);
	}
	else{ return $str; }
}



/*debug*/ print '<pre>';

if(!file_exists($temp) || filemtime($temp) < time()-(0.95*60*60) ){
	file_put_contents($temp, file_get_contents($source));
}
$line = file($temp);

#$line = explode("\n", file_get_contents($source));

$str = NULL;
$db = array();
$db["today"]["date"] = date("d.m.Y");


$start = count($line); //find start: '<div class="tx-mglocationweather-pi1">' @397 .... '</div>' @709
for($i=1; $i<=count($line); $i++){
	if(preg_match('#<div class="tx-mglocationweather-pi1">#', trim($line[$i-1]))){ $str = $line[$i-1]; $start = $i; }
	if(($start < $i && $str != NULL && $i < $start + (709-397))){
		$str .= $line[$i-1];
	}
}
$table = explode('</table>', $str);

foreach($table as $row=>$section){
	//*debug*/ print '<strong>['.$i."]</strong>: \n";
	switch($row){
		case 2: case 5:
			preg_match_all("#<(div)([^>]{0,})>([^¤]+)¤#i", str_replace('</div>', '¤', $section), $buffer);
			break;
		case 4:
			preg_match_all("#<(img) (src)=\"([^\"]{0,})\"([^>]+)>#i", $section, $buffer);
			break;
		default:
			preg_match_all("#<(p)([^>]{0,})>([^¤]+)¤#i", str_replace('</p>', '¤', $section), $buffer);
	}
	foreach($buffer[3] as $j=>$value){
		switch($row){
			case 0:
				$alias = array('city','station','weather-code','temperature','wind-strength','airpressure','2b'=>'weather-description','2c'=>'timestamp');
				if( $j == 2 ){
					preg_match("#([0-9]+).png\" alt=\"([^\"]+)\"#i", unlink_string($value), $buff);
					$db["today"][$alias[$j]] = $buff[1];
					$db["today"][$alias['2b']] = substr($buff[2], 0, -20);
					$db["today"][$alias['2c']] = substr($buff[2], -19);
				}
				else{
					$db["today"][$alias[$j]] = unlink_string($value);
				}
				break;
			case 1:
				$x = round( (($j+1) / 2));
				$db[$x][($x == (($j+1) / 2) ? "date" : "dayname")] = unlink_string($value);
				break;
			case 2:
				preg_match("#([0-9]+).png\" alt=\"([^\"]+)\"#i", unlink_string($value), $buff);
				$x = round( (($j+1) / 2));
				$db[$x][($x == (($j+1) / 2) ? "day-phase" : "night-phase")]["weather-code"] = $buff[1];
				$db[$x][($x == (($j+1) / 2) ? "day-phase" : "night-phase")]["weather-description"] = $buff[2];
				break;
			case 3:
				$x = round( (($j+1) / 2));
				$db[$x][($x == (($j+1) / 2) ? "day-phase" : "night-phase")]["temperature"] = unlink_string($value);
				break;
			case 4:
				$db["today"]["temperature-graph"] = $value;
				break;
			case 5:
				preg_match("#([0-9]+).png\" alt=\"([^\"]+)\"#i", unlink_string($value), $buff);
				$x = round( (($j+1) / 2));
				$db[$x][($x == (($j+1) / 2) ? "day-phase" : "night-phase")]["wind-code"] = $buff[1];
				$db[$x][($x == (($j+1) / 2) ? "day-phase" : "night-phase")]["wind-description"] = $buff[2];
				break;
			case 6:
				$x = round( (($j+1) / 2));
				$db[$x][($x == (($j+1) / 2) ? "day-phase" : "night-phase")]["wind-strength"] = unlink_string($value);
				break;
			case 7:
				preg_match("#([0-9,.]+)mm<br />([0-9]+)%#", unlink_string($value), $buff);
				$x = round( (($j+1) / 2));
				$db[$x][($x == (($j+1) / 2) ? "day-phase" : "night-phase")]["rain-amount"] = str_replace(',', '.', $buff[1]);
				$db[$x][($x == (($j+1) / 2) ? "day-phase" : "night-phase")]["rain-chance"] = ($buff[2] /100);
				break;
			case 8:
				$x = round( (($j+2) / 3));
				switch($j - (($x-1)*3)){
					case 0: $db[$x]['sunshine'] = preg_replace("#<img[^>]+>#", "", unlink_string($value)); break;
					case 1: $db[$x]['sunrise'] = preg_replace("#<img[^>]+>#", "", unlink_string($value)); break;
					case 2: $db[$x]['sunset'] = preg_replace("#<img[^>]+>#", "", unlink_string($value)); break;
				}
				break;
			default:
				/*ignore*/
		}
		
		//*degug*/ print "\t(".$i.':'.$j.(isset($buffer[1][$j]) && preg_match("#class=\"([^\"]+)\"#", $buffer[2][$j], $class) ? ' '.$class[1] : NULL).(isset($buffer[1][$j]) && preg_match("#title=\"([^\"]+)\"#", $buffer[2][$j], $title) ? ' "<em>'.$title[1].'</em>"' : NULL).")\t".htmlspecialchars(unlink_string($value))."\n";
	}
	if($row == 0){ #fix 0:4
		preg_match("#<div class=\"head_wind\"><img([^>]+)>#", $section, $buffel);
		preg_match("#([-]?[0-9]{0,2}).png\" alt=\"([^\"]+)\"#i", $buffel[1], $buff);
		$db["today"]["wind-code"] = $buff[1];
		$db["today"]["wind-description"] = $buff[2];
	}
}

//*debug*/ print_r($db);

$json = json_encode($db, JSON_PRETTY_PRINT);
$dbfile = dirname(dirname(dirname(dirname(__FILE__)))).'/db/online/'.basename(__FILE__, '.php').'-'.basename($source, ".html").'['.date('Y-m-d').']['.date('H').'].json';
file_put_contents($dbfile, $json);
print 'file: '.$dbfile."\n\n";
print $json;
/*debug*/ print '</pre>';
?>
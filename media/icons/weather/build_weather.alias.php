<?php
$db = array();

$list = scandir('./');
foreach($list as $i=>$file){
	if(preg_match("#^([0-9]{6}).png$#", $file, $buffer)){
		$tags = array();
		if($buffer[1]{0} == '1'){ $tags[] = "sun"; }
		if($buffer[1]{0} == '2'){ $tags[] = "moon"; }
		#if($buffer[1]{0} == '3'){ $tags[] = "cloud"; }
		//#with
		if($buffer[1]{1} == '1'){ $tags[] = "minimal"; $tags[] = "clouds"; }
		if($buffer[1]{1} == '2'){ $tags[] = "medium"; $tags[] = "clouds"; }
		if($buffer[1]{1} == '3'){ $tags[] = "maximal"; $tags[] = "clouds"; }
		//#with
		if($buffer[1]{2} != '0'){ $tags[] = "rain"; }
		if($buffer[1]{3} != '0'){ $tags[] = "snow"; }
		if($buffer[1]{3} == '3'){ $tags[] = "flurry"; }
		if($buffer[1]{4} != '0'){ $tags[] = "ice"; $tags[] = "hail"; }
		//#and
		if($buffer[1]{5} == '1'){ $tags[] = "thunder"; }
		if($buffer[1]{5} == '2'){ $tags[] = "warning"; }
		if($buffer[1]{5} == '3'){ $tags[] = "fog"; }
		if($buffer[1]{5} == '4'){ $tags[] = "storm"; }
		if($buffer[1]{5} == '5'){ $tags[] = "slippery"; }
		if($buffer[1]{5} == '6'){ $tags[] = "smog"; }
		$db[] = array("code"=>$buffer[1], "src"=>$file,"tag"=>$tags);
	}
}

$json = json_encode($db);
$json = str_replace("},{", "},\n{", $json);
$json = "[\n".substr($json, 1, -1)."\n]";
header("Content-Type: text/plain");
print $json;
?>

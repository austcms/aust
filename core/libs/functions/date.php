<?php
function dateName($date, $today = 'hoje', $yesterday = 'ontem', $tomorrow = 'amanhã', $format = "d/m/Y H:i"){
	
	
	$todayDate = strtotime(date('M j, Y'));
	$date = strtotime($date);

	$reldays = ($date - $todayDate)/86400;
	$hour = date("H:i", $date);

	if ($reldays >= 0 && $reldays < 1) {
		$result = $today.' '.$hour;
	} else if ($reldays >= -1 && $reldays < 0) {
		$result = $yesterday.' '.$hour;
	} else if ($reldays >= 1 && $reldays < 2) {
		$result = $tomorrow.' '.$hour;
	} else {
		$date = date($format, $date);
		$result = $date;
	}
	
	
	return $result;
	
	
}


?>
<?php
function dateName($date, $today = 'hoje', $yesterday = 'ontem', $tomorrow = 'amanhã', $format = "d/m/Y H:i"){
	
	
	$todayDate = strtotime(date('M j, Y'));
	$date = strtotime($date);

	$reldays = ($date - $todayDate)/86400;
	if ($reldays >= 0 && $reldays < 1) {
		$result = $today;
	} else if ($reldays >= -1 && $reldays < 0) {
		$result = $yesterday;
	} else if ($reldays >= 1 && $reldays < 2) {
		$result = $tomorrow;
	} else {
		$date = date($format, $date);
		$result = $date;
	}
	
	
	return $result;
	
	
}


?>
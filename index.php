<?php
/*Created by Aidan Taylor - www.aidantaylor.net*/

require_once("includes/global.inc.php");//include global

ob_start();

function time_stamp($date, $time = false){
	if (empty($date)){
		return "No date provided";//return if date empty
	}
	
	$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");//set period names
	$lengths = array("60","60","24","7","4.35","12","10");//set period lenghs
	
	if (is_array($date)) {
		$now = $date[0];//set now from date array
		$date = $date[1];//set date from date array
	} else {
		$now = time();//set the current time
	}
	
	if ($time === true) {
		$unix_date = $date;//if time is set, set the unix date
	} else {
		$unix_date = strtotime($date);//if no time set, make the date a unix date
	}
	
	if (empty($unix_date)){
		return "Bad date";//if no unix date/unix date empty, return
	}
	
	$tense = "";//define tense
	
	if ($now > $unix_date){//detect now less then unix date
		$difference = $now - $unix_date;//calculate difference
	} else {
		$difference = $unix_date - $now;//calculate difference
    }
	
	for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++){//go through each lengh with the difference
		$difference /= $lengths[$j];//devide the difference by the length required
	}
	
	$difference = round($difference);//round difference
	
	if ($difference != 1){
		$periods[$j] .= "s";//if difference is not plural add s
	}
	
	return "$difference $periods[$j] $tense";//return formatted date
}

/*html output start*/
	if ($config->no_html === false) {
		echo "<div style=\"position:fixed;left:512px;background:#fff;padding:15px;\">";
			echo "<h3>Cache data for:</h3>";
			
			echo "<form method=\"post\" id=\"select_server\">";
				echo "<select name=\"minecraft_server\" onChange=\"document.getElementById('select_server').submit()\">";
				
				foreach($config->minecraft_servers as $key => $value) {//loop through each server and echo option for the select
					echo "<option value=\"{$key}\"" . (($key == $_SESSION['minecraft_server']) ? "selected=\"selected\"" : "") . ">";
						echo "{$value['minecraft_host']}:{$value['minecraft_port']} ({$key})";
					echo "</option>";
				}
				
				echo "</select>";
			echo "</form>";
		echo "</div>";
	}

	require_once("includes/getaverages.inc.php");//include averages
	
	if (isset($startdate)) {//detect data existance
		//echo formatted information
		echo "<div style=\"width:512px;font-size:14px;\">\n";
			echo "<span style=\"font-size:16px;\">Current Player Cache<br />\n</span>";
			echo "From: " . date('Y-m-d H:i:s', $startdate) . "<br />\n";
			echo "End: when the script loaded (" . date('Y-m-d H:i:s', $mysqldate) . ")<br />\n";
			echo "Time Diff: " . time_stamp(array($startdate, $mysqldate), true) . "<br /><br />\n\n";
			echo "Average Online: " . round($avonline) . "<br />\n";
			echo "Most Online: {$mostonline}<br />\n";
			echo "Least Online: {$leastonline}<br />\n";
			echo "Unique Players: {$uniqueplayers}<br /><br />\n";
			echo " <span style=\"font-size:12px;\">{$players}</span>\n";
		echo "</div>\n<br />\n<br />\n";
	}
	
$query = $sql->database->query("SELECT * FROM `{$config->sql_table_prefix}averages` WHERE `server` = '{$config->minecraft_selected}' ORDER BY `date` DESC");//read older cache data

if ($sql->database->num($query) > 0) {//detect older cache data and echo formatted information
	echo "<h3>Older Cache Data:<br />\n<br />\n</h3>";
	
	while($arr = $sql->database->arr($query)) {//loop the query array
		$players = implode(", ", array_filter(explode(",", $arr['players'])));//filter the players and add a spacer then save string
		
		echo "<div style=\"width:512px;font-size:14px;\">\n";
			echo "<span style=\"font-size:16px;\">Player Cache<br />\n</span>";
			echo "From: " . date('Y-m-d H:i:s', $arr['startdate']) . "<br />\n";
			echo "End: " . date('Y-m-d H:i:s', $arr['enddate']) . "<br />\n";
			echo "Time Diff: " . time_stamp(array($arr['startdate'], $arr['enddate']), true) . "<br /><br />\n\n";
			echo "Average Online: {$arr['averageonline']}<br />\n";
			echo "Most Online: {$arr['mostonline']}<br />\n";
			echo "Least Online: {$arr['leastonline']}<br />\n";
			echo "Unique Players: {$arr['uniqueplayers']}<br /><br />\n";
			echo " <span style=\"font-size:12px;\">{$players}</span>\n";
		echo "</div>\n<br /><br />\n";
	}
}
/*html output end*/

if ($config->no_html) {
	$ob_content = strip_tags(str_replace("<br />", "\n", str_replace("\n", "", ob_get_contents())));
} else {
	$ob_content = ob_get_contents();
}

ob_end_clean();

echo $ob_content;
?>
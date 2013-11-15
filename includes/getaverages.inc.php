<?php
/*Created by Aidan Taylor - www.aidantaylor.net*/

/*Get player averages*/
$query2 = $sql->database->query("SELECT * FROM `{$config->sql_table_prefix}averages` WHERE `server` = '{$config->minecraft_selected}' ORDER BY `date` DESC LIMIT 1");

$data2 = $sql->database->arr($query2);//make the averages query into data

if (!isset($enddate)) {
	$enddate = $data2['enddate'];//set end date if none exists
}

if (isset($startdate)) {
	$query = $sql->database->query("SELECT * FROM `{$config->sql_table_prefix}cache` WHERE `date` >= '{$startdate}' AND  `date` <= '{$enddate}' AND `server` = '{$config->minecraft_selected}' ORDER BY `date` ASC");//select the range of cache data between $startdate and $enddate
} else {
	$query = $sql->database->query("SELECT * FROM `{$config->sql_table_prefix}cache` WHERE `date` >= '{$enddate}' AND `server` = '{$config->minecraft_selected}' ORDER BY `date` ASC");//select the range of cache data from the end date to the end of the database
}

if ($sql->database->num($query) <= 0) {
	echo 'No entries found!';//display error if no entries are found
} else {
	//define some defaults
	$date = array();
	$player = array();
	$maxplayers = 0;
	$totalonline = 0;
	$mostonline = 0;
	$leastonline = 9999;
	$i = 0;
	
	while($row = $sql->database->arr($query)) {//loop through results
		if ($row['offline'] == 0) {
			$players = array_filter(explode(",", $row['players']));//get players as array
			
			if ($i == 0) {
				$startid = $row['id'];//define start row
				$startdate = $row['date'];//define start date
			}
			
			foreach($players as $key2 => $value2) {//loop players
				if (!isset($player[$value2])) {
					$player[$value2] = $value2;//only save player into $player array if they're not there already
				}
			}
			
			$totalonline += $row['onlineplayers'];//add total online to the total online count
			
			if ($row['onlineplayers'] < $leastonline) {
				$leastonline = $row['onlineplayers'];//set least online if online players is less then the current least online
			}
			
			if ($row['onlineplayers'] > $mostonline) {
				$mostonline = $row['onlineplayers'];//set most online if online players is more then the current most online
			}
			
			if ($row['maxplayers'] > $maxplayers) {
				$maxplayers = $row['maxplayers'];//set the max players if online players is more then the current maximum online
			}
			
			$data[] = $row;//add the cache row to the data array
			
			$i++;
		}
	}
	
	if (!isset($data)) {
		unset($startdate);
	} else {
		$endid = $data[count($data) - 1]['id'];//set the end id
		$enddate = $data[count($data) - 1]['date'];//set the end date
		
		$phpdate = date('Y-m-d H:i:s');//get the current date
		$mysqldate = strtotime($phpdate);//make the date into mysqltime
		
		$uniqueplayers = count($player);//count the unique players
		$players = implode(",", $player);//make the player array into a string
		$avonline = $totalonline / $i;//detect the average online (total online divided by the amount of cache rows)
	}
}
?>
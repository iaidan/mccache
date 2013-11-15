<?php
/*Created by Aidan Taylor - www.aidantaylor.net*/

require_once("global.inc.php");//include global

if (PHP_SAPI === 'cli' || isset($argv)) {//detect console arguements
	if (!isset($config->minecraft_servers[$argv[0]])) {//check if server exists
		goto a;
	} else {
		averages($argv[0]);//Calculate server averages if exists
	}
} else if ($config->cmd_cache === true) {
	die("Access Denied.");
} else if (isset($_REQUEST['minecraft_server'])) {
	averages();//Calculate server averages (global will read the REQUEST)
} else {
	a:
	foreach($config->minecraft_servers as $key => $value) {//loop servers
		echo "<br /><br />Attempting averages for {$key}...<br />";
		
		averages($key);//Calculate server averages
	}
}

function averages($server = false) {
	global $config;
	global $sql;
	
	if ($server !== false) {
		$_SESSION['minecraft_server'] = $server;//set bew server
		update_server();//update server information
	}
	
	include("getaverages.inc.php");//include averages from db
	
	echo "Attempting to calculate averages for {$_SESSION['minecraft_server']}...";
	
	if ($sql->database->query("INSERT INTO `{$config->sql_table_prefix}averages` (`server`, `date`, `startdate`, `enddate`, `startid`, `endid`, `averageonline`, `mostonline`, `leastonline`, `players`, `maxplayers`, `uniqueplayers`) VALUES ('{$config->minecraft_selected}', '{$mysqldate}', '{$startdate}', '{$enddate}', '{$startid}', '{$endid}', '{$avonline}', '{$mostonline}', '{$leastonline}', '{$players}', '{$maxplayers}', '{$uniqueplayers}')")) {//insert latest averages into database
		$from = date('Y-m-d H:i:s', $startdate);//format date
		$to = date('Y-m-d H:i:s', $enddate);//format date
		echo "<pre>{$players}\n\nFrom: {$from}\nTo: {$to}</pre>";//print from - to
	} else  {
		die("MYSQL Error: " . mysql_error());//die and print error
	}
}
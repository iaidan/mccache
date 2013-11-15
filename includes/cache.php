<?php
/*Created by Aidan Taylor - www.aidantaylor.net*/

require_once("global.inc.php");//include global

if (PHP_SAPI === 'cli' || isset($argv)) {//detect console arguements
	if (!isset($config->minecraft_servers[$argv[0]])) {//check if server exists
		goto a;
	} else {
		cache($argv[0]);//cache if exists
	}
} else if ($config->cmd_cache === true) {
	die("Access Denied.");
} else if (isset($_REQUEST['minecraft_server'])) {
	cache();//cache (global will read the REQUEST)
} else {
	a:
	foreach($config->minecraft_servers as $key => $value) {//loop servers
		echo "<br /><br />Attempting cache for {$key}...<br />";
		
		cache($key);//cache server
	}
}

function cache($server = false) {
	global $minecraft;
	global $config;
	global $sql;
	
	if ($server !== false) {
		$_SESSION['minecraft_server'] = $server;//set bew server
		update_server();//update server information
	}
	
	$phpdate = date('Y-m-d H:i:s');//set date
	$mysqldate = strtotime($phpdate);//set date into mysql format
	
	if ($minecraft->offline === true) {//detect offline server
		if ($sql->database->query("INSERT INTO `{$config->sql_table_prefix}cache` (`server`, `date`, `offline`) VALUES ('{$config->minecraft_selected}', '{$mysqldate}', 1)")) {//inset into database that the server is offline
			echo "Server is offline!";//if mysql query was successful, end script and print
		} else {
			die("MYSQL Error: " . mysql_error());//if mysql query was unseccessful, enc script and print error
		}
	} else {
		if ($minecraft->GetPlayers() != '') {//detect no players (false return or empty)
			$players = implode(",", array_filter($minecraft->GetPlayers()));//make the players array filtered string
		} else {
			$players = '';//set players to nothing
		}
		
		if ($sql->database->query("INSERT INTO `{$config->sql_table_prefix}cache` (`server`, `date`, `players`, `maxplayers`, `onlineplayers`, `offline`) VALUES ('{$config->minecraft_selected}', {$mysqldate}, '{$players}', {$minecraft->MaxPlayers}, {$minecraft->Players}, 0)")) {//insert cache data
			echo $players;//echo the players online currently
		} else {
			die("MYSQL Error: " . mysql_error());//die and print error
		}
	}
}
?>
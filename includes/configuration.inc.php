<?php
class config {
	/*Minecraft server details*/
	var $minecraft_selected = 'Server 1';//the server that will be displayed initially when the index.php is loaded
	var $minecraft_servers  = array(
		'Server 1' => array(
			'minecraft_host'       => "mc.server1.net",//minecraft host (no port)
			'minecraft_port'       => 25565,//minecraft port
			'minecraft_query'      => true,//minecraft query (has to be enabled in server.properties)
			'minecraft_query_port' => 25565//minecraft port
		),
		'Server 2' => array(
			'minecraft_host'       => "mc.server2.net",//minecraft host (no port)
			'minecraft_port'       => 25565,//minecraft port
			'minecraft_query'      => true,//minecraft query (has to be enabled in server.properties)
			'minecraft_query_port' => 25565//minecraft port
		)
	);
	
	/*caching settings*/
	var $cmd_cache = false;//only allow access to cache.php and averages.php from console (cron)
	var $no_html   = false;//disable html output
	
	/*MySQL server details*/
	var $sql_host         = "localhost";//mysql host
	var $sql_user         = "root";//mysql username
	var $sql_pass         = "";//mysql password
	var $sql_database     = "mcCache";//database name
	var $sql_table_prefix = "mcc_";
}
?>
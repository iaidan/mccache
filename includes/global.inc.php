<?php
/*Created by Aidan Taylor - www.aidantaylor.net*/

@session_start();//start session

//session_destroy();

if (!defined("_Global")) {
	define("_Global", true) or die("Can't define _Global.");
}

if (!isset($config)) {
	require("configuration.inc.php");//include configuration
	$config = new config();//make config an object
}

/*mysql*/
require_once("sql.class.php");//include mysql class
$sql = new sql($config->sql_host, $config->sql_user, $config->sql_pass, $config->sql_database, $config->sql_table_prefix);//connect to mysql
$sql->database->select($config->sql_database);//select database

/*minecraft*/
function update_server(){
	global $config;
	global $minecraft;
	
	if (isset($argv[0])) {//detect console arguements
		if (!sset($config->minecraft_servers[$argv[0]])) {//check if server exists
			$_SESSION['minecraft_server'] = $argv[0];//set the option in session
			goto b;
		}
	}
	/*select default minecraft server (for mysql queries)*/
	if (isset($_REQUEST['minecraft_server'])) {//check for server change request
		$_SESSION['minecraft_server'] = $_REQUEST['minecraft_server'];//set the requested server in session
	}
	
	if (!isset($_SESSION['minecraft_server'])) {//check for selected server in session
		$_SESSION['minecraft_server'] = $config->minecraft_selected;//set the default server into session
	}
	
	if (!isset($config->minecraft_servers[$_SESSION['minecraft_server']])) {
		echo "The server {$_SESSION['minecraft_server']} was not found!";
		session_destroy();
		die();
	}
	
	b:
	
	$config->minecraft_selected = htmlspecialchars("{$config->minecraft_servers[$_SESSION['minecraft_server']]['minecraft_host']}:{$config->minecraft_servers[$_SESSION['minecraft_server']]['minecraft_port']}");//set the default minecraft server
	
	$minecraft = new Minecraft($config->minecraft_servers[$_SESSION['minecraft_server']]['minecraft_host'], $config->minecraft_servers[$_SESSION['minecraft_server']]['minecraft_port'], 2, $config->minecraft_servers[$_SESSION['minecraft_server']]['minecraft_query'], $config->minecraft_servers[$_SESSION['minecraft_server']]['minecraft_query_port']);
}

require_once("minecraft.class.php");
$minecraft = "";//define minecraft
update_server();
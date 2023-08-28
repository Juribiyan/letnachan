<?php
require_once 'config.php';

$db = null;

function connect_db() {
	global $db;

	if (!is_object($db)) {
		$db = new mysqli(DB_HOST, DB_USER, DB_PASSWD, DB_DATABASE);
		if ($db->connect_errno) {
		  die('Database error #' . $db->connect_errno );
		}
		$db->query("SET NAMES '".DB_CHARSET."'");
		$db->query("SET CHARACTER SET '".DB_CHARSET."'");
		$db->query("SET SESSION collation_connection = '".DB_COLLATION."'");
	}
	
	return $db;
}

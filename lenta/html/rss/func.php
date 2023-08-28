<?php
require_once '../db.php';
$db = connect_db();

function dbquery($query) {
	global $db;
	$result = @$db->query($query);
	if (!$result) {
		echo $db->error;
		return false;
	} else {
		return $result;
	}
}

function dbarray($query) {
	global $db;
	$result = @$db->query($query)->fetch_assoc();
	if (!$result) {
		echo $db->error;
		return false;
	} else {
		return $result;
	}
}

?>
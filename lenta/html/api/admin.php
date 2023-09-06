<?php
session_start();

require_once '../engine.php';
require_once '../inc/admin.php';

$li_URL = ROOT_URL;

if(CheckLoginS()) {
	$id = @$_GET['id'];
	if (!$id) {
		exit_err("Не указан ID поста.");
	}
	if (isset($_GET['edit'])) {
		if (! (isset($_POST['subject']) && isset($_POST['message']))) {
			exit_err("Поля не могут быть пустыми.");
		}
		$name = $_POST['subject'];
		$text = $db->real_escape_string($_POST['message']);
		$text2 = $db->real_escape_string($_POST['fullmessage']);
		$link = $_POST['link'];
		query_and_check("UPDATE `blog` SET `subject`='$name',`message`='$text',`fullmessage`='$text2',`link`='$link' WHERE `id` = $idPost",
			"Пост отредактирован.", "Нечего редактировать");
	}
	if (isset($_GET['del'])) {
		$db->query("DELETE FROM `blog` WHERE `id` = $id");
		if ($db->affected_rows) {
			clientBroadcast('global', 'del-entry', $id);
			exit_ok("Пост удален.");
		}
		else {
			exit_err("Нечего удобрять.");
		}
	}
	elseif (isset($_GET['real'])) {
		query_and_check("UPDATE `blog` SET `real`='1' WHERE `id` = $id", 
			"Пост удобрен.", "Нечего удобрять.");
	} 
	elseif(isset($_GET['unreal'])) {
		query_and_check("UPDATE `blog` SET `real`='0' WHERE `id` = $id", 
			"Пост разудобрен.", "Нечего разудобрять.");
	}
}

function query_and_check($query, $succ_msg="Успешно", $err_msg="Неуспешно") {
	global $db;
	$db->query($query);
	if ($db->affected_rows) {
		exit_ok($succ_msg);
	}
	else {
		exit_err($err_msg);
	}
}

function jexit($payload) {
	if (@strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		exit(json_encode($payload));
	}
	else {
		header("Location: $li_URL/panel?msg=".$payload['msg']);
	}
}

function exit_err($msg) {
	jexit([
		"error" => true,
		"msg" => $msg
	]);
}

function exit_ok($msg) {
	jexit([
		"error" => false,
		"msg" => $msg
	]);
}
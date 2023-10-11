<?php
session_start();

require_once '../engine.php';
require_once '../inc/admin.php';

$li_URL = ROOT_URL;

$auth = CheckLoginS();

if($auth) {
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
  elseif(USE_TELEGRAM) {
    $check_post = $db->query("SELECT `tg_user` FROM `blog` WHERE `id` = $id")->fetch_assoc();
    $user_id = @$check_post['tg_user'];
    if (!$user_id) {
      exit_err("Пост не связан с пользователем");
    }
    $user = $db->query("SELECT * FROM `tg_users` WHERE `id`=$user_id")->fetch_assoc();
    if (!$user) {
      exit_err("Пользователь не найден");
    }

    if (isset($_GET['ban'])) {
      if ($user['authority'] == 'admin') {
        exit_err("Недостаточно прав");
      }
      if ($user['banned_by']) {
        exit_err("Пользователь уже в бане");
      }
      $banned_by = $auth['id'];
      query_and_check("UPDATE `tg_users` SET `banned_by`=$banned_by WHERE `id` = $user_id", 
        "Бан установлен.", "Не удалось установить бан.");
    }

    elseif(isset($_GET['give_mod'])) {
      if ($auth['authority'] != 'admin') {
        exit_err("Недостаточно прав");
      }
      if ($user['banned_by']) {
        exit_err("Невозможно дать модерку: пользователь забанен");
      }
      if ($user['authority'] != 'user') {
        exit_err("Пользователь уже имеет модерку");
      }
      query_and_check("UPDATE `tg_users` SET `authority`='mod' WHERE `id` = $user_id", 
        "Модерка выдана.", "Не удалось выдать модерку.");
    }
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
<?php
// Функции все в inc/func
// Страницы в pages
// Пингвины в африке

// Соединение с высшим
/* error_reporting(E_ERROR | E_WARNING | E_PARSE);
ob_start();
echo "\n_POST:";
var_dump($_POST);
echo "_SESSION:";
var_dump($_SESSION);
echo "_COOKIE:";
var_dump($_COOKIE);
$contents = ob_get_contents();
ob_end_clean();

error_log($contents); */

require_once 'db.php';
$db = connect_db();

$papa = '10'; // Записей на страницу
$li_winnews = '';
// $pushserver = 'https://psh.lentachan.ru';

#Лимиты
$title_lim = "73";
function limitlines($string, $lines)
{
	$string = explode("\n", $string);
	array_splice($string, $lines);
	return implode("\n", $string);
}
function postError($title)
{
	exit(json_encode(array(
		'code' => '400',
		'response' => $title
	)));
}
function CheckLogin()
{
	if (@$_SESSION['user_login'] or @$_SESSION['user_name']) {
		return @true;
	} else {
		return @false;
	}
}
function errorMsg($string)
{
	switch ($string) {
		case 'nullnews':
			$string = '<div class="big-message">Записей не найдено</div>';
			break;
		default:
			$string = '<div class="big-message">Страница не найдена</div>';
	}
	return $string;
}
function whatcat($category)
{
	switch ($category) {
		case 'aib':
			$cate = 'Новости АИБ';
			break;
		case 'irl':
			$cate = 'Новости ИРЛ';
			break;
		case 'int':
			$cate = 'Новости Интернета';
			break;
		case 'all':
			$cate = 'Обсуждение';
			break;
	}
	return @$cate;
}
function countcoms($thread)
{
	global $db;
	
	$countcom = $db->query("SELECT COUNT(`id`) as cc FROM `blog` WHERE `parrent` = $thread")
	->fetch_assoc();
	return $countcom['cc'];
}

function clientBroadcast($channel, $event, $data=null) {
	$payload = [
		'channel' => $channel,
		'event' => $event,
		'token' => SOCKETIO_SRV_TOKEN
	];
	if ($data) {
		$payload['data'] = $data;
	}
	$data_json = json_encode($payload);

	$curl_session = curl_init(SOCKETIO_HOST . ':' . SOCKETIO_PORT . '/broadcast/');
	curl_setopt($curl_session, CURLOPT_PROXY, "");
	curl_setopt($curl_session, CURLOPT_POSTFIELDS, $data_json);
	curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl_session, CURLOPT_HTTPHEADER, [
		'Content-Type: application/json',
		'Content-Length: ' . strlen($data_json)
	]);
	curl_exec($curl_session);
	if (curl_errno($curl_session)) {
		die('cURL error: ' . curl_error($curl_session));
	}
}
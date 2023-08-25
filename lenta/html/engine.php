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

$csbl_db = mysql_connect('localhost', '', '');
mysql_select_db('lenta', $csbl_db);
mysql_query("SET NAMES 'utf8'");
mysql_query("SET CHARACTER SET 'utf8'");
mysql_query("SET SESSION collation_connection = 'utf8_unicode_ci'");
$li_URL = "https://lentachan.ru"; // Няшный адерс  # Прошу без слеша в конце
$papa = '10'; // Записей на страницу
$li_winnews = '';
$pushserver = 'https://psh.lentachan.ru';
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
function chan($chans)
{
	switch ($chans) {
		case 'Анонимус':
			$chan = 'anon.png';
			break;
		case 'Двач':
			$chan = '2ch.gif';
			break;
		case '0chan':
			$chan = '0chan.gif';
			break;
		case 'Ычан':
			$chan = 'iichan.gif';
			break;
		case 'Доброчан':
			$chan = 'dobrochan.gif';
			break;
		case '4chan':
			$chan = '4chan.png';
			break;
		case 'Оланет':
			$chan = 'olanet.png';
			break;
		case 'ВКонтакте':
			$chan = 'vk.ico';
			break;
		case '1chan.ru':
			$chan = '1chan.png';
			break;
		case 'ICQ':
			$chan = 'icq.png';
			break;
		case 'Lenta':
			$chan = 'lenta.png';
			break;
		default:
			$chan = 'no.png';
	}
	return $chan;
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
	$countcom = ("SELECT COUNT(`id`) FROM `blog` WHERE `parrent` = $thread ");
	$countcom = mysql_query($countcom);
	$countcom = mysql_fetch_row($countcom);
	$countcom = max($countcom);
	return $countcom;
}

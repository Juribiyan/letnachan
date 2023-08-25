<?php
$db_host		= 'localhost';
$db_user		= '';
$db_pass		= '';
$db_database	= 'lenta'; 
$link = @mysql_connect($db_host,$db_user,$db_pass) or die('ALLOU, VAS PLOHO SLISHNO');
mysql_select_db($db_database,$link);
mysql_query("SET NAMES 'utf8'");
mysql_query("SET CHARACTER SET 'utf8'");
mysql_query("SET SESSION collation_connection = 'utf8_unicode_ci'");
mysql_query(" DELETE FROM `blog` WHERE `blog`.`message` = '<b>Одинчан <a target=\"_blank\" href=\"https://1chan.ru/news/all/</b>\">https://1chan.ru/news/all/<b></a><br></b>Одинчан <a target=\"_blank\" href=\"https://1chan.ru/news/all/<b>\">https://1chan.ru/news/all/</b></a><br><b>Одинчан <a target=\"_blank\" href=\"https://1chan.ru/news/all/</b>\">https://1chan.ru/news/all/<b></a><br></b>Одинчан <a target=\"_blank\" href=\"https://1chan.ru/news/all/<b>\">https://1chan.ru/news/all/</b></a><br><b>Одинчан <a target=\"_blank\" href=\"https://1chan.ru/news/all/</b>\">https://1chan.ru/news/all/<b></a><br></b>Одинчан <a target=\"_blank\" href=\"https://1chan.ru/news/all/<b>\">https://1chan.ru/news/all/</b></a><br>Одинчан <a target=\"_blank\" href=\"https://1chan.ru/news/all/\">https://1chan.ru/news/all/</a>' ");
mysql_query("DELETE FROM `blog` WHERE `blog`.`message` = 'ПРИСОСАЧ'");
mysql_query("DELETE FROM `blog` WHERE `blog`.`subject` = 'Одинчан'");
mysql_query("DELETE FROM `blog` WHERE `blog`.`subject` = 'ФФФФФФФФФФФФФФФФФФФФФФФФФФФФФФФФФФФ'");
mysql_query("DELETE FROM `blog` WHERE `blog`.`subject` = 'WWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWW'");
mysql_query("DELETE FROM `blog` WHERE `blog`.`subject` = 'фщарфхащшфр'");
?>

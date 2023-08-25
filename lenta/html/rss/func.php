<?php
// установки для связи с базой данных
$db_host="localhost"; // localhost скорее всего
$db_user=""; // имя пользователя БД
$db_pass=""; // пароль пользователя БД
$db_name="lenta"; // имя БД


function dbconnect($db_host, $db_user, $db_pass, $db_name) {
$db_connect = @mysql_connect($db_host, $db_user, $db_pass);
$db_select = @mysql_select_db($db_name);
if (!$db_connect) {
die("Не могу установить связь с MySQL
".mysql_errno()." : ".mysql_error()."");
} elseif (!$db_select) {
die("Не могу выбрать базу данных MySQL
".mysql_errno()." : ".mysql_error()."");
}
}

function dbquery($query) {
$result = @mysql_query($query);
if (!$result) {
echo mysql_error();
return false;
} else {
mysql_query("SET NAMES 'utf8'");
mysql_query("SET CHARACTER SET 'utf8'");
mysql_query("SET SESSION collation_connection = 'utf8_unicode_ci'");
return $result;
}
}

function dbarray($query) {
$result = @mysql_fetch_assoc($query);
if (!$result) {
echo mysql_error();
return false;
} else {
mysql_query("SET NAMES 'utf8'");
mysql_query("SET CHARACTER SET 'utf8'");
mysql_query("SET SESSION collation_connection = 'utf8_unicode_ci'");
return $result;
}
}

dbconnect($db_host, $db_user, $db_pass, $db_name);
?>
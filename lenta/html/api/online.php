<?php
$ip = $_SERVER['REMOTE_ADDR'];
$intip = ip2long($ip);//Преобразуем ойпи в int
if(mysql_num_rows(mysql_query("SELECT * FROM was WHERE ip=".$intip))==0){
    mysql_query("INSERT INTO was (ip) VALUES(".$intip.")");
    fwrite(fopen('api/was', 'w'), mysql_num_rows(mysql_query("SELECT * FROM was")));
}
//Проверяем, онлайн ли юзер
$isoline = mysql_query("SELECT 1 FROM online WHERE ip=".$intip);
if(!mysql_num_rows($isoline))
{
    //Если нет, то заносим
    mysql_query("INSERT INTO online (ip) VALUES(".$intip.")");
}
else
{
    // А если онлайн, то обновляем дату нахождения на сайте:
    mysql_query("UPDATE online SET dt=NOW() WHERE ip=".$intip);
}

// Удаляем записи, которые не обновлялись больше 2ух минуты:
mysql_query("DELETE FROM online WHERE dt<SUBTIME(NOW(),'0 0:1:0')");

// Считаем всех гостей онлайн:
list($totalOnline) = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM online"));
// Выводим:
fwrite(fopen('api/online', 'w'), $totalOnline);
$wasonline = mysql_num_rows(mysql_query("SELECT * FROM was"));
echo $totalOnline;
?>
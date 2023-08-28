<?php
$ip = $_SERVER['REMOTE_ADDR'];
$intip = ip2long($ip);//Преобразуем ойпи в int

require_once 'db.php';
$db = connect_db();

if($db->query("SELECT * FROM was WHERE ip=".$intip)->num_rows == 0){
    $db->query("INSERT INTO was (ip) VALUES(".$intip.")");
    fwrite(fopen('api/was', 'w'), $db->query("SELECT * FROM was")->num_rows);
}
//Проверяем, онлайн ли юзер
$isoline = $db->query("SELECT 1 FROM online WHERE ip=".$intip);
if(!$isoline->num_rows)
{
    //Если нет, то заносим
    $db->query("INSERT INTO online (ip) VALUES(".$intip.")");
}
else
{
    // А если онлайн, то обновляем дату нахождения на сайте:
    $db->query("UPDATE online SET dt=NOW() WHERE ip=".$intip);
}

// Удаляем записи, которые не обновлялись больше 2ух минуты:
$db->query("DELETE FROM online WHERE dt<SUBTIME(NOW(),'0 0:1:0')");

// Считаем всех гостей онлайн:
$totalOnline = $db->query("SELECT COUNT(*) as oc FROM online")->fetch_assoc()['oc'];
// Выводим:
fwrite(fopen('api/online', 'w'), $totalOnline);
$wasonline = $db->query("SELECT * FROM was")->num_rows;
echo $totalOnline;
?>
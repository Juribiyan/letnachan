<?php
$ip = $_SERVER['REMOTE_ADDR'];
$intip = ip2long($ip);//Преобразуем ойпи в int

// $intip = rand(); // For testing

if (!function_exists('clientBroadcast')) { // when pinged
    require_once '../engine.php';
}

if (@$intip) {
    if($db->query("SELECT COUNT(*) as cnt FROM was WHERE ip=".$intip)->fetch_assoc()['cnt'] == 0) {
        $db->query("INSERT INTO was (ip) VALUES(".$intip.")");
    }
    //Проверяем, онлайн ли юзер
    $isoline = $db->query("SELECT COUNT(*) as cnt FROM online WHERE ip=".$intip)->fetch_assoc()['cnt'];
    if(!$isoline) {
        //Если нет, то заносим
        $db->query("INSERT INTO online (ip) VALUES(".$intip.")");
    }
    else
    {
        // А если онлайн, то обновляем дату нахождения на сайте:
        $db->query("UPDATE online SET dt=NOW() WHERE ip=".$intip);
    }
}

// Удаляем записи, которые не обновлялись больше 2ух минуты:
$db->query("DELETE FROM online WHERE dt<SUBTIME(NOW(),'0 0:1:0')");

// Считаем всех гостей онлайн:
$totalOnline = $db->query("SELECT COUNT(*) as cnt FROM online")->fetch_assoc()['cnt'];

// Удаляем записи за прошлый день
$db->query("DELETE FROM was WHERE dt<TIMESTAMP(CURDATE())");

// Выводим:
$wasonline = $db->query("SELECT COUNT(*) as cnt FROM was")->fetch_assoc()['cnt'];
echo $totalOnline;

// Broadcast
clientBroadcast("global", 'online-update', [
    'now' => $totalOnline,
    'was' => $wasonline
]);
?>
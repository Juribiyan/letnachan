<?php
include("engine.php");
require_once "Dklab/Realplexor.php";
function rape($id, $method)
{
    $rpl = new Dklab_Realplexor("127.0.0.1", "10010", "main");
    $sql_one     = "SELECT * FROM `blog` WHERE `id`='" . $id . "' AND `type`='thread'";
    $query_one   = mysql_query($sql_one);
    $rmethod     = mysql_fetch_assoc($query_one);
    $ip          = ip2long($_SERVER['REMOTE_ADDR']);
    $get_numbers = $rmethod['rating'];
    //----
    $sql_two     = "SELECT * FROM `rate` WHERE `ip`='" . $ip . "' AND `thread`='" . $id . "'";
    $query_two   = mysql_query($sql_two);
    if (mysql_num_rows($query_two)) {
        $getrate         = "SELECT * FROM `blog` WHERE `id`='" . $id . "'";
        $getrate2        = mysql_query($getrate);
        $rating          = mysql_fetch_assoc($getrate2);
        $say['response'] = '100';
        $say['message']  = 'Вы уже голосовали за эту новость';
        exit(json_encode($say));
    }
    //----
    if ($method == 'up') {
        $new_numbers = $get_numbers + 1;
    }
    if ($method == 'down') {
        $new_numbers = $get_numbers - 1;
    }
    if ($get_numbers !== $new_numbers) {
        mysql_query("UPDATE `blog` SET `rating`='" . $new_numbers . "'  WHERE `id`='" . $id . "'");
        mysql_query("INSERT INTO `rate` SET `ip`='" . $ip . "',`thread`='" . $id . "'");
        $getrate         = "SELECT * FROM `blog` WHERE `id`='" . $id . "'";
        $getrate2        = mysql_query($getrate);
        $rating          = mysql_fetch_assoc($getrate2);
        $say['response'] = '200';
        $resonance = ($rating['rating'] < 0 ? 'red' : 'green');
        $rpl->send(array("updater"), array('rate' => array('id' => $id, 'rating' => $rating['rating'], 'resonance' => $resonance)));
        $say['message']  = 'Ваш голос засчитан';
        exit(json_encode($say));
        echo $new_numbers;
    } else {
        $say['message'] = 'Неизвестная ошибка';
        die(json_encode($say));
    }
}
if (isset($_GET['id']) AND $_GET['id'] > 0 AND isset($_GET['method']) AND $_GET['method'] == 'up' OR $_GET['method'] == 'down') {
    rape((int) $_GET['id'], $_GET['method']);
} else {
    exit(json_encode(array(
        'message' => 'Неверные параметры голосования'
    )));
}
?>
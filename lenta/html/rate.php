<?php
include("engine.php");
function rape($id, $method)
{
    global $db; 

    $res = $db->query("SELECT * FROM `blog` WHERE `id`='" . $id . "' AND `type`='thread'")
    ->fetch_assoc();
    $ip          = ip2long($_SERVER['REMOTE_ADDR']);
    $get_numbers = $res['rating'];
    //----
    $sql_two     = "SELECT * FROM `rate` WHERE `ip`='" . $ip . "' AND `thread`='" . $id . "'";
    $existing   = $db->query("SELECT COUNT(*) as cnt FROM `rate` WHERE `ip`='" . $ip . "' AND `thread`='" . $id . "'")
    ->fetch_assoc()['cnt'];
    if ($existing) {
        $rating        = $db->query("SELECT * FROM `blog` WHERE `id`='" . $id . "'")->fetch_assoc();
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
        $db->query("UPDATE `blog` SET `rating`='" . $new_numbers . "'  WHERE `id`='" . $id . "'");
        $db->query("INSERT INTO `rate` SET `ip`='" . $ip . "',`thread`='" . $id . "'");
        $say['response'] = '200';
        $say['message']  = 'Ваш голос засчитан';
        // -------------- Broadcast time! --------------
        clientBroadcast("stats:$id", 'rating-update', [
            'id' => $id,
            'rating' => $new_numbers
        ]);
        exit(json_encode($say));
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
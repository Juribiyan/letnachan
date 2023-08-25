<?php
session_start();
require_once '../engine.php';
require_once '../inc/parse.php';
require_once '../inc/validator.php';
require_once "../Dklab/Realplexor.php";
mb_internal_encoding("UTF-8");
$request = parse_url($_SERVER['HTTP_REFERER']);
if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest' and $request['path'] !== 'news') {
    exit('Ты явно делаешь что-то не так');
}
$ip      = ip2long($_SERVER['REMOTE_ADDR']); # IP
$parrent = $_POST['entry']; # Тред
$time    = time(); # Время
$result = mysql_query("SELECT * FROM `blog` WHERE `ip`='$ip' ORDER BY id DESC LIMIT 1");
while ($row = mysql_fetch_array($result)) {
    $wipe = $row['timestamp'];
}
ob_start();
echo "sec code:" . $_SESSION['security_code'];
var_dump($_POST['captcha']);
echo "comparision:\n";
var_dump((isset($_SESSION['security_code']) && isset($_SESSION['security_code']) && $_SESSION['security_code'] == $_POST['captcha']) == FALSE);
$contents = ob_get_contents();
ob_end_clean();
error_log($contents);
$okay = $time - $wipe;
//Капчачек:
if ((isset($_SESSION['security_code']) && isset($_SESSION['security_code']) && $_SESSION['security_code'] == $_POST['captcha']) == FALSE) {
    unset($_SESSION['security_code']);
    postError('Неверная капча!');
}
unset($_SESSION['security_code']);
//Настраиваем параметры валидации комментария
$validator = new FormValidator();
$validator->addValidation("message", "req", "И где комментарий?");
if ($validator->ValidateForm())
//Если входные данные нас удовлетворяют, то создаем комментарий
{
    $rpl = new Dklab_Realplexor("127.0.0.1", "10010", "main");
    $messcheck = preg_replace('/\n(\s*\n)+/', "\n\n", $_POST['message']); #Выпиливаем пустые строки из комментария
    $message   = mysql_real_escape_string(MarkPost(mb_substr(limitlines($messcheck, 7), 0, 1024))); # Текст комментария
    $isitcomment = mysql_fetch_array(mysql_query("SELECT  `type` FROM  `blog` WHERE  `id` = '$parrent'"));
    if ($okay < 5 or $ip != "1307118148") {
        exit(json_encode(array(
            'code' => '403',
            'response' => 'Упырьте мел.',
        )));
    } else {
        if ($isitcomment['type'] == 'thread') {
            if (isset($_POST['captcha']) and !empty($_POST['captcha'])) {
                mysql_query("INSERT INTO `blog` SET `message`='$message', `timestamp`='$time', `type`='post',`parrent`='$parrent',`ip`='$ip',`ch`='1'");
                @$insertid = mysql_insert_id();
            } else {
                mysql_query("INSERT INTO `blog` SET `message`='$message', `timestamp`='$time', `type`='post',`parrent`='$parrent',`ip`='$ip'");
                @$insertid = mysql_insert_id();
            }
        } else {
            exit(json_encode(array(
                'code' => '400',
                'response' => 'Возможно вы наркоман <br>и пытаетесь постить комментарий к комментарию.'
            )));
        }
    }
    $rpl->send(array("updater" => $insertid), array('comm' => array('id' => $parrent, 'num' => max(mysql_fetch_row(mysql_query("SELECT COUNT(`id`) FROM `blog` WHERE `parrent` = $parrent"))))));
    fwrite(fopen('comms/' . $parrent, 'w'), $insertid);
    exit(json_encode(array(
        'code' => '200',
        'response' => 'Ваш комментарий добавлен'
    )));
} else
//А если нет, то выводим ошибку
{
    $errors = $validator->GetErrors();
    foreach ($errors as $inpname => $inp_err) {
        $arrerr[] = $inp_err . '<br>';
    }
    return postError($arrerr);
}

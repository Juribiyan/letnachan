<?php
session_start();
require_once '../engine.php';
require_once '../inc/parse.php';
require_once '../inc/validator.php';
mb_internal_encoding("UTF-8");
$request = parse_url($_SERVER['HTTP_REFERER']);
if ($request['path'] !== '/news') {
    var_dump($request['path']);
    exit('Ты явно делаешь что-то не так');
}
$ip      = ip2long($_SERVER['REMOTE_ADDR']); # IP
$parrent = $_POST['entry']; # Тред
$time    = time(); # Время
$result = $db->query("SELECT * FROM `blog` WHERE `ip`='$ip' ORDER BY id DESC LIMIT 1");
while ($row = $result->fetch_array()) {
    $wipe = $row['timestamp'];
}

$okay = @$wipe ? ($time-$wipe) : WIPE_TIMEOUT_COMM+1;

//Капчачек:
if (USE_HCAPTCHA
  ? CheckHcaptcha() !== 'ok'
  : (
    !isset($_SESSION['security_code']) 
    ||
    $_SESSION['security_code'] != $_POST['captcha']
  )
) {
  unset($_SESSION['security_code']);
  postError('Неверная капча!');
} 
unset($_SESSION['security_code']);

if(trim($_POST['message']) == $_SESSION['last_comment']) {
    exit(json_encode(array(
    'code' => '403',
    'response' => 'Упырьте мел.'
  )));
}
else {
    $_SESSION['last_comment'] = trim($_POST['message']);
}

//Настраиваем параметры валидации комментария
$validator = new FormValidator();
$validator->addValidation("message", "req", "И где комментарий?");
if ($validator->ValidateForm())
//Если входные данные нас удовлетворяют, то создаем комментарий
{
    // $rpl = new Dklab_Realplexor("127.0.0.1", "10010", "main");
    $messcheck = preg_replace('/\n(\s*\n)+/', "\n\n", $_POST['message']); #Выпиливаем пустые строки из комментария
    $message   = $db->real_escape_string(MarkPost(mb_substr(limitlines($messcheck, 7), 0, 1024))); # Текст комментария
    $isitcomment = $db->query("SELECT  `type` FROM  `blog` WHERE  `id` = '$parrent'")->fetch_array();
    if ($okay < WIPE_TIMEOUT_COMM/* or $ip != "1307118148"*/) {
        exit(json_encode(array(
            'code' => '403',
            'response' => 'Упырьте мел.',
        )));
    } else {
        if ($isitcomment['type'] == 'thread') {
            if (isset($_POST['captcha']) and !empty($_POST['captcha'])) {
                $db->query("INSERT INTO `blog` SET `message`='$message', `timestamp`='$time', `type`='post',`parrent`='$parrent',`ip`='$ip',`ch`='1'");
                @$id = $db->insert_id;
            } else {
                $db->query("INSERT INTO `blog` SET `message`='$message', `timestamp`='$time', `type`='post',`parrent`='$parrent',`ip`='$ip'");
                @$id = $db->insert_id;
            }
            // -------------- Broadcast time! --------------
            require_once '../inc/func/stringformatting.php';
            $message = stripslashes($message);
            $comtime = formatDate($time);
            $content = <<<EOT
<a id="{$id}"></a>
<div class="comment new" id="comment{$id}">
  <div class="comment-info">{$comtime} <a id="href" onclick="javascript:insert('&gt;&gt;{$id}');return false;">№{$id}</a></div>
  <div class="comment-text">
    <p>{$message}</p>
  </div>
</div>
EOT;
            clientBroadcast("comms:$parrent", 'new-comment', [
                'id' => $id,
                'content' => preg_replace('~[\r\n]+~', '', $content)
            ]);
            // Broadcast comment count
            $cc = @$db->query(
               "SELECT COUNT(*) AS cc 
                FROM `blog`
                WHERE `parrent`='$parrent'")
            ->fetch_assoc()['cc'];
            if ($cc) {
                clientBroadcast("stats:$parrent", 'comment-count', [
                    'id' => $parrent,
                    'count' => $cc
                ]);
            }
        } else {
            exit(json_encode(array(
                'code' => '400',
                'response' => 'Возможно вы наркоман <br>и пытаетесь постить комментарий к комментарию.'
            )));
        }
    }
    if (@strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        exit(json_encode(array(
            'code' => '200',
            'response' => 'Ваш комментарий добавлен'
        )));
    }
    else {
      header("Location: /news?id=$parrent");
      die();
    }
} else
//А если нет, то выводим ошибку
{
    $errors = $validator->GetErrors();
    foreach ($errors as $inpname => $inp_err) {
        $arrerr[] = $inp_err . '<br>';
    }
    return postError($arrerr);
}

<?php
session_start();
require_once '../engine.php';
require_once '../inc/parse.php';
require_once '../inc/validator.php';
mb_internal_encoding("UTF-8");
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND $_SERVER['HTTP_REFERER']!=$li_URL.'/add') {
  exit('Чего ты пытаешься добиться?');
}
$time = time(); # Время
$ip   = ip2long($_SERVER['REMOTE_ADDR']); # IP
$result=$db->query("SELECT * FROM `blog` WHERE `ip`='$ip' ORDER BY id DESC LIMIT 1");
  while($row = $result->fetch_array()){
    $wipe=$row['timestamp'];
}
$okay = @$wipe ? ($time-$wipe) : WIPE_TIMEOUT_NEWS+1;
ob_start();
echo "sec code:" . $_SESSION['security_code'];
var_dump($_POST['captcha']);
echo "comparision:\n";
var_dump((isset($_SESSION['security_code']) && isset($_SESSION['security_code']) && $_SESSION['security_code'] == $_POST['captcha']) == FALSE);
$contents = ob_get_contents();
ob_end_clean();
error_log($contents);
//Капчачек:
if ((isset($_SESSION['security_code']) && isset($_SESSION['security_code'])  && $_SESSION['security_code'] == $_POST['captcha']) == FALSE)  {
  unset($_SESSION['security_code']);
  postError('Неверная капча!');
} 
unset($_SESSION['security_code']);
if ($okay < WIPE_TIMEOUT_NEWS/* or $ip != "1307118148"*/) {
  exit(json_encode(array(
    'code' => '403',
    'response' => 'Упырьте мел.',
    'ip' => $ip
  )));
} else {
    //Настраиваем параметры валидации
    $validator = new FormValidator();
    $validator->addValidation("title", "minlen=3", "Длина заголовка должна быть больше 3 символов");
    $validator->addValidation("text", "minlen=10", "Короткая новость должна быть больше 10 символов");
    if ($validator->ValidateForm()) //Если входные данные нас удовлетворяют, то создаем новость
      {
        $title    = mb_substr($db->real_escape_string(strip_tags($_POST['title'])), 0, $title_lim); # Заголовок  
        $text     = MarkPost($db->real_escape_string(nl2br(strip_tags(mb_substr(limitlines($_POST['text'], 7), 0, 1024))))); # Короткая новость
        $text2    = MarkPost($db->real_escape_string(nl2br(strip_tags(mb_substr(limitlines($_POST['text2'], 70), 0, 8192))))); # Полная новость
        $chan     = $db->real_escape_string(strip_tags($_POST['chan'])); # Чан
        $link     = $db->real_escape_string(strip_tags($_POST['link'])); # Ссылка
        $category = $db->real_escape_string(strip_tags($_POST['category'])); # Категория
        if($category == 'no'){
            $category = '';
        }
        $video    = $db->real_escape_string(strip_tags($_POST['video'])); # Видео
        if (!empty($video))
          {
            parse_str(parse_url($video, PHP_URL_QUERY), $param);
            $idvideo    = $param['v'];
            $obj        = json_decode(file_get_contents("http://gdata.youtube.com/feeds/api/videos/$idvideo?v=2&alt=jsonc"));
            $titlevideo = $obj->data->title;
            $embedvideo = $obj->data->accessControl->embed;
            if ($titlevideo and $embedvideo == "allowed")
              {
                $text2 .= '<p>Видео YouTube: <b>' . $titlevideo . '</b></p>';
                $text2 .= '<span class="youtube"><a class="youtube-link" target="_blank" href="http://www.youtube.com/watch?v=' . $idvideo . '" title="Воспроизвести" style="background-image: url(http://i2.ytimg.com/vi/' . $idvideo . '/0.jpg);" id="' . $idvideo . '" onclick="youtube(this.id);return false;"><div class="youtube-link-div"></div></a></span>';
              }
          }
        $db->query("INSERT INTO `blog` SET `subject`='$title', `message`='$text', `fullmessage`='$text2', `timestamp`='$time', `chan`='$chan', `link`='$link', `category`='$category',`type`='thread',`parrent`='0',`ip`='$ip'");
          fwrite(fopen('lastid', 'w'), $db->insert_id);
          exit(json_encode(array(
              'code' => '200',
              'response' => 'Ваша новость успешно опубликована',
              'id' => $db->insert_id
          )));
      }
    else
    //А если нет, то выводим всевозможные ошибки
      {
        $errors = $validator->GetErrors();
        foreach ($errors as $inpname => $inp_err)
          {
            $arrerr[] = $inp_err . '<br>';
          }
        return postError($arrerr);
      }
}
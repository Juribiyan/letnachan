<?php
session_start();
require_once '../engine.php';
require_once '../inc/parse.php';
require_once '../inc/validator.php';

mb_internal_encoding("UTF-8");
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH) != '/add') {
  exit('Чего ты пытаешься долбиться?');
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
        if (!empty($video)) {
          require_once '../inc/embeds.php';
          foreach($embeds as $site => $exp) {
            if (preg_match($exp, $video, $matches)) {
              $fn = "embed_".$site;
              $fig_code = $fn($matches);
              $text = $fig_code . $text;
              break;
            }
          }
        }
        $db->query("INSERT INTO `blog` SET `subject`='$title', `message`='$text', `fullmessage`='$text2', `timestamp`='$time', `chan`='$chan', `link`='$link', `category`='$category',`type`='thread',`parrent`='0',`ip`='$ip'");
        $id = $db->insert_id;

        // -------------- Broadcast time! --------------
        require_once '../custom/homeboards.php';
        require_once '../inc/func/stringformatting.php';
        $li_URL = ROOT_URL;
        $title = stripslashes($title);
        $text = stripslashes($text);
        $post_title = $link
          ? '<a target="_blank" href="'.$link.'">'.$title.'</a>'
          : $title;
        $time = formatDate($time);
        $cat_full = whatcat($category);
        $post_category = $cat_full
          ? '<a target="_blank" href="'.$li_URL.'/news/'.$category.'">'.$cat_full.'</a>'
          : '';
        $post_text2_link = $text2
          ? '<a href="'.$li_URL.'/news?id='. $id.'" class="link">Читать далее...</a>'
          : '';
        $homebrd = @$homeboards[$chan];
        $chan = $homebrd ? $homebrd['icon'] : 'no.png';
        $content = <<<EOT
<div class="entry new">
  <div class="news-header">
    <span class="title"><img src="{$li_URL}/images/{$chan}" alt=""> {$post_title}</span>
    <span class="info">
      <span class="time">{$time}</span> | 
      <i class="icon-arrow-down" onclick="vote('down',{$id})"></i> 
      <span class="rat-com green" id="{$id}">0</span> 
      <i class="icon-arrow-up" onclick="vote('up',{$id})"></i> | 
      {$post_category}
      <a class="link" href="{$li_URL}/news?id={$id}">Дискач: <num>0</num></a>
      <span class="foradmin"><br>
        <a href="{$li_URL}/panel?del&id={$id}" class="link">Удалить</a>
        <a href="{$li_URL}/panel?edit&id={$id}" class="link">Редактировать</a>
        <a href="{$li_URL}/panel?real&id={$id}" class="link"><b>Одобряе!</b></a>
      </span>
    </span>
  </div>
  <div class="news-body">
    <p>{$text}</p>{$post_text2_link}
  </div>
</div>
EOT;
        clientBroadcast('global', 'new-entry', [
          'id' => $id,
          'content' => preg_replace('~[\r\n]+~', '', $content)
        ]); // ------------ /Broadcast ------------

        exit(json_encode(array(
            'code' => '200',
            'response' => 'Ваша новость успешно опубликована',
            'id' => $id
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
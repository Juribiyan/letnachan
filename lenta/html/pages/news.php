<?php
//ini_set('display_errors',1);
//error_reporting(E_ALL);
$_SESSION['bot'] = false;

require_once 'custom/homeboards.php';

$li_URL = ROOT_URL; // Слишком лень ковыряться в кавычках

function captcha() {
  return USE_HCAPTCHA
    ? '<script src="https://js.hcaptcha.com/1/api.js" async defer></script>'.
      '<div class="h-captcha" data-sitekey="'.HCAPTCHA_SITEKEY.'" data-callback="enable_submit"></div>'
    : '<a id="cchange"><img src="captcha.php" id="captchaimage"></a><input type="text" name="captcha" autocomplete="off">';
}

$post = (int)@$_GET['id'];

if ($post) {
  $sqlquery = "SELECT * FROM `blog` WHERE `id` = $post AND `type`='thread'";
  $page = 1;
}
else {
  $filter = ['`type`="thread"'];
  if ($page == 'news')
    $filter []= "`real`='1'";
  if ($post_cate)
    $filter []= "`category`='$post_cate'";
  $filter = implode(' AND ', $filter);

  /*Кол-во страниц*/
  $counter = $db->query('SELECT COUNT(`id`) as bc FROM `blog` WHERE '.$filter)
  ->fetch_assoc()['bc'] - 1;
  $pages = intval($counter / $papa) + 1;
  $page = (int)(preg_replace("/[^\w\x7F-\xFF\s]/", "", @$_GET['page']));
  $start = ($page > 0 && $page <= $pages) ? ($page * $papa - $papa) . "," : "";
  $sqlquery = "SELECT * FROM `blog` WHERE $filter ORDER BY `timestamp` DESC LIMIT $start $papa";
  if ($start == "")
    $page = 1;
}

$results = $db->query($sqlquery);
$cnt = 0;
while ($row = $results->fetch_assoc()) {
  $cnt++;
  $posid = $row['id'];
  $rating = $row['rating'];
  $homebrd = @$homeboards[$row['chan']];
  $chan   = $homebrd ? $homebrd['icon'] : 'no.png';
  $name   = stripslashes($row['subject']);
  $text   = stripslashes($row['message']);
  $text2  = stripslashes($row['fullmessage']);
  $comnum = countcoms($row['id']);
  $link   = $row['link'];
  $category = whatcat($row['category']);
  $cate = $row['category'];
  $time   = formatDate($row['timestamp']);
  if ($category) @$post_category = "<a href=\"$li_URL/news/$cate/\">$category</a> | "; else @$post_category = "";
  if ($link) {
    @$post_name = "<a target=\"_blank\" href=\"$link\">$name</a>";
  }
  else {
    @$post_name = "$name";
  }
  if (isset($_GET['id']) and $text2) @$post_text2 = "<p>$text2</p>";
  if ($text2 and !@$_GET['id']) @$post_text2_link =  "<a href=\"$li_URL/news?id=$posid\">Читать далее...</a>"; else @$post_text2_link =  "";
  @$post_text2 = @$post_text2;
  $post_admin = CheckLogin()
    ? "<br>
      <a href=\"$li_URL/api/admin.php?del&id=$posid\" data-action=\"del\" class=\"link admin-ajax-link\">Удалить</a>
      <a href=\"$li_URL/panel?edit&id=$posid\" class=\"link\">Редактировать</a> "
      . ($row['real']==1
        ? "<a href=\"$li_URL/api/admin.php?unreal&id=$posid\" data-action=\"unreal\" class=\"link admin-ajax-link\"><b>Я передумал</b></a>"
        : "<a href=\"$li_URL/api/admin.php?real&id=$posid\" data-action=\"real\" class=\"link admin-ajax-link\"><b>Одобряе!</b></a>"
      )
    : "";
  $resonance = ($rating < 0 ? 'red' : 'green');
  echo <<<EOT
<div class="entry">
  <div class="news-header">
    <span class="title"><img src="{$li_URL}/images/{$chan}" alt=""> {$post_name}</span>
    <span class="info"><span class="time">{$time}</span> | <i class="icon-arrow-down" onclick="vote('down',{$posid})"></i> <span class="rat-com {$resonance}" id="{$posid}">{$rating}</span> <i class="icon-arrow-up" onclick="vote('up',{$posid})"></i> | {$post_category}<a class="link" href="{$li_URL}/news?id={$posid}">Дискач: <num>{$comnum}</num></a>{$post_admin}</span>
  </div>
  <div class="news-body">
    <p>{$text}</p>{$post_text2}{$post_text2_link}
  </div>
</div>
EOT;
}
if ($cnt) {
  if($post) { /*А так же комментарии, если есть потребность.*/
    $comments = $db->query("SELECT * FROM `blog` WHERE `parrent` = $post AND `type`='post' ORDER BY `timestamp` ASC");
    while ($row = $comments->fetch_assoc()) {
      @$lastid = $row['id'];
      $comid = $row['id'];
      $comtext = stripslashes($row['message']);
      $comtime = formatDate($row['timestamp']);          
      echo <<<EOT
      <a id="{$comid}"></a>
      <div class="comment" id="comment{$comid}">
        <div class="comment-info">{$comtime} <a id="href" onclick="javascript:insert('&gt;&gt;{$comid}');return false;">№{$comid}</a></div>
        <div class="comment-text">
          <p>{$comtext}</p>
        </div>
      </div>
      EOT;
    }
    echo '<id id="'.@$lastid.'"></id>';
    echo '<form id="createcomm" method="post" action="api/ncomm.php">
        <div class="olive"> 
          <input type="hidden" name="entry" id="enty" value="'.$post.'">
          <textarea name="message" id="commentText"></textarea>
          <captchazone>'.captcha().'</captchazone>
          <input type="submit" class="button" name="submitComment" value="Отправить" style="float:right;"'.
            (USE_HCAPTCHA ? ' disabled' : '').'>
        </div>
      </form>'; 
  }
}
else {
  echo errorMsg('nullnews');
}

if ((!@$_GET['id']) and ($page > 1 or $page < $pages)) {
  echo '<table class="cont-bottom"><tbody><tr>';
  echo '<td class="left">';
  if ($page > 1) echo '<a class="nav" href="?page=' . ($page - 1) . '">← новее</a>';
  echo '</td>';
  echo '<td class="right">';
  if ($page!=$pages and $page < $pages) echo '<a class="nav" href="?page=' . ($page + 1) . '">старее →</a>';
  echo '</td>';
  echo '</tr></tbody></table>';
}
?>

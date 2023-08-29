<?php
require_once '../engine.php';
require_once '../inc/func/stringformatting.php';
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH) != '/random') {
    exit('Что ищешь тут ты?');
} 
$li_URL = ROOT_URL; // Слишком лень ковыряться в кавычках
$lastid = (int)$_GET['lastid'];
$finish = time() + 50;
$count = @fread(fopen("lastid","r"),filesize("lastid")); 
while ($count <= $lastid)
{
		$now = time();
		usleep(10000);
		if ($now <= $finish)
		{
				$count = @fread(fopen("lastid","r"),filesize("lastid")); 
		}
		else
		{
				break;
		}
}
if ($lastid == $count)
{
		$log['lastid'] = $lastid;
                echo json_encode($log);
}
else
{
$res = $db->query("SELECT * FROM `blog` where `real` = '0' and `type`='thread' and `id`>$lastid ORDER BY `timestamp` DESC LIMIT 10");
    while ($row = $res->fetch_assoc()){
        $posid = $row['id'];
        $rating = $row['rating'];
        $chan   = chan($row['chan']);
        $name   = stripslashes($row['subject']);
        $text   = stripslashes($row['message']);
        $text2  = stripslashes($row['fullmessage']);
        $comnum = countcoms($row['id']);
        $link   = $row['link'];
        $category = whatcat($row['category']);
        $cate = $row['category'];
        $time   = formatDate($row['timestamp']);
        if ($category) @$post_category = "<a href=\"$li_URL/news/$cate/\">$category</a> | "; else @$post_category = "";
        if ($link){
          @$post_name = "<a target=\"_blank\" href=\"$link\">$name</a>";
        }else{
          @$post_name = "$name";
        }
        if (isset($_GET['id']) and $text2) @$post_text2 = "<p>$text2</p>";
        if ($text2 and !@$_GET['id']) @$post_text2_link =  "<a href=\"$li_URL/news?id=$posid\">Читать далее...</a>"; else @$post_text2_link =  "";
        if(CheckLogin()) @$post_admin = "<br><a href=\"$li_URL/panel?del&id=$posid\" class=\"link\">Удалить</a> <a href=\"$li_URL/panel?edit&id=$posid\" class=\"link\">Редактировать</a> <a href=\"$li_URL/panel?real&id=$posid\" class=\"link\"><b>Одобряе!</b></a>"; else @$post_admin = "";
        echo <<<EOT
<div class="entry new">
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
}
?>

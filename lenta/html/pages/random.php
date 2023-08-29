<?php
//ini_set('display_errors',1);
//error_reporting(E_ALL);
require_once 'db.php';
$db = connect_db();

$li_URL = ROOT_URL; // Слишком лень ковыряться в кавычках

$post = (int)@$_GET['id'];
/*Кол-во страниц*/
$counter = $db->query('SELECT COUNT(`id`) as c FROM `blog` WHERE `real` = "0" AND `type`="thread"')
->fetch_assoc()['c'] - 1;
$pages   = intval($counter / $papa) + 1;
if (isset($_GET['page']) and !$_GET['id']){
    $page = preg_replace("/[^\w\x7F-\xFF\s]/", "", $_GET['page']);
    $page = (int) $page;
    if ($page > 0 && $page <= $pages){
        $start = $page * $papa - $papa;
        $sqlquery = ("SELECT * FROM `blog` WHERE `real` = '0' AND `type`='thread' ORDER BY `timestamp` DESC LIMIT {$start}, {$papa}");
      }else{
        $sqlquery = ("SELECT * FROM `blog` WHERE `real` = '0' AND `type`='thread' ORDER BY `timestamp` DESC LIMIT $papa");
        $page = 1;
      }
  }elseif ($post){
    $sqlquery = ("SELECT * FROM `blog` WHERE `id` = $post");
    $page  = 1;
  }else{
    $sqlquery = ("SELECT * FROM `blog` WHERE `real` = '0' AND `type`='thread' ORDER BY `timestamp` DESC LIMIT $papa");
    $page = 1;
  }
require_once 'custom/homeboards.php';
$results = $db->query($sqlquery);
if ($results->num_rows){
    while ($row = $results->fetch_assoc()) {
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
        if ($link){
          @$post_name = "<a target=\"_blank\" href=\"$link\">$name</a>";
        }else{
          @$post_name = "$name";
        }
        if (isset($_GET['id']) and $text2) @$post_text2 = "<p>$text2</p>";
        if ($text2 and !@$_GET['id']) @$post_text2_link =  "<a href=\"$li_URL/news?id=$posid\">Читать далее...</a>"; else @$post_text2_link =  "";
        @$post_text2 = @$post_text2;
        if(CheckLogin()) @$post_admin = "<br><a href=\"$li_URL/panel?del&id=$posid\" class=\"link\">Удалить</a> <a href=\"$li_URL/panel?edit&id=$posid\" class=\"link\">Редактировать</a> <a href=\"$li_URL/panel?real&id=$posid\" class=\"link\"><b>Одобряе!</b></a>"; else @$post_admin = "";
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
  }else{
    echo errorMsg('nullnews');
  }
if ((!@$_GET['id']) and ($page > 1 or $page < $pages)){
    echo '<table class="cont-bottom"><tbody><tr>';
    echo '<td class="left">';
    if ($page > 1) echo '<a href="random?page=' . ($page - 1) . '">< новее</a>';
    echo '</td>';
    echo '<td class="right">';
    if ($page!=$pages and $page < $pages) echo '<a href="random?page=' . ($page + 1) . '">старее ></a>';
    echo '</td>';
    echo '</tr></tbody></table>';
  }
?>
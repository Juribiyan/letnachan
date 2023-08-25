<?php
include '../engine.php';
include '../inc/func/stringformatting.php';
$thread     = (int) $_POST['id'];
$lastidcomm = (int) $_POST['last'];
$comments   = ("SELECT * FROM `blog` WHERE `parrent` = $thread AND `type`='post' AND `id`>$lastidcomm ORDER BY `timestamp` ASC");
$comments   = mysql_query($comments);
$counter = 0;
if (mysql_num_rows($comments)>0)
{
    while ($row = mysql_fetch_assoc($comments))
    {
        $counter +=1;
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
   echo '<count id="'.$counter.'"></count>'; 
   echo '<id id="'.$pastid.'"></id>'; 

}
else
{
    exit(json_encode(array('response'=>'Новых комментариев нет')));
}
?>
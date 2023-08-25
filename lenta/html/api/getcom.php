<?php
include '../engine.php';
include '../inc/func/stringformatting.php';
$idcom    = (int) $_POST['id'];
$comments   = ("SELECT * FROM `blog` WHERE `id` = $idcom");
$comments   = mysql_query($comments);
if (mysql_num_rows($comments)>0)
{
    while ($row = mysql_fetch_assoc($comments))
    {
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
}
else
{
    exit(json_encode(array('response'=>'')));
}
?>
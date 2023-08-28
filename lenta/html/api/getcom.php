<?php
include '../engine.php';
include '../inc/func/stringformatting.php';
$idcom    = (int) $_POST['id'];
$comments   = $db->query("SELECT * FROM `blog` WHERE `id` = $idcom");
if ($comments->num_rows > 0)
{
    while ($row = $comments->fetch_assoc())
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
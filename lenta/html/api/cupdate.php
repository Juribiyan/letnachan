<?php
require_once '../engine.php';
require_once '../inc/func/stringformatting.php';
/*if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND $_SERVER['HTTP_REFERER']!=$li_URL.'/random') {
exit('Что ищешь тут ты?');
} 
 * 
 */
$thread     = (int) $_GET['id'];
$lastid = (int) $_GET['lastid'];
$finish = time() + 50;
$count = @fread(fopen("comms/".$thread ,"r"),filesize("comms/".$thread));
while ($count <= $lastid)
{
    $now = time();
    usleep(10000);
    if ($now <= $finish)
    {
        $count = @fread(fopen("comms/".$thread,"r"),filesize("comms/".$thread));
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
    $counter = 0;
    $comments   = $db->query("SELECT * FROM `blog` WHERE `parrent` = $thread AND `type`='post' AND `id`>$lastid ORDER BY `timestamp` ASC");
    while ($row = $comments->fetch_assoc())
    {
        $counter +=1;
        $comid = $row['id'];
        $comtext = stripslashes($row['message']);
        $comtime = formatDate($row['timestamp']);
        echo <<<EOT
        <a id="{$comid}"></a>
        <div class="comment new" id="comment{$comid}">
          <div class="comment-info">{$comtime} <a id="href" onclick="javascript:insert('&gt;&gt;{$comid}');return false;">№{$comid}</a></div>
          <div class="comment-text">
            <p>{$comtext}</p>
          </div>
        </div>
EOT;
    }
    echo '<count id="'.$counter.'"></count>';
    echo '<id id="'.$comid.'"></id>';
}
?>
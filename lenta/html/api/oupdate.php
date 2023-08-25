<?php
require_once '../engine.php';
$moment   = $_GET['online'];
$totalnum = $_GET['was'];
$finish   = time() + 30;
$intip    = ip2long($_SERVER['REMOTE_ADDR']);
mysql_query("DELETE FROM online WHERE dt<SUBTIME(NOW(),'0 0:1:0')");
list($totalOnline) = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM online"));
$count     = $totalOnline;
$wasonline = mysql_num_rows(mysql_query("SELECT * FROM was"));
while ($count <= $moment)
  {
    $now = time();
    usleep(10000);
    if ($now <= $finish)
      {
        mysql_query("DELETE FROM online WHERE dt<SUBTIME(NOW(),'0 0:1:0')");
        list($totalOnline) = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM online"));
        $count = $totalOnline;
      }
    else
      {
        break;
      }
  }
if ($moment == $count)
  {
    mysql_query("UPDATE online SET dt=NOW() WHERE ip=" . $intip);
    mysql_query("DELETE FROM online WHERE dt<SUBTIME(NOW(),'0 0:1:0')");
    $log['was']    = $totalnum;
    $log['online'] = $moment;
    echo json_encode($log);
  }
else
  {
    mysql_query("UPDATE online SET dt=NOW() WHERE ip=" . $intip);
    mysql_query("DELETE FROM online WHERE dt<SUBTIME(NOW(),'0 0:1:0')");
    list($totalOnline) = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM online"));
    $count         = $totalOnline;
    $log['was']    = mysql_num_rows(mysql_query("SELECT * FROM was"));
    $log['online'] = $count;
    echo json_encode($log);
  }
?>
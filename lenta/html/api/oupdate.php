<?php
require_once '../engine.php';
$moment   = $_GET['online'];
$totalnum = $_GET['was'];
$finish   = time() + 30;
$intip    = ip2long($_SERVER['REMOTE_ADDR']);
$db->query("DELETE FROM online WHERE dt<SUBTIME(NOW(),'0 0:1:0')");
list($totalOnline) = $db->query("SELECT COUNT(*) FROM online")->fetch_array();
$count     = $totalOnline;
$wasonline = $db->query("SELECT * FROM was")->num_rows;
while ($count <= $moment) {
  $now = time();
  usleep(10000);
  if ($now <= $finish) {
    $db->query("DELETE FROM online WHERE dt<SUBTIME(NOW(),'0 0:1:0')");
    list($totalOnline) = $db->query("SELECT COUNT(*) FROM online")->fetch_array();
    $count = $totalOnline;
  }
  else {
    break;
  }
}
if ($moment == $count) {
  $db->query("UPDATE online SET dt=NOW() WHERE ip=" . $intip);
  $db->query("DELETE FROM online WHERE dt<SUBTIME(NOW(),'0 0:1:0')");
  $log['was']    = $totalnum;
  $log['online'] = $moment;
  echo json_encode($log);
}
else {
  $db->query("UPDATE online SET dt=NOW() WHERE ip=" . $intip);
  $db->query("DELETE FROM online WHERE dt<SUBTIME(NOW(),'0 0:1:0')");
  list($totalOnline) = $db->query("SELECT COUNT(*) FROM online")->fetch_array();
  $count         = $totalOnline;
  $log['was']    = $db->query("SELECT * FROM was")->num_rows;
  $log['online'] = $count;
  echo json_encode($log);
}
?>
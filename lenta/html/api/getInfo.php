<?php
require_once '../engine.php';
$obj=json_decode($_POST['array'], true);
for ($i=1; $i<count($obj); $i++){
    $id = $obj[$i]['id'];
    $res = $db->query("SELECT rating FROM blog WHERE id = $id")->fetch_assoc();
    $obj[$i]['rate'] = $res['rating'];
    $obj[$i]['comm'] = countcoms($obj[$i]['id']);
}
echo json_encode($obj);
?>

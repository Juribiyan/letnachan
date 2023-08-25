<?php
require_once '../engine.php';
$obj=json_decode($_POST['array'], true);
for ($i=1; $i<count($obj); $i++){
    $id = $obj[$i]['id'];
    $sql = mysql_fetch_assoc(mysql_query("SELECT rating FROM blog WHERE id = $id")); 
    $obj[$i]['rate'] = $sql['rating'];
    $obj[$i]['comm'] = countcoms($obj[$i]['id']);
}
echo json_encode($obj);
?>

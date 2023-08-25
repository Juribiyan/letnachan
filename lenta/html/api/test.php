<?php
$db_host		= 'localhost';
$db_user		= '';
$db_pass		= '';
$db_database	= 'lenta'; 
$link = @mysql_connect($db_host,$db_user,$db_pass) or die('ALLOU, VAS PLOHO SLISHNO');
mysql_select_db($db_database,$link);
mysql_query("SET NAMES 'utf8'");
mysql_query("SET CHARACTER SET 'utf8'");
mysql_query("SET SESSION collation_connection = 'utf8_unicode_ci'");
$result=mysql_query("SELECT * FROM `blog` WHERE `ip`='127.0.0.1' ORDER BY id DESC LIMIT 1");
  while($row = mysql_fetch_array($result)){
    $wipe=$row['timestamp'];

    echo "$wipe";
    }
?>
<?php 
require_once '../db.php';
$db = connect_db();

header('Content-Type: text/xml; charset=utf-8'); 
// include "func.php"; 

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<rss version=\"2.0\">
<channel>
	<title>lentachan.ru</title>
	<link>http://lentachan.ru/rss/</link>
	<description>lentachan.ru RSS</description>
	<language>ru</language>
	";

$row = $db->query("SELECT `timestamp` FROM `blog` ORDER BY `timestamp` DESC LIMIT 0, 1")->fetch_assoc();
$lnd = $row['timestamp'];
$upldt = date("r",$lnd); // конвертация даты в формат RFC 2822
echo "<lastBuildDate>$upldt</lastBuildDate>
";

$result = $db->query("SELECT * FROM `blog` WHERE `id` AND `type`='thread' ORDER BY `timestamp` DESC LIMIT 0, 10");
while ($row = $result->fetch_assoc()) {
	$thread_id = $row['id'];
	$thread_name = stripslashes($row['subject']);
	$thread_text =  preg_replace('/\<span class\="youtube"\>(.+)\<\/span\>/is', "", $row['message']); 
	$thread_text =  preg_replace('/\<audio controls\="controls"\>(.+)\<\/audio\>/is', "", $thread_text);
	$thread_text =  preg_replace('/\<br\>/is', "<br />", $thread_text);
	$thread_comnum = $row['id'];
	$thread_time = date("r",$row['timestamp']);

	echo "	<item>\n";
	echo "		<title>$thread_name</title>\n";
	echo "		<link>http://lentachan.ru/news?id=$thread_id</link>\n";
	echo "		<description><![CDATA[$thread_text]]></description>\n";		
	echo "		<pubDate>$thread_time</pubDate>\n";
	echo "		<guid isPermaLink=\"1\">http://lentachan.ru/news?id=$thread_id</guid>\n";
	echo "	</item>\n";
}

echo "</channel>
</rss>";
?>

<?php
mb_internal_encoding('UTF-8');
mb_regex_encoding('UTF-8');
function MakeURL($string)
{
    $string = preg_replace('#(http://|https://|ftp://|irc://)([^(\s<|\[)]*)#', '<a target="_blank" href="\\1\\2">\\1\\2</a>', $string);
    return $string;
}
function BBCode($string)
{
    $patterns = array(
        '`\*\*(.+?)\*\*`is',
        '`\*(.+?)\*`is',
        '`%%(.+?)%%`is',
        '`\[b\](.+?)\[/b\]`is',
        '`\[i\](.+?)\[/i\]`is',
        '`\[u\](.+?)\[/u\]`is',
        '`\[s\](.+?)\[/s\]`is',
        '`\[link\](.+?)\[/link\]`is',
        '`\[code\](.+?)\[/code\]`is',
        '`\[spoiler\](.+?)\[/spoiler\]`is',
//        '`\[img\](.+?)\[/img\]`is',
        '`--`is',
	'`:sobak:`is'
    );
    $replaces = array(
        '<b>\\1</b>',
        '<i>\\1</i>',
        '<span class="spoiler">\\1</span>',
        '<b>\\1</b>',
        '<i>\\1</i>',
        '<span style="border-bottom: 1px solid">\\1</span>',
        '<strike>\\1</strike>',
        '<a class="link" target="_blank" href="\\1">\\1</a>',
        '<pre><code>\\1</code></pre>',
        '<span class="spoiler">\\1</span>',
//	'<a href="https://i.imgur.com/\\1.png" target="_blank"><img class="inpost" width="100px" height="100px" src="https://i.imgur.com/\\1.png"></a>',
        '—',
	'<img src="/images/dog.gif">'
    );
    $string   = preg_replace($patterns, $replaces, $string);
    return $string;
}
function postRef($matches)
{
    $id =& $matches[2];
    $result = mysql_query("SELECT `parrent` FROM `blog` WHERE `id` = $id");
    if ($result) {
        while ($post = mysql_fetch_assoc($result)) {
            $par = $post['parrent'];
        }
    }
    if ($par == '0') {
        $par = $id;
    }
    if ($par) {
        return "<a id=\"postpreview-target-$id\"  href=\"news?id=$par#$id\" class=\"tooltip-target\">&gt;&gt;$id</a>\n";
    } else {
        return "&gt;&gt;$id";
    }
}
function MakeLinks($message)
{
    $message = preg_split('/\\r\\n?|\\n/', $message);
    for ($i = 0; $i < count($message); $i++) {
        if (mb_substr($message[$i], 0, 1) == '>') {
            $message[$i] = '<span class="yobka">' . $message[$i] . '</span>';
        }
        if (preg_match("#.*(>>|&gt;&gt;)([0-9]+).*#is", $message[$i])) {
            $message[$i] = preg_replace_callback("#(>>|&gt;&gt;)([0-9]+)#", "postRef", $message[$i]);
        }
    }
    $message = implode("<br>", $message);
    return $message;
}
function MarkPost($string)
{
    $string = MakeURL($string);
    /*Делаем УРЛЫ*/
    $string = MakeLinks($string);
    /*Делаем >>*/
    $string = BBCode($string);
    /*ББшки*/
    return $string;
}
?>
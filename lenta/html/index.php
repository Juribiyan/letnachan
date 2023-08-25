<?php
//сессия для админки
session_start();
//сессия для админки
require_once("engine.php");
require_once 'inc/func.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title><?php
        $url = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $url = parse_url($url);
        if (!isset($url['path']) OR $url['path'] == '/'){
            $title = 'Lentachan.ru: Новости имиджборд';
        }
        if ($url['path'] == '/random'){
            $title = 'Все новости';
        }
        if ($url['path'] == '/add'){
            $title = 'Добавление новости';
        }
        if ($url['path'] == '/help'){
            $title = 'Помощь';
        }
        if ($url['path'] == '/rules'){
            $title = 'Правила';
        }
        if ($url['path'] == '/contact'){
            $title = 'Контакты';
        }
	
        else{
            switch ($url['path']){
                case '/news/aib/': $title='Новости АИБ'; break;
                case '/news/irl/': $title='Новости ИРЛ'; break;
                case '/news/int/': $title='Новости Интернета'; break;
                case '/news/all/': $title='Обсуждение'; break;
            }
        }
        if (isset($_GET['id']))
        {
            $id    = (int) $_GET['id'];
            $title = mysql_fetch_assoc(mysql_query("SELECT subject,category FROM blog WHERE id = $id"));
            if (!empty($title['category'])){
                switch ($title['category']){
                    case 'aib': $title['category']='Новости АИБ'; break;
                    case 'irl': $title['category']='Новости ИРЛ'; break;
                    case 'int': $title['category']='Новости Интернета'; break;
                    case 'all': $title['category']='Обсуждение'; break;
                }
                $title = $title['subject'] . ' - ' . $title['category'];
            }
            else{
                $title = $title['subject'];
            }
        }
        echo $title;
        ?></title>
    <meta name="description" content="Новостной ресурс АИБ">
    <meta name="keywords" content="Лентачан, лентач, лента-чан, Новости">
    <link rel="stylesheet" href="/min/index.php?&amp;g=c&amp;8" type="text/css">
    <link rel="shortcut icon" href="<?= $li_URL; ?>/favicon.ico">
    <script src="/js/jquery-latest.min.js"></script>
    <script src="/js/jquery-common.js"></script>
    <script src="/js/jquery-realplexor.js"></script>
    <script src="/js/lentach.js"></script>
</head>
<body>
<div class="left-colm">
    <div class="left-menu">
<br>
        <hr>
        <menu>
            <li><a href="/random" class="menu-a">Все новости</a></li>
            <li><a href="/news/aib/" class="menu-a">Новости АИБ</a></li>
            <li><a href="/news/irl/" class="menu-a">Новости ИРЛ</a></li>
            <li><a href="/news/int/" class="menu-a">Новости Интернета</a></li>
            <li><a href="/news/all/" class="menu-a">Обсуждение</a></li>
        </menu>
        <hr>
        <div class="left-links">
            <h2 class="title">Имиджборды</h2>
            <hr>
            <div class="category">
                <img src="<?= $li_URL; ?>/images/iichan.gif" alt class="icon"> <a href="https://iichan.hk/" rel="nofollow" target="_blank">IIchan</a>
            </div>
            <div class="category">
                <img src="<?= $li_URL; ?>/images/dobrochan.gif" alt class="icon"> <a href="http://dobrochan.org/" rel="nofollow" target="_blank">Доброчан</a>
            </div>
            <div class="category">
                <img src="<?= $li_URL; ?>/images/0chan.gif" alt class="icon"> <a href="http://0chan.one/" rel="nofollow" target="_blank">Øchan.one</a>
            </div>
            <div class="category">
                <img src="<?= $li_URL; ?>/images/2ch.hk.gif" alt class="icon"> <a href="https://2ch.hk/" rel="nofollow" target="_blank">2ch.hk</a>
            </div>
            <hr>
            <h2 class="title">Другое</h2>
            <hr>
            <div class="category">
                <img src="<?= $li_URL; ?>/images/overchan.gif" alt class="icon"> <a href="http://overchan.ru/" rel="nofollow" target="_blank">overchan</a>
            </div>
            <div class="category">
                <img src="<?= $li_URL; ?>/images/metachan.ico" alt class="icon"> <a href="http://metachan.ru/" rel="nofollow" target="_blank">metachan</a>
            </div>
            <div class="category">
                <img src="<?= $li_URL; ?>/images/1chan.png" alt class="icon"> <a href="https://1chan.ru/news/" rel="nofollow" target="_blank">1chan.ru</a>
            </div>
            <div class="category">
                <img src="<?= $li_URL; ?>/images/1chan.ca.gif" alt class="icon"> <a href="https://1chan.ca/news/" rel="nofollow" target="_blank">1chan.ca</a>
            </div>
            <div class="category">
                <img src="<?= $li_URL; ?>/images/1chan.pl.png" alt class="icon"> <a href="https://1chan.pl/news/" rel="nofollow" target="_blank">1chan.pl</a>
            </div>
        </div>
    </div>
</div>
<div class="cont-colm">
    <!--header-->
    <header>
        <div class="header-add">
            <span>Читают <linenum><?php require_once 'api/online.php'; ?></linenum>, сегодня было <totalnum><?=$wasonline?></totalnum></span>
            <a href="/add">Добавить новость</a></div>
        <span class="logo"><a href="<?= $li_URL; ?>"><img src="<?= $li_URL; ?>/images/logo.png" alt="logo" id="logo"></a></span>
		<span class="datetime">
			<span id="timeto"></span>			
		</span>
    </header>
    <!--#header-->

    <!--content-->
    <div class="content">
        <?php
        $page = preg_replace("/[^\w\x7F-\xFF\s]/", "", $url['path']);
        $page = $page . '.php';
        if (is_file('pages/' . $page) == true) {
 //           echo $page; - для отладки, чтобы понять, на какой странице выведенная инфа
            include("pages/$page");
        }elseif (!@$_GET['pages']){
            include("pages/news.php");
        }else{
            include('pages/404.php');
        }
        ?>
    </div>
    <!--#content-->

    <div class="footerline">
        Сделал <i>лента-кун</i>, все права принадлежат анону. <br>
        <span><a href="<?= $li_URL; ?>/contact">Контакты</a> | <a href="<?= $li_URL; ?>/rules">Правила</a> | <a href="<?= $li_URL; ?>/help">Помощь</a> | RSS: <a href="<?= $li_URL; ?>/rss/">Все</a> <a href="<?= $li_URL; ?>/rss/?cate=news">Одобренные</a> <a href="<?= $li_URL; ?>/rss/?cate=random",>Рандом</a></span>
        <a id="bottom"></a>
    </div>
</div>
</body>
</html>

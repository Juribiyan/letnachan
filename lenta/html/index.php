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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php
        $title = SITE_TITLE . ': ' . SITE_SUBTITLE;
        $url = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $url = parse_url($url);
        $path_map = [
            '/random' => 'Все новости',
            '/news' => 'Все новости (одобренные)',
            '/add' => 'Добавление новости',
            '/help' => 'Помощь',
            '/rules' => 'Правила',
            '/contact' => 'Контакты',
            '/news/aib/' => 'Новости АИБ (одобренные)',
            '/news/irl/' => 'Новости ИРЛ (одобренные)',
            '/news/int/' => 'Новости Интернета (одобренные)',
            '/news/all/' => 'Обсуждения (одобренные)',
            '/category/aib/' => 'Новости АИБ',
            '/category/irl/' => 'Новости ИРЛ',
            '/category/int/' => 'Новости Интернета',
            '/category/all/' => 'Обсуждения'
        ];
        if (@$path_map[$url['path']]) {
            $title = $path_map[$url['path']];
        }
        if (isset($_GET['id']))
        {
            $id    = (int) $_GET['id'];
            $title = $db->query("SELECT subject,category FROM blog WHERE id = $id")
            ->fetch_assoc();
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
    <link rel="stylesheet" href="/css/style.css" type="text/css">
    <link rel="shortcut icon" href="<?= ROOT_URL ?>/favicon.ico">
    <script src="/js/jquery-latest.min.js"></script>
    <script src="/js/jquery-common.js"></script>
    <?php if (!DISABLE_SOCKETIO): ?><script src="/socket.io/socket.io.js"></script><?php endif; ?>
    <script src="/js/lentach.js"></script>
</head>
<body>
<div class="left-colm">
    <div class="left-menu">
<br>
        <hr>
        <menu>
            <li><a href="/random" class="menu-a">Все новости</a></li>
            <li><a href="/news" class="menu-a">Одобрeнные</a></li>
            <li><a href="/category/aib/" class="menu-a">Новости АИБ</a></li>
            <li><a href="/category/irl/" class="menu-a">Новости ИРЛ</a></li>
            <li><a href="/category/int/" class="menu-a">Новости Интернета</a></li>
            <li><a href="/category/all/" class="menu-a">Обсуждение</a></li>
        </menu>
        <hr>
        <div class="left-links">
            <h2 class="title">Имиджборды</h2>
            <hr>
            <?php require_once 'custom/links.php';
            foreach($imageboards as $link): ?>
            <div class="category">
                <img src="<?= ROOT_URL ?>/images/<?= $link['icon'] ?>" alt class="icon"> <a href="<?= $link['url'] ?>" rel="nofollow" target="_blank"><?= $link['name'] ?></a>
            </div>
            <?php endforeach; ?>
            <hr>
            <h2 class="title">Другое</h2>
            <hr>
            <?php foreach($other_links as $link): ?>
            <div class="category">
                <img src="<?= ROOT_URL ?>/images/<?= $link['icon'] ?>" alt class="icon"> <a href="<?= $link['url'] ?>" rel="nofollow" target="_blank"><?= $link['name'] ?></a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<div class="cont-colm">
    <!--header-->
    <header>
        <div class="burger" title="Меню"></div>
        <div class="header-add">
            <span>Читают <linenum><?php require_once 'api/online.php'; ?></linenum>, сегодня было <totalnum><?=$wasonline?></totalnum></span>
            <a href="/add">Добавить новость</a></div>
        <span class="logo"><a href="<?= ROOT_URL ?>"><img src="<?= ROOT_URL ?>/images/logo.png" alt="logo" id="logo"></a></span>
		<span class="datetime">
			<span id="timeto"></span>			
		</span>
    </header>
    <!--#header-->

    <!--content-->
    <div class="content">
        <?php
        $path = preg_split("/\//", $url['path']);
        $page = $path[1];
        $post_cate = @$path[2];
        if (!$page || $page == 'random' || $page == 'category') {
            include("pages/news.php");
        }
        else {
            $page_file = "pages/$page.php";
            if (is_file($page_file)) {
                include($page_file);
            }
            else {
                include('pages/404.php');
            }
        }
        ?>
    </div>
    <!--#content-->

    <div class="footerline">
        Сделал <i>лента-кун</i>, все права принадлежат анону. <br>
        <span><a href="<?= ROOT_URL ?>/contact">Контакты</a> | <a href="<?= ROOT_URL ?>/rules">Правила</a> | <a href="<?= ROOT_URL ?>/help">Помощь</a> | RSS: <a href="<?= ROOT_URL ?>/rss/">Все</a> <a href="<?= ROOT_URL ?>/rss/?cate=news">Одобренные</a> <a href="<?= ROOT_URL ?>/rss/?cate=random",>Рандом</a></span>
        <a id="bottom"></a>
    </div>
</div>
</body>
</html>

<?php
// require 'inc/parse.php';

require_once 'db.php';
$db = connect_db();
require_once 'inc/admin.php';

if (@$_GET['getpass']) {
	echo my_crypt($_GET['getpass']);
}

# Идеальный код регистрации на ленте.
/*if($_GET['reg'] == "1"){
	echo "<p>Создание аккаунта.</p>";
	echo "<br><form action='panel?reg=1' method='post'>
	<input type='text' name='reg_login' placeholder='Логин'>
	<input type='text' name='reg_password' placeholder='Пароль'>
	<input type='text' name='reg_name' placeholder='Имя'>
	<input type='submit' value='Регистрация'>
	</form>"; 

$reg_login = $_POST['reg_login'];
$reg_password = my_crypt($_POST['reg_password']);
$reg_name = $_POST['reg_name'];

	if(isset($_POST['reg_login']) AND isset($_POST['reg_password']) AND isset($_POST['reg_name'])){	
		mysql_query("INSERT INTO `users` SET `login`='$reg_login', `password`='$reg_password',`name`='$reg_name'");
	}
}*/

if (isset($_POST['login']) && isset($_POST['password'])){
	$login = $db->real_escape_string($_POST['login']);
	$password = my_crypt($_POST['password']);
	$query = "SELECT * FROM `users` WHERE `login`='{$login}' AND `password`='{$password}' LIMIT 1";
	$user = $db->query("SELECT * FROM `users` WHERE `login`='{$login}' AND `password`='{$password}' LIMIT 1")->fetch_assoc();
	if ($user) {
		$_SESSION['user_id'] = $user['id'];
		$_SESSION['user_name'] = $user['name'];
	}
	else {
		echo 'Такой логин с паролем не найдены в базе данных.';
	}
}

if(CheckLoginS()) {
	echo "<div class=\"cont-header\">Привет, <b>".$_SESSION['user_name']."</b>.</div>
		<p>".(isset($_GET['msg']) ? $_GET['msg'] : "Сюда хочется что-то пихнуть, а нечего пихать.")."</p>";
	if (isset($_GET['edit'])){
		echo '<h1>Редактируем запись.</h1>';
		$id = @$_GET['id'];
		if (!$id) {
			echo "<p>Не указан ID поста.</p>";
			exit();
		}
		$row = $db->query("SELECT * FROM `blog` WHERE `id` = $id ORDER BY `timestamp` DESC")
		->fetch_assoc();    
		if (!$row) {
			echo "<p>Поста с указанным ID не существует.</p>";
			exit();
		}
		echo "<br><form action='/api/panel.php?edit&id=".$row['id']."' method='post' name='form_add'>
		<input type='text' name='subject' value=\"".$row['subject']."\"><br>
		<textarea name='message' cols=\"40\" rows=\"10\">".$row['message']."</textarea><br>";
		if($row['fullmessage']){
			echo "<textarea name='fullmessage' cols=\"40\" rows=\"10\">".$row['fullmessage']."</textarea><br>";
		}
		echo "<input type='text' name='link' value=\"".$row['link']."\"><br>
		<input type='submit' value='Изменить пост'>
		</form>";		
	}
}
else {
	echo "<br><form action='panel?login' method='post'>
	<input type='text' placeholder='Кто?' name='login'>
	<input type='text' placeholder='Каким способом?' name='password'>
	<input type='submit' value='Аннигилировать'>
	</form>"; 
}

?>

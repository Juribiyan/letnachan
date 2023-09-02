<?php
require 'inc/parse.php';

require_once 'db.php';
$db = connect_db();

function my_crypt($pass){
	$spec=array('~','!','@','#','$','%','^','&','*','?');
	$crypted=md5(md5(CRYPT_SALT).md5($pass));
	$c_text=md5($pass);
	for ($i=0;$i<strlen($crypted);$i++){
	if (ord($c_text[$i])>=48 and ord($c_text[$i])<=57){
		@$temp.=$spec[$c_text[$i]];
	} elseif(ord($c_text[$i])>=97 and ord($c_text[$i])<=100){
		@$temp.=strtoupper($crypted[$i]);
	} else {
		@$temp.=$crypted[$i];
	}
	}
	return md5($temp);
}

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
    $sql = $db->query($query) or die($db->error);
    if ($sql->num_rows == 1) {
        $row = $sql->fetch_assoc();
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['user_name'] = $row['name'];
    }
    else {
        echo 'Такой логин с паролем не найдены в базе данных.';
    }
}
	function CheckLoginS(){
		if(@$_SESSION['user_login'] or @$_SESSION['user_name']){
			return true;
		}else{
			return false;
		}
	}
if(CheckLoginS()){

	echo "<div class=\"cont-header\">Привет, <b>".$_SESSION['user_name']."</b>.</div>";
	echo "<p>Сюда хочется что-то пихнуть, а нечего пихать.</p>";
		if(isset($_GET['edit'])){
			echo '<h1>Редактируем запись.</h1>';
			$idPost = $_GET['id'];
			$sqlquery = ("SELECT * FROM `blog` WHERE `id` = $idPost ORDER BY `timestamp` DESC");    
			$results = $db->query($sqlquery);
			while ($row = $results->fetch_assoc()) {

			    echo "<br><form action='panel?edit&id=".$row['id']."' method='post' name='form_add'>
			    <input type='text' name='subject' value=\"".$row['subject']."\"><br>
			    <textarea name='message' cols=\"40\" rows=\"10\">".$row['message']."</textarea><br>";
			    if($row['fullmessage']){
			    	echo "<textarea name='fullmessage' cols=\"40\" rows=\"10\">".$row['fullmessage']."</textarea><br>";
			    }
			    echo "<input type='text' name='link' value=\"".$row['link']."\"><br>
			    <input type='submit' value='Изменить пост'>
			    </form>";      
			}
			if(isset($_POST['subject']) AND isset($_POST['message'])){	
				$name = $_POST['subject'];
				$text = $db->real_escape_string($_POST['message']);
				$text2 = $db->real_escape_string($_POST['fullmessage']);
				$link = $_POST['link'];
				$results = $db->query("UPDATE `blog` SET `subject`='$name',`message`='$text',`fullmessage`='$text2',`link`='$link' WHERE `id` = $idPost");
				header("location: panel");
				exit;
			}			
		}elseif(isset($_GET['del'])){
			$id = $_GET['id'];
			if(isset($_GET['com'])){
				$results = $db->query("DELETE FROM `comments` WHERE `id` = $id");
				header("location: panel?com");
				exit;
			}else{
				$results = $db->query("DELETE FROM `blog` WHERE `id` = $id");
				$results = $db->query("DELETE FROM `comments` WHERE `thread` = $id");
				header("location: panel");
				exit;
			}
		}elseif(isset($_GET['real'])){
			$id = $_GET['id'];
			$results = $db->query("UPDATE `blog` SET `real`='1' WHERE `id` = $id");				
		}elseif(isset($_GET['unreal'])){
			$id = $_GET['id'];
			$results = $db->query("UPDATE `blog` SET `real`='0' WHERE `id` = $id");
		}

}else{
	echo "<br><form action='panel?login' method='post'>
	<input type='text' placeholder='Кто?' name='login'>
    <input type='text' placeholder='Каким способом?' name='password'>
    <input type='submit' value='Анигилировать'>
    </form>"; 
}

?>

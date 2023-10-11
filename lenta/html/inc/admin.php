<?php
function CheckLoginS(){
  if (USE_TELEGRAM) {
    require_once dirname(__FILE__).'/../api/db.php'; // I FUCKED THIS DIRECTORY STRUCTURE AND I FUCKED EVERY PHP DEVELOPER PERSONALLY AND I FUCKED ORIGINAL DEVS' WHOLE PHOTO ALBUM
    $user = userByCookie();
    if ($user && !$user['banned_by'] && $user['authority']!='user')
      return [
        "id" => $user['id'],
        "authority" => $user['authority']
      ];
    else
      return false;
  }
  else {
    if(@$_SESSION['user_login'] or @$_SESSION['user_name']){
      return true;
    }
    else {
      return false;
    }
  }
	
}

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
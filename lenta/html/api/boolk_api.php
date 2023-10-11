<?php
if (!isset($db)) {
  require_once dirname(__FILE__).'/../db.php'; // FUCK THIS BULLSHIT
  $db = connect_db();
}

define('TG_BANNED', '<div class="banned-msg logon-msg">Вы забанены!</div>');
define('TG_LOGOUT', '<button onclick="telegramLogout()" class="logout"><i></i>Выйти</button>');
define('TG_WIDGET', '<script async src="https://telegram.org/js/telegram-widget.js?22" 
  data-telegram-login="' .  BOT_ID . '" 
  data-size="medium" data-onauth="onTelegramAuth(user)" data-request-access="write"></script>');

function checkTelegramAuthorization($auth_data) {
  global $db;

  $check_hash = $auth_data['hash'];
  unset($auth_data['hash']);
  $data_check_arr = [];
  foreach ($auth_data as $key => $value) {
    $data_check_arr[] = $key . '=' . $value;
  }
  sort($data_check_arr);
  $data_check_string = implode("\n", $data_check_arr);
  $secret_key = hash('sha256', BOT_TOKEN, true);
  $hash = hash_hmac('sha256', $data_check_string, $secret_key);
  if (strcmp($hash, $check_hash) !== 0) {
    throw new Exception('Неверные данные авторизации');
  }
  if ((time() - $auth_data['auth_date']) > 86400) {
    throw new Exception('Данные авторизации протухли');
  }
  return $auth_data['id'];
}

function checkTgUser($hash) {
  global $db;
  $user = userByHash($hash);
  if ($user && $user['banned_by']) {
    throw new Exception('Вы забанены!');
  }
  return $user;
}

function userByHash($hash) {
  global $db;
  return $db->query("SELECT * FROM `tg_users` WHERE `hash`='$hash'")->fetch_assoc();
}

function userByCookie() {
  $hash = @$_COOKIE['tg_user'];
  return $hash
    ? userByHash($hash)
    : false;
}

function registerTgUser($hash) {
  global $db;
  $db->query("INSERT INTO `tg_users` SET `hash`='$hash'");
  return [
    "authority" => 'user',
    "register" => true
  ];
}

function tgLogonHTML($user) {
  $html = '<div class="telegram-logon">';

  if ($user) {
    if ($user['banned_by']) {
      $html .= TG_BANNED;
    }
    else {
      $html .= '<div class="logon-msg">Уровень доступа: '.(
        ["user" => 'Пользователь',
         "mod" => 'Модератор',
         "admin" => 'Администратор'
        ][$user['authority']]
      ).'</div>'; 
    }
    $html .= TG_LOGOUT;
  }
  else {
    $html .= TG_WIDGET;
  }

  return $html.'</div>';
}

function telegramLogon() {
  $user = userByCookie();
  $html = tgLogonHTML($user);
  $may_post = ($user && !$user['banned_by']);
  return [$html, $may_post];
}

if (isset($_GET['user'])) {
  try {
    $user_id = checkTelegramAuthorization($_GET['user']);
    // Produce a permanent user hash
    $hash = hash('sha256', $user_id.CRYPT_SALT);
    $user = checkTgUser($hash);
    if (!$user) {
      $user = registerTgUser($hash);
    }
    setcookie('tg_user', $hash, ["path" => '/']);
    exit(json_encode([
      "error" => false,
      "authority" => $user['authority'],
      "new_html" => tgLogonHTML($user)
    ]));
  } catch (Exception $e) {
    $err_msg = $e->getMessage();
    $resp = ["error" => $err_msg];
    if ($err_msg == 'Вы забанены!') {
      $resp["new_html"] = TG_BANNED . TG_LOGOUT;
    }
    exit(json_encode(["error" => $e->getMessage()]));
  }
}
elseif (isset($_GET['logout'])) {
  setcookie('tg_user', '', ["path" => '/']);
  exit(json_encode([
    "error" => false,
    "logout" => true,
    "new_html" => TG_WIDGET
  ]));
}
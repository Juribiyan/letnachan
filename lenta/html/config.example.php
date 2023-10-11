<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWD', '');
define('DB_DATABASE', 'lenta');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATION', 'utf8mb4_unicode_ci');

define('SITE_TITLE', 'Lentachan');
define('SITE_SUBTITLE', 'Новости имиджборд');

define('WIPE_TIMEOUT_NEWS', 60);
define('WIPE_TIMEOUT_COMM', 5);

define('ROOT_URL', ''); // внешний адрес сайта (в большинстве случаев лучше оставить пустым)

// настройки подключения к socket.io (должны совпадать со значениями в /scripts/pipe/.env)
define('SOCKETIO_HOST', '127.0.0.1');
define('SOCKETIO_PORT', '9393');
define('SOCKETIO_SRV_TOKEN', '<any_random_shit>');
define('DISABLE_SOCKETIO', false); // Disable client broadcasting for dev purposes

define('CRYPT_SALT', '<any_other_random_shit>');

define('USE_HCAPTCHA', true);
define('HCAPTCHA_SITEKEY', '<your_sitekey>'); // get yours @ https://dashboard.hcaptcha.com/
define('HCAPTCHA_SECRET', '<your_secret_key>');

define('USE_TELEGRAM', true);
define('BOT_ID', '<your_bot_id>');
define('BOT_TOKEN', '<your_bot_token>');

mb_internal_encoding('UTF-8');
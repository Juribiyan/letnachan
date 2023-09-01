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

mb_internal_encoding('UTF-8');
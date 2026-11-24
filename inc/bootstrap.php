<?php
/**
 * Начальная загрузка: настройки, подключение к БД, сессия, вспомогательные функции.
 */
declare(strict_types=1);

define('ROOT', dirname(__DIR__));

if (!is_file(ROOT . '/config.php')) {
    http_response_code(500);
    exit('Не найден файл config.php. Скопируйте config.sample.php в config.php и укажите параметры БД.');
}
$CONFIG = require ROOT . '/config.php';

if (!empty($CONFIG['debug'])) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
}

date_default_timezone_set('Europe/Moscow');
mb_internal_encoding('UTF-8');

// Базовый путь сайта: позволяет запускать его как в корне домена,
// так и в подкаталоге XAMPP (например, http://localhost/lesdep/)
$base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
define('BASE', $base === '/' ? '' : $base);

// Сессия с безопасными параметрами cookie
session_name('LESDEP');
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => BASE . '/',
    'httponly' => true,
    'samesite' => 'Lax',
    'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
]);
session_start();

require ROOT . '/inc/db.php';
require ROOT . '/inc/helpers.php';
require ROOT . '/inc/auth.php';

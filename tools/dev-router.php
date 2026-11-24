<?php
// Маршрутизатор для встроенного веб-сервера PHP (только для разработки):
//   php -S localhost:8000 tools/dev-router.php
// Статические файлы отдаются как есть, остальные запросы – в index.php.
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = dirname(__DIR__) . $path;
if ($path !== '/' && is_file($file) && !preg_match('#^/(inc|pages|templates|sql|tools)/#', $path)) {
    return false;
}
$_SERVER['SCRIPT_NAME'] = '/index.php';
require dirname(__DIR__) . '/index.php';

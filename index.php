<?php
/**
 * Единая точка входа (front controller).
 * Все запросы к несуществующим файлам перенаправляются сюда через .htaccess,
 * здесь адрес разбирается и вызывается обработчик нужной страницы.
 */
declare(strict_types=1);

require __DIR__ . '/inc/bootstrap.php';

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = rawurldecode($path);
if (BASE !== '' && str_starts_with($path, BASE)) {
    $path = substr($path, strlen(BASE));
}
$path = '/' . trim($path, '/');
if ($path === '/index.php') {
    $path = '/';
}
$parts = $path === '/' ? [] : explode('/', trim($path, '/'));

// Разделы со статьями
$sections = ['about', 'news', 'activity', 'citizens', 'documents'];

// Простые страницы: адрес => файл обработчика
$pages = [
    ''            => 'home',
    'appeals'     => 'appeals',
    'contacts'    => 'contacts',
    'search'      => 'search',
    'sitemap'     => 'sitemap',
    'sitemap.xml' => 'sitemap_xml',
    'login'       => 'login',
    'register'    => 'register',
    'logout'      => 'logout',
    'cabinet'     => 'cabinet',
];

$first = $parts[0] ?? '';

if (count($parts) <= 1 && isset($pages[$first])) {
    require ROOT . '/pages/' . $pages[$first] . '.php';
} elseif (in_array($first, $sections, true) && count($parts) === 1) {
    $sectionSlug = $first;
    require ROOT . '/pages/section.php';
} elseif (in_array($first, $sections, true) && count($parts) === 2) {
    $sectionSlug = $first;
    $articleSlug = $parts[1];
    require ROOT . '/pages/article.php';
} elseif ($first === 'appeals' && count($parts) === 2 && ctype_digit($parts[1])) {
    $appealId = (int)$parts[1];
    require ROOT . '/pages/appeal_view.php';
} elseif ($first === 'admin') {
    $adminPage = $parts[1] ?? 'index';
    $allowed = ['index', 'articles', 'article', 'users', 'user', 'appeals', 'banners'];
    if (count($parts) > 2 || !in_array($adminPage, $allowed, true)) {
        not_found();
    }
    require ROOT . '/pages/admin/' . $adminPage . '.php';
} else {
    not_found();
}

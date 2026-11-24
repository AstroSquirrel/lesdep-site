<?php
/** Карта сайта в формате XML (sitemaps.org) для поисковых систем */

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');

$urls = [];
foreach (array_keys(main_menu()) as $href) {
    $urls[] = [$host . url($href), date('Y-m-d')];
}
$rows = q_all('SELECT a.slug, a.published_at, a.updated_at, s.slug AS sec FROM articles a
               JOIN sections s ON s.id = a.section_id WHERE a.is_published = 1');
foreach ($rows as $r) {
    $urls[] = [$host . url('/' . $r['sec'] . '/' . $r['slug']), substr($r['updated_at'] ?: $r['published_at'], 0, 10)];
}
$urls[] = [$host . url('/sitemap'), date('Y-m-d')];

header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as [$loc, $mod]) {
    echo "  <url><loc>" . e($loc) . "</loc><lastmod>$mod</lastmod></url>\n";
}
echo '</urlset>';

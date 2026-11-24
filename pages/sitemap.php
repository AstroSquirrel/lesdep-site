<?php
/** Карта сайта – отдельная страница со всей структурой сайта, строится автоматически из БД */

$sections = q_all('SELECT * FROM sections ORDER BY sort');
$tree = [];
foreach ($sections as $s) {
    $s['items'] = q_all('SELECT title, slug FROM articles WHERE section_id = ? AND is_published = 1
                         ORDER BY ' . ($s['slug'] === 'news' ? 'published_at DESC' : 'published_at ASC, id ASC'), [$s['id']]);
    $tree[$s['slug']] = $s;
}

render('sitemap', [
    'title'  => 'Карта сайта',
    'crumbs' => [['Карта сайта', '']],
    'tree'   => $tree,
]);

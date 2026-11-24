<?php
/** Раздел сайта: список статей раздела (для новостей – лента с постраничной навигацией) */

/** @var string $sectionSlug */
$section = q_one('SELECT * FROM sections WHERE slug = ?', [$sectionSlug]);
if (!$section) {
    not_found();
}

$isNews = $sectionSlug === 'news';
$order  = $isNews ? 'a.published_at DESC' : 'a.published_at ASC, a.id ASC';

$total = (int)q_val('SELECT COUNT(*) FROM articles a WHERE a.section_id = ? AND a.is_published = 1', [$section['id']]);
$pg = paginate($total, $isNews ? (int)config('per_page') : 50, (int)($_GET['page'] ?? 1));

$items = q_all("SELECT a.* FROM articles a WHERE a.section_id = ? AND a.is_published = 1
                ORDER BY $order LIMIT {$pg['limit']} OFFSET {$pg['offset']}", [$section['id']]);

render('section', [
    'title'       => $section['title'],
    'description' => $section['description'],
    'active'      => '/' . $sectionSlug,
    'crumbs'      => [[$section['title'], '']],
    'section'     => $section,
    'items'       => $items,
    'pg'          => $pg,
    'isNews'      => $isNews,
]);

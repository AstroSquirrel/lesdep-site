<?php
/** Страница отдельной статьи (материал раздела, новость или документ) */

/** @var string $sectionSlug @var string $articleSlug */
$section = q_one('SELECT * FROM sections WHERE slug = ?', [$sectionSlug]);
if (!$section) {
    not_found();
}
$article = q_one('SELECT a.*, u.full_name AS author FROM articles a LEFT JOIN users u ON u.id = a.author_id
                  WHERE a.section_id = ? AND a.slug = ? AND (a.is_published = 1 OR ?)',
    [$section['id'], $articleSlug, is_staff() ? 1 : 0]);
if (!$article) {
    not_found();
}

// счётчик просмотров
q_exec('UPDATE articles SET views = views + 1 WHERE id = ?', [$article['id']]);

// другие материалы этого раздела – для боковой навигации
$siblings = q_all('SELECT title, slug FROM articles WHERE section_id = ? AND is_published = 1
                   ORDER BY ' . ($sectionSlug === 'news' ? 'published_at DESC' : 'published_at ASC, id ASC') ,
    [$section['id']]);

render('article', [
    'title'       => $article['title'],
    'description' => $article['summary'],
    'active'      => '/' . $sectionSlug,
    'crumbs'      => [[$section['title'], '/' . $sectionSlug], [$article['title'], '']],
    'section'     => $section,
    'a'           => $article,
    'siblings'    => $siblings,
]);

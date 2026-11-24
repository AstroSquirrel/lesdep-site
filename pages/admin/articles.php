<?php
/** Список материалов сайта с фильтром по разделу; удаление и снятие с публикации */

$user = require_login('admin', 'employee');

if (is_post()) {
    csrf_check();
    $id = (int)($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';
    if ($action === 'toggle') {
        q_exec('UPDATE articles SET is_published = 1 - is_published, updated_at = NOW() WHERE id = ?', [$id]);
        flash('success', 'Статус публикации изменён.');
    } elseif ($action === 'delete' && is_role('admin')) {
        q_exec('DELETE FROM articles WHERE id = ?', [$id]);
        flash('success', 'Материал удалён.');
    }
    redirect('/admin/articles', array_filter(['section' => $_POST['section'] ?? '']));
}

$sections = q_all('SELECT * FROM sections ORDER BY sort');
$filter = str_param('section');
$params = [];
$where = '';
if ($filter !== '') {
    $where = 'WHERE s.slug = ?';
    $params[] = $filter;
}
$items = q_all("SELECT a.id, a.title, a.slug, a.is_published, a.published_at, a.views, s.title AS section_title, s.slug AS section_slug
                FROM articles a JOIN sections s ON s.id = a.section_id $where
                ORDER BY s.sort, a.published_at DESC", $params);

render('admin/articles', [
    'title'       => 'Материалы сайта',
    'crumbs'      => [['Управление сайтом', '/admin'], ['Материалы', '']],
    'adminActive' => 'articles',
    'sections'    => $sections,
    'filter'      => $filter,
    'items'       => $items,
]);

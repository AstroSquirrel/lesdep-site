<?php
/** Баннеры главной страницы (только администратор): включение, порядок, текст и ссылка */

require_login('admin');

if (is_post()) {
    csrf_check();
    foreach ((array)($_POST['b'] ?? []) as $id => $row) {
        q_exec('UPDATE banners SET title = ?, text = ?, link = ?, sort = ?, is_active = ? WHERE id = ?', [
            mb_substr(trim((string)($row['title'] ?? '')), 0, 150),
            mb_substr(trim((string)($row['text'] ?? '')), 0, 300),
            mb_substr(trim((string)($row['link'] ?? '')), 0, 255),
            (int)($row['sort'] ?? 0),
            !empty($row['is_active']) ? 1 : 0,
            (int)$id,
        ]);
    }
    flash('success', 'Баннеры сохранены.');
    redirect('/admin/banners');
}

render('admin/banners', [
    'title'       => 'Баннеры',
    'crumbs'      => [['Управление сайтом', '/admin'], ['Баннеры', '']],
    'adminActive' => 'banners',
    'items'       => q_all('SELECT * FROM banners ORDER BY place, sort'),
]);

<?php
/** Создание и редактирование материала */

$user = require_login('admin', 'employee');
$sections = q_all('SELECT * FROM sections ORDER BY sort');
$id = (int)($_GET['id'] ?? 0);

$a = $id ? q_one('SELECT * FROM articles WHERE id = ?', [$id]) : null;
if ($id && !$a) {
    not_found();
}
if (!$a) {
    $sec = q_one('SELECT id FROM sections WHERE slug = ?', [(string)($_GET['section'] ?? '')]);
    $a = ['id' => 0, 'section_id' => $sec['id'] ?? $sections[0]['id'], 'title' => '', 'slug' => '', 'summary' => '',
          'body' => '', 'image' => null, 'doc_meta' => '', 'is_published' => 1, 'published_at' => date('Y-m-d H:i:s')];
}
$errors = [];

if (is_post()) {
    csrf_check();
    $a['section_id']   = (int)($_POST['section_id'] ?? 0);
    $a['title']        = trim((string)($_POST['title'] ?? ''));
    $a['slug']         = trim((string)($_POST['slug'] ?? ''));
    $a['summary']      = trim((string)($_POST['summary'] ?? ''));
    $a['body']         = sanitize_html((string)($_POST['body'] ?? ''));
    $a['doc_meta']     = trim((string)($_POST['doc_meta'] ?? ''));
    $a['is_published'] = !empty($_POST['is_published']) ? 1 : 0;
    $date              = (string)($_POST['published_at'] ?? '');
    $a['published_at'] = preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $date) ? str_replace('T', ' ', $date) . ':00' : date('Y-m-d H:i:s');

    if (!q_val('SELECT COUNT(*) FROM sections WHERE id = ?', [$a['section_id']])) {
        $errors['section_id'] = 'Выберите раздел.';
    }
    if (mb_strlen($a['title']) < 5) {
        $errors['title'] = 'Заголовок слишком короткий.';
    }
    $a['slug'] = slugify($a['slug'] !== '' ? $a['slug'] : $a['title']);
    if (q_val('SELECT COUNT(*) FROM articles WHERE section_id = ? AND slug = ? AND id <> ?', [$a['section_id'], $a['slug'], $a['id']])) {
        $errors['slug'] = 'Материал с таким адресом в разделе уже есть.';
    }
    if (mb_strlen(strip_tags($a['body'])) < 50) {
        $errors['body'] = 'Текст материала слишком короткий.';
    }

    // загрузка иллюстрации: только JPEG/PNG/WebP до 3 МБ, имя файла формирует сайт
    if (!empty($_FILES['image']['name'])) {
        $f = $_FILES['image'];
        $types = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $mime = $f['error'] === UPLOAD_ERR_OK ? (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']) : '';
        if (!isset($types[$mime]) || $f['size'] > 3 * 1024 * 1024) {
            $errors['image'] = 'Загрузите изображение JPEG, PNG или WebP размером до 3 МБ.';
        } else {
            $name = 'upload-' . date('Ymd-His') . '-' . bin2hex(random_bytes(3)) . '.' . $types[$mime];
            if (move_uploaded_file($f['tmp_name'], ROOT . '/assets/img/articles/' . $name)) {
                $a['image'] = $name;
            }
        }
    } elseif (!empty($_POST['remove_image'])) {
        $a['image'] = null;
    }

    if (!$errors) {
        $fields = [$a['section_id'], $a['slug'], $a['title'], $a['summary'], $a['body'], $a['image'],
                   $a['doc_meta'] !== '' ? $a['doc_meta'] : null, $a['is_published'], $a['published_at']];
        if ($a['id']) {
            q_exec('UPDATE articles SET section_id=?, slug=?, title=?, summary=?, body=?, image=?, doc_meta=?, is_published=?, published_at=?, updated_at=NOW()
                    WHERE id=?', array_merge($fields, [$a['id']]));
        } else {
            q_exec('INSERT INTO articles (section_id, slug, title, summary, body, image, doc_meta, is_published, published_at, author_id)
                    VALUES (?,?,?,?,?,?,?,?,?,?)', array_merge($fields, [$user['id']]));
            $a['id'] = (int)db()->lastInsertId();
        }
        flash('success', 'Материал сохранён.');
        redirect('/admin/article', ['id' => $a['id']]);
    }
}

$secSlug = '';
foreach ($sections as $s) {
    if ((int)$s['id'] === (int)$a['section_id']) {
        $secSlug = $s['slug'];
    }
}

render('admin/article', [
    'title'       => $a['id'] ? 'Редактирование материала' : 'Новый материал',
    'crumbs'      => [['Управление сайтом', '/admin'], ['Материалы', '/admin/articles'], [$a['id'] ? 'Редактирование' : 'Новый материал', '']],
    'adminActive' => 'articles',
    'sections'    => $sections,
    'a'           => $a,
    'secSlug'     => $secSlug,
    'errors'      => $errors,
]);

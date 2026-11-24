<?php /** @var array $sections @var string $filter @var array $items */ ?>
<div class="container">
    <h1 class="page-title">Материалы сайта</h1>
    <?php require ROOT . '/templates/admin/_nav.php'; ?>
    <div class="toolbar">
        <form method="get" action="<?= url('/admin/articles') ?>" class="toolbar__filter">
            <label>Раздел:
                <select name="section" onchange="this.form.submit()">
                    <option value="">Все разделы</option>
                    <?php foreach ($sections as $s): ?>
                        <option value="<?= e($s['slug']) ?>"<?= $filter === $s['slug'] ? ' selected' : '' ?>><?= e($s['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <noscript><button class="btn btn--small">Показать</button></noscript>
        </form>
        <a class="btn btn--small" href="<?= url('/admin/article', array_filter(['section' => $filter])) ?>">+ Добавить материал</a>
    </div>
    <div class="table-wrap">
        <table class="table table--admin">
            <thead><tr><th>Заголовок</th><th>Раздел</th><th>Дата</th><th>Просмотры</th><th>Статус</th><th>Действия</th></tr></thead>
            <tbody>
            <?php foreach ($items as $a): ?>
                <tr>
                    <td><a href="<?= url('/' . $a['section_slug'] . '/' . $a['slug']) ?>"><?= e($a['title']) ?></a></td>
                    <td><?= e($a['section_title']) ?></td>
                    <td><?= date('d.m.Y', strtotime($a['published_at'])) ?></td>
                    <td><?= (int)$a['views'] ?></td>
                    <td><?= $a['is_published'] ? '<span class="status status--answered">Опубликован</span>' : '<span class="status status--closed">Черновик</span>' ?></td>
                    <td class="actions">
                        <a href="<?= url('/admin/article', ['id' => $a['id']]) ?>">Изменить</a>
                        <form method="post" action="<?= url('/admin/articles') ?>">
                            <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$a['id'] ?>"><input type="hidden" name="section" value="<?= e($filter) ?>">
                            <button class="linklike" name="action" value="toggle"><?= $a['is_published'] ? 'Снять' : 'Опубликовать' ?></button>
                            <?php if (is_role('admin')): ?>
                                <button class="linklike linklike--danger" name="action" value="delete" data-confirm="Удалить материал «<?= e($a['title']) ?>»?">Удалить</button>
                            <?php endif; ?>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

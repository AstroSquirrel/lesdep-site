<?php
/** Меню раздела управления. Пункты зависят от роли пользователя. */
$adminActive = $adminActive ?? '';
$navItems = [
    'index'    => ['/admin', 'Сводка'],
    'articles' => ['/admin/articles', 'Материалы'],
    'appeals'  => ['/admin/appeals', 'Обращения'],
    'users'    => ['/admin/users', is_role('admin') ? 'Пользователи' : 'Учётные записи граждан'],
];
if (is_role('admin')) {
    $navItems['banners'] = ['/admin/banners', 'Баннеры'];
}
$navUser = current_user();
?>
<div class="admin-bar">
    <nav class="admin-nav" aria-label="Меню управления">
        <?php foreach ($navItems as $k => [$href, $name]): ?>
            <a href="<?= url($href) ?>"<?= $adminActive === $k ? ' class="is-active"' : '' ?>><?= e($name) ?></a>
        <?php endforeach; ?>
    </nav>
    <div class="admin-bar__user"><?= e($navUser['full_name']) ?> · <?= e(role_name($navUser['role'])) ?></div>
</div>

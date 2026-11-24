<?php /** @var array $stats @var array $bySection @var array $lastAppeals */ ?>
<div class="container">
    <h1 class="page-title">Управление сайтом</h1>
    <?php require ROOT . '/templates/admin/_nav.php'; ?>
    <div class="stats">
        <a class="stat" href="<?= url('/admin/articles') ?>"><span class="stat__num"><?= $stats['articles'] ?></span>материалов</a>
        <a class="stat" href="<?= url('/admin/appeals') ?>"><span class="stat__num"><?= $stats['new'] ?></span>обращений ждут ответа</a>
        <a class="stat" href="<?= url('/admin/appeals') ?>"><span class="stat__num"><?= $stats['appeals'] ?></span>обращений всего</a>
        <a class="stat" href="<?= url('/admin/users') ?>"><span class="stat__num"><?= $stats['users'] ?></span>пользователей</a>
    </div>
    <div class="admin-cols">
        <section>
            <h2>Материалы по разделам</h2>
            <table class="table">
                <thead><tr><th>Раздел</th><th>Материалов</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($bySection as $s): ?>
                    <tr><td><?= e($s['title']) ?></td><td><?= (int)$s['cnt'] ?></td>
                        <td><a href="<?= url('/admin/articles', ['section' => $s['slug']]) ?>">Открыть</a></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <p><a class="btn btn--small" href="<?= url('/admin/article') ?>">+ Добавить материал</a></p>
        </section>
        <section>
            <h2>Последние обращения</h2>
            <?php if (!$lastAppeals): ?><p>Обращений пока нет.</p><?php else: ?>
            <table class="table">
                <thead><tr><th>№</th><th>Тема</th><th>Статус</th></tr></thead>
                <tbody>
                <?php foreach ($lastAppeals as $a): ?>
                    <tr><td><?= (int)$a['id'] ?></td>
                        <td><a href="<?= url('/appeals/' . $a['id']) ?>"><?= e($a['subject']) ?></a><br><small><?= e($a['full_name']) ?></small></td>
                        <td><span class="status status--<?= e($a['status']) ?>"><?= e(appeal_status($a['status'])) ?></span></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </section>
    </div>
</div>

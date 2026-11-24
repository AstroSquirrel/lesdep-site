<?php /** @var array $items @var string $status */
$tabs = ['active' => 'Требуют ответа', 'answered' => 'Дан ответ', 'closed' => 'Закрытые', 'all' => 'Все']; ?>
<div class="container">
    <h1 class="page-title">Обращения граждан</h1>
    <?php require ROOT . '/templates/admin/_nav.php'; ?>
    <nav class="tabs" aria-label="Фильтр по статусу">
        <?php foreach ($tabs as $k => $name): ?>
            <a href="<?= url('/admin/appeals', ['status' => $k]) ?>"<?= $status === $k ? ' class="is-active"' : '' ?>><?= e($name) ?></a>
        <?php endforeach; ?>
    </nav>
    <?php if (!$items): ?>
        <p>Обращений с таким статусом нет.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table class="table table--admin">
                <thead><tr><th>№</th><th>Тема и тематика</th><th>Заявитель</th><th>Статус</th><th>Исполнитель</th><th>Сообщ.</th><th>Обновлено</th></tr></thead>
                <tbody>
                <?php foreach ($items as $a): ?>
                    <tr>
                        <td><?= (int)$a['id'] ?></td>
                        <td><a href="<?= url('/appeals/' . $a['id']) ?>"><?= e($a['subject']) ?></a><br><small><?= e($a['category']) ?></small></td>
                        <td><?= e($a['full_name']) ?></td>
                        <td><span class="status status--<?= e($a['status']) ?>"><?= e(appeal_status($a['status'])) ?></span></td>
                        <td><?= $a['assignee'] ? e($a['assignee']) : '—' ?></td>
                        <td><?= (int)$a['msgs'] ?></td>
                        <td><?= date('d.m.Y H:i', strtotime($a['updated_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

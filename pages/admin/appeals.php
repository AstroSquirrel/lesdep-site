<?php
/** Обращения граждан: список для сотрудников с фильтром по статусу */

require_login('admin', 'employee');

$status = (string)($_GET['status'] ?? 'active');
$where = '';
$params = [];
if ($status === 'active') {
    $where = "WHERE a.status IN ('new','in_work')";
} elseif (in_array($status, ['new', 'in_work', 'answered', 'closed'], true)) {
    $where = 'WHERE a.status = ?';
    $params[] = $status;
}
$items = q_all("SELECT a.*, u.full_name, e.full_name AS assignee,
                       (SELECT COUNT(*) FROM appeal_messages m WHERE m.appeal_id = a.id) AS msgs
                FROM appeals a JOIN users u ON u.id = a.user_id LEFT JOIN users e ON e.id = a.assigned_to
                $where ORDER BY FIELD(a.status, 'new', 'in_work', 'answered', 'closed'), a.updated_at DESC", $params);

render('admin/appeals', [
    'title'       => 'Обращения граждан',
    'crumbs'      => [['Управление сайтом', '/admin'], ['Обращения', '']],
    'adminActive' => 'appeals',
    'items'       => $items,
    'status'      => $status,
]);

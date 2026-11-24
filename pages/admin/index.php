<?php
/** Панель управления сайтом: сводка */

require_login('admin', 'employee');

$stats = [
    'articles' => (int)q_val('SELECT COUNT(*) FROM articles'),
    'users'    => (int)q_val('SELECT COUNT(*) FROM users'),
    'new'      => (int)q_val("SELECT COUNT(*) FROM appeals WHERE status IN ('new','in_work')"),
    'appeals'  => (int)q_val('SELECT COUNT(*) FROM appeals'),
];
$bySection = q_all('SELECT s.title, s.slug, COUNT(a.id) AS cnt FROM sections s
                    LEFT JOIN articles a ON a.section_id = s.id GROUP BY s.id ORDER BY s.sort');
$lastAppeals = q_all("SELECT a.id, a.subject, a.status, a.updated_at, u.full_name FROM appeals a
                      JOIN users u ON u.id = a.user_id ORDER BY a.updated_at DESC LIMIT 5");

render('admin/index', [
    'title'       => 'Управление сайтом',
    'crumbs'      => [['Управление сайтом', '']],
    'adminActive' => 'index',
    'stats'       => $stats,
    'bySection'   => $bySection,
    'lastAppeals' => $lastAppeals,
]);

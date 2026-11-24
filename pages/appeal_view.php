<?php
/**
 * Переписка по обращению.
 * Автор видит только свои обращения; сотрудники и администратор – все.
 * Ответ сотрудника переводит обращение в статус «Дан ответ», уточняющий вопрос
 * гражданина – обратно в «На рассмотрении».
 */

/** @var int $appealId */
$user = require_login();
$appeal = q_one('SELECT a.*, u.full_name AS author_name, u.email AS author_email, u.phone AS author_phone
                 FROM appeals a JOIN users u ON u.id = a.user_id WHERE a.id = ?', [$appealId]);
if (!$appeal || (!is_staff() && (int)$appeal['user_id'] !== (int)$user['id'])) {
    not_found();
}

$error = '';
if (is_post()) {
    csrf_check();
    $action = $_POST['action'] ?? 'reply';

    if ($action === 'status' && is_staff()) {
        $st = (string)($_POST['status'] ?? '');
        if (in_array($st, ['new', 'in_work', 'answered', 'closed'], true)) {
            q_exec('UPDATE appeals SET status = ?, assigned_to = COALESCE(assigned_to, ?), updated_at = NOW() WHERE id = ?',
                [$st, $user['id'], $appeal['id']]);
            flash('success', 'Статус обращения изменён.');
        }
        redirect('/appeals/' . $appeal['id']);
    }

    $body = trim((string)($_POST['body'] ?? ''));
    if ($appeal['status'] === 'closed') {
        $error = 'Обращение закрыто, новые сообщения добавить нельзя.';
    } elseif (mb_strlen($body) < 2 || mb_strlen($body) > 5000) {
        $error = 'Сообщение должно содержать от 2 до 5000 символов.';
    } else {
        q_exec('INSERT INTO appeal_messages (appeal_id, user_id, body) VALUES (?, ?, ?)', [$appeal['id'], $user['id'], $body]);
        $newStatus = is_staff() ? 'answered' : 'in_work';
        q_exec('UPDATE appeals SET status = ?, updated_at = NOW()' . (is_staff() ? ', assigned_to = COALESCE(assigned_to, ?)' : '') . ' WHERE id = ?',
            is_staff() ? [$newStatus, $user['id'], $appeal['id']] : [$newStatus, $appeal['id']]);
        flash('success', is_staff() ? 'Ответ отправлен заявителю.' : 'Сообщение добавлено в переписку.');
        redirect('/appeals/' . $appeal['id']);
    }
}

$messages = q_all('SELECT m.*, u.full_name, u.role FROM appeal_messages m JOIN users u ON u.id = m.user_id
                   WHERE m.appeal_id = ? ORDER BY m.created_at, m.id', [$appeal['id']]);

render('appeal_view', [
    'title'    => 'Обращение № ' . $appeal['id'],
    'active'   => '/appeals',
    'crumbs'   => [['Обращения граждан', '/appeals'], ['Обращение № ' . $appeal['id'], '']],
    'appeal'   => $appeal,
    'messages' => $messages,
    'error'    => $error,
]);

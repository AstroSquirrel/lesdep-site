<?php
/**
 * Интернет-приёмная: порядок рассмотрения обращений и форма подачи.
 * Обращение могут направить только зарегистрированные пользователи – так ответ
 * приходит в личный кабинет, а переписка по обращению сохраняется (система обмена сообщениями).
 */

$user = current_user();
$errors = [];
$d = ['category' => '', 'subject' => '', 'body' => ''];

if (is_post()) {
    $user = require_login();
    csrf_check();
    foreach (array_keys($d) as $k) {
        $d[$k] = trim(str_param($k));
    }
    if (!in_array($d['category'], appeal_categories(), true)) {
        $errors['category'] = 'Выберите тематику обращения.';
    }
    if (mb_strlen($d['subject']) < 5 || mb_strlen($d['subject']) > 200) {
        $errors['subject'] = 'Тема: от 5 до 200 символов.';
    }
    if (mb_strlen($d['body']) < 20) {
        $errors['body'] = 'Опишите суть обращения подробнее (не менее 20 символов).';
    } elseif (mb_strlen($d['body']) > 5000) {
        $errors['body'] = 'Текст обращения не должен превышать 5000 символов.';
    }
    if (!$errors) {
        db()->beginTransaction();
        q_exec('INSERT INTO appeals (user_id, category, subject) VALUES (?, ?, ?)', [$user['id'], $d['category'], $d['subject']]);
        $id = (int)db()->lastInsertId();
        q_exec('INSERT INTO appeal_messages (appeal_id, user_id, body) VALUES (?, ?, ?)', [$id, $user['id'], $d['body']]);
        db()->commit();
        flash('success', "Обращение № $id зарегистрировано. Ответ появится на этой странице и в личном кабинете.");
        redirect('/appeals/' . $id);
    }
}

render('appeals', [
    'title'  => 'Обращения граждан',
    'active' => '/appeals',
    'crumbs' => [['Обращения граждан', '']],
    'user'   => $user,
    'd'      => $d,
    'errors' => $errors,
]);

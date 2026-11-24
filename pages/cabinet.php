<?php
/** Личный кабинет: данные профиля, смена пароля, список своих обращений */

$user = require_login();
$errors = [];

if (is_post()) {
    csrf_check();
    $action = $_POST['action'] ?? '';
    if ($action === 'profile') {
        $d = [
            'login'     => $user['login'],
            'email'     => trim(str_param('email')),
            'full_name' => trim(str_param('full_name')),
            'phone'     => trim(str_param('phone')),
        ];
        $errors = validate_user($d, false, (int)$user['id']);
        if (!$errors) {
            q_exec('UPDATE users SET email = ?, full_name = ?, phone = ? WHERE id = ?',
                [$d['email'], $d['full_name'], $d['phone'] !== '' ? $d['phone'] : null, $user['id']]);
            flash('success', 'Данные профиля сохранены.');
            redirect('/cabinet');
        }
    } elseif ($action === 'password') {
        $row = q_one('SELECT password_hash FROM users WHERE id = ?', [$user['id']]);
        $d = ['login' => $user['login'], 'email' => $user['email'], 'full_name' => $user['full_name'],
              'password' => str_param('password'), 'password2' => str_param('password2')];
        if (!password_verify(str_param('old_password'), $row['password_hash'])) {
            $errors['old_password'] = 'Текущий пароль введён неверно.';
        } else {
            $errors = array_intersect_key(validate_user($d, true, (int)$user['id']), ['password' => 1, 'password2' => 1]);
        }
        if (!$errors) {
            q_exec('UPDATE users SET password_hash = ?, must_change_password = 0 WHERE id = ?', [password_hash($d['password'], PASSWORD_DEFAULT), $user['id']]);
            flash('success', 'Пароль изменён.');
            redirect('/cabinet');
        }
    }
}

$appeals = q_all('SELECT a.*, (SELECT COUNT(*) FROM appeal_messages m WHERE m.appeal_id = a.id) AS msgs
                  FROM appeals a WHERE a.user_id = ? ORDER BY a.updated_at DESC', [$user['id']]);

render('cabinet', [
    'title'   => 'Личный кабинет',
    'crumbs'  => [['Личный кабинет', '']],
    'user'    => $user,
    'appeals' => $appeals,
    'errors'  => $errors,
]);

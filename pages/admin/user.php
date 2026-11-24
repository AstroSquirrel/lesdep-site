<?php
/** Изменение учётной записи: роль, блокировка, сброс пароля */

$me = require_login('admin', 'employee');
$isAdmin = is_role('admin');
$u = q_one('SELECT * FROM users WHERE id = ?', [(int)($_GET['id'] ?? 0)]);
// сотрудник может управлять только записями граждан
if (!$u || (!$isAdmin && $u['role'] !== 'citizen')) {
    not_found();
}
$errors = [];

if (is_post()) {
    csrf_check();
    $action = $_POST['action'] ?? '';
    if ($action === 'save') {
        $d = ['login' => $u['login'], 'email' => trim(str_param('email')),
              'full_name' => trim(str_param('full_name')), 'phone' => trim(str_param('phone'))];
        $errors = validate_user($d, false, (int)$u['id']);
        $role = $u['role'];
        $active = $u['is_active'];
        if ($isAdmin && (int)$u['id'] !== (int)$me['id']) {
            // администратор не может понизить или заблокировать сам себя
            $role = in_array($_POST['role'] ?? '', ['admin', 'employee', 'citizen'], true) ? $_POST['role'] : $u['role'];
            $active = !empty($_POST['is_active']) ? 1 : 0;
        } elseif (!$isAdmin) {
            $active = !empty($_POST['is_active']) ? 1 : 0;
        }
        if (!$errors) {
            q_exec('UPDATE users SET email = ?, full_name = ?, phone = ?, role = ?, is_active = ? WHERE id = ?',
                [$d['email'], $d['full_name'], $d['phone'] !== '' ? $d['phone'] : null, $role, $active, $u['id']]);
            flash('success', 'Изменения сохранены.');
            redirect('/admin/user', ['id' => $u['id']]);
        }
        $u = array_merge($u, $d);
    } elseif ($action === 'reset') {
        $p = generate_password();
        q_exec('UPDATE users SET password_hash = ?, must_change_password = 1 WHERE id = ?', [password_hash($p, PASSWORD_DEFAULT), $u['id']]);
        flash('success', "Новый временный пароль для «{$u['login']}»: $p");
        redirect('/admin/user', ['id' => $u['id']]);
    }
}

$appeals = (int)q_val('SELECT COUNT(*) FROM appeals WHERE user_id = ?', [$u['id']]);

render('admin/user', [
    'title'       => 'Учётная запись ' . $u['login'],
    'crumbs'      => [['Управление сайтом', '/admin'], ['Пользователи', '/admin/users'], [$u['login'], '']],
    'adminActive' => 'users',
    'u'           => $u,
    'me'          => $me,
    'isAdmin'     => $isAdmin,
    'errors'      => $errors,
    'appeals'     => $appeals,
]);

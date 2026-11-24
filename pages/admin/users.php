<?php
/**
 * Учётные записи пользователей.
 * Сценарий создания учётной записи сотрудником (например, на личном приёме гражданина):
 * сотрудник вводит ФИО, логин и почту → система проверяет данные и формирует временный
 * пароль → пароль один раз показывается сотруднику для передачи гражданину →
 * гражданин входит и меняет пароль в личном кабинете.
 * Администратор может создавать записи с любой ролью, сотрудник – только с ролью «Гражданин».
 */

$me = require_login('admin', 'employee');
$isAdmin = is_role('admin');
$errors = [];
$d = ['full_name' => '', 'login' => '', 'email' => '', 'phone' => '', 'role' => 'citizen'];

if (is_post()) {
    csrf_check();
    foreach (array_keys($d) as $k) {
        $d[$k] = trim((string)($_POST[$k] ?? ''));
    }
    if (!$isAdmin || !in_array($d['role'], ['admin', 'employee', 'citizen'], true)) {
        $d['role'] = 'citizen';
    }
    $manual = trim((string)($_POST['password'] ?? ''));
    $d['password'] = $d['password2'] = $manual !== '' ? $manual : generate_password();
    $errors = validate_user($d);
    if (!$errors) {
        $id = create_user($d, $d['role'], (int)$me['id']);
        flash('success', sprintf('Учётная запись «%s» (%s) создана. Пароль для первого входа: %s — передайте его владельцу и попросите сменить пароль в личном кабинете.',
            $d['login'], role_name($d['role']), $d['password']));
        redirect('/admin/users');
    }
}

$users = $isAdmin
    ? q_all('SELECT u.*, c.login AS creator FROM users u LEFT JOIN users c ON c.id = u.created_by ORDER BY FIELD(u.role, "admin", "employee", "citizen"), u.id')
    : q_all("SELECT u.*, c.login AS creator FROM users u LEFT JOIN users c ON c.id = u.created_by WHERE u.role = 'citizen' ORDER BY u.id");

render('admin/users', [
    'title'       => $isAdmin ? 'Пользователи' : 'Учётные записи граждан',
    'crumbs'      => [['Управление сайтом', '/admin'], ['Пользователи', '']],
    'adminActive' => 'users',
    'users'       => $users,
    'd'           => $d,
    'errors'      => $errors,
    'isAdmin'     => $isAdmin,
]);

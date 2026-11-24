<?php
/** Вход на сайт */

if (current_user()) {
    redirect('/cabinet');
}

$back = (string)($_GET['back'] ?? $_POST['back'] ?? '');
// возвращаем пользователя только на страницы этого же сайта
if ($back === '' || !str_starts_with($back, '/') || str_starts_with($back, '//')) {
    $back = '';
}

$error = '';
$login = '';
if (is_post()) {
    csrf_check();
    $login = trim((string)($_POST['login'] ?? ''));
    if (attempt_login($login, (string)($_POST['password'] ?? ''))) {
        flash('success', 'Вы вошли на сайт.');
        if ($back !== '') {
            header('Location: ' . $back);
            exit;
        }
        redirect(is_staff() ? '/admin' : '/cabinet');
    }
    $error = 'Неверный логин или пароль. После пяти неудачных попыток вход блокируется на 10 минут.';
}

render('login', [
    'title'  => 'Вход на сайт',
    'crumbs' => [['Вход', '']],
    'error'  => $error,
    'login'  => $login,
    'back'   => $back,
]);

<?php
/** Вход на сайт */

if (current_user()) {
    redirect('/cabinet');
}

// возвращаем пользователя только на страницы этого же сайта
$back = safe_back(str_param('back'));

$error = '';
$login = '';
if (is_post()) {
    csrf_check();
    $login = trim(str_param('login'));
    $result = attempt_login($login, str_param('password'));
    if ($result === 'ok') {
        if (current_user()['must_change_password']) {
            flash('info', 'Вы вошли с временным паролем. Придумайте новый пароль, чтобы продолжить работу с сайтом.');
            redirect('/cabinet');
        }
        flash('success', 'Вы вошли на сайт.');
        if ($back !== '') {
            header('Location: ' . $back);
            exit;
        }
        redirect(is_staff() ? '/admin' : '/cabinet');
    }
    $error = $result === 'blocked'
        ? 'Слишком много неудачных попыток входа. Попробуйте снова через 10 минут.'
        : 'Неверный логин или пароль. После пяти неудачных попыток вход блокируется на 10 минут.';
}

render('login', [
    'title'  => 'Вход на сайт',
    'crumbs' => [['Вход', '']],
    'error'  => $error,
    'login'  => $login,
    'back'   => $back,
]);

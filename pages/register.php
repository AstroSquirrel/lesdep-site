<?php
/**
 * Самостоятельная регистрация гражданина.
 * Сценарий: посетитель заполняет форму → данные проверяются → создаётся запись
 * с ролью «Гражданин» → выполняется вход → переход в личный кабинет.
 */

if (current_user()) {
    redirect('/cabinet');
}

$d = ['login' => '', 'email' => '', 'full_name' => '', 'phone' => ''];
$errors = [];

if (is_post()) {
    csrf_check();
    foreach (['login', 'email', 'full_name', 'phone', 'password', 'password2'] as $k) {
        $d[$k] = trim((string)($_POST[$k] ?? ''));
    }
    $errors = validate_user($d);
    if (empty($_POST['agree'])) {
        $errors['agree'] = 'Необходимо согласие на обработку персональных данных.';
    }
    if (!$errors) {
        $id = create_user($d, 'citizen', null);
        session_regenerate_id(true);
        $_SESSION['uid'] = $id;
        flash('success', 'Регистрация прошла успешно. Теперь вы можете направлять обращения в департамент.');
        redirect('/cabinet');
    }
}

render('register', [
    'title'  => 'Регистрация',
    'crumbs' => [['Регистрация', '']],
    'd'      => $d,
    'errors' => $errors,
]);

<?php
/**
 * Учётные записи и права доступа.
 *
 * Роли:
 *  admin    – администратор: управление всеми пользователями и ролями, материалами, баннерами, обращениями;
 *  employee – сотрудник департамента: материалы сайта, ответы на обращения, создание учётных записей граждан;
 *  citizen  – гражданин: личный кабинет, подача обращений и переписка по ним.
 */
declare(strict_types=1);

function current_user(bool $reload = false): ?array
{
    static $user = false;
    if ($user === false || $reload) {
        $user = null;
        if (!empty($_SESSION['uid'])) {
            $user = q_one('SELECT id, login, email, full_name, phone, role, is_active FROM users WHERE id = ?',
                [(int)$_SESSION['uid']]);
            if (!$user || !$user['is_active']) {
                unset($_SESSION['uid']);
                $user = null;
            }
        }
    }
    return $user;
}

function is_role(string ...$roles): bool
{
    $u = current_user();
    return $u !== null && in_array($u['role'], $roles, true);
}

function is_staff(): bool
{
    return is_role('admin', 'employee');
}

/** Требовать вход; если указаны роли – ещё и нужную роль */
function require_login(string ...$roles): array
{
    $u = current_user();
    if ($u === null) {
        flash('info', 'Войдите на сайт, чтобы продолжить.');
        redirect('/login', ['back' => $_SERVER['REQUEST_URI'] ?? '/']);
    }
    if ($roles && !in_array($u['role'], $roles, true)) {
        http_response_code(403);
        render('message', [
            'title'  => 'Доступ запрещён',
            'text'   => 'У вашей учётной записи нет прав для просмотра этой страницы.',
            'crumbs' => [],
        ]);
        exit;
    }
    return $u;
}

/** Проверка логина и пароля, вход */
function attempt_login(string $login, string $password): bool
{
    // защита от подбора пароля: не более 5 неудачных попыток за 10 минут в рамках сессии
    $fails = array_filter($_SESSION['login_fails'] ?? [], fn($t) => $t > time() - 600);
    if (count($fails) >= 5) {
        return false;
    }
    $u = q_one('SELECT id, password_hash, is_active FROM users WHERE login = ? OR email = ?', [$login, $login]);
    if ($u && $u['is_active'] && password_verify($password, $u['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['uid'] = (int)$u['id'];
        $_SESSION['login_fails'] = [];
        current_user(true);
        q_exec('UPDATE users SET last_login_at = NOW() WHERE id = ?', [$u['id']]);
        return true;
    }
    $fails[] = time();
    $_SESSION['login_fails'] = $fails;
    return false;
}

function logout(): void
{
    $_SESSION = [];
    session_regenerate_id(true);
    current_user(true);
}

/**
 * Проверка данных новой учётной записи. Возвращает массив ошибок (пустой – всё верно).
 * Используется и при самостоятельной регистрации, и при создании записи сотрудником.
 */
function validate_user(array $d, bool $needPassword = true, ?int $exceptId = null): array
{
    $err = [];
    if (!preg_match('/^[A-Za-z0-9_.-]{3,40}$/', $d['login'] ?? '')) {
        $err['login'] = 'Логин: от 3 до 40 символов, латинские буквы, цифры, точка, дефис или подчёркивание.';
    } elseif (q_val('SELECT COUNT(*) FROM users WHERE login = ? AND id <> ?', [$d['login'], $exceptId ?? 0])) {
        $err['login'] = 'Такой логин уже занят.';
    }
    if (!filter_var($d['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
        $err['email'] = 'Укажите корректный адрес электронной почты.';
    } elseif (q_val('SELECT COUNT(*) FROM users WHERE email = ? AND id <> ?', [$d['email'], $exceptId ?? 0])) {
        $err['email'] = 'Пользователь с такой почтой уже зарегистрирован.';
    }
    if (mb_strlen(trim($d['full_name'] ?? '')) < 5) {
        $err['full_name'] = 'Укажите фамилию, имя и отчество.';
    }
    if (($d['phone'] ?? '') !== '' && !preg_match('/^[0-9+()\-\s]{6,20}$/', $d['phone'])) {
        $err['phone'] = 'Телефон указан в неверном формате.';
    }
    if ($needPassword) {
        $p = $d['password'] ?? '';
        if (mb_strlen($p) < 8 || !preg_match('/[A-Za-zА-Яа-я]/u', $p) || !preg_match('/\d/', $p)) {
            $err['password'] = 'Пароль: не короче 8 символов, обязательно буквы и цифры.';
        } elseif ($p !== ($d['password2'] ?? '')) {
            $err['password2'] = 'Пароли не совпадают.';
        }
    }
    return $err;
}

function create_user(array $d, string $role, ?int $createdBy): int
{
    q_exec('INSERT INTO users (login, email, password_hash, full_name, phone, role, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?)', [
        $d['login'], $d['email'], password_hash($d['password'], PASSWORD_DEFAULT),
        trim($d['full_name']), ($d['phone'] ?? '') !== '' ? $d['phone'] : null, $role, $createdBy,
    ]);
    return (int)db()->lastInsertId();
}

/** Временный пароль для учётной записи, созданной сотрудником */
function generate_password(): string
{
    $abc = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
    $p = '';
    for ($i = 0; $i < 6; $i++) {
        $p .= $abc[random_int(0, strlen($abc) - 1)];
    }
    return $p . random_int(10, 99);
}

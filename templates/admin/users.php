<?php /** @var array $users @var array $d @var array $errors @var bool $isAdmin */
$err = fn($k) => isset($errors[$k]) ? '<span class="form__error">' . e($errors[$k]) . '</span>' : ''; ?>
<div class="container">
    <h1 class="page-title"><?= $isAdmin ? 'Пользователи' : 'Учётные записи граждан' ?></h1>
    <?php require ROOT . '/templates/admin/_nav.php'; ?>
    <div class="admin-cols admin-cols--wide">
        <section>
            <div class="table-wrap">
                <table class="table table--admin">
                    <thead><tr><th>Логин</th><th>ФИО</th><th>Роль</th><th>Создан</th><th>Последний вход</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr class="<?= $u['is_active'] ? '' : 'is-disabled' ?>">
                            <td><?= e($u['login']) ?></td>
                            <td><?= e($u['full_name']) ?><br><small><?= e($u['email']) ?></small></td>
                            <td><span class="role role--<?= e($u['role']) ?>"><?= e(role_name($u['role'])) ?></span><?= $u['is_active'] ? '' : '<br><small>заблокирован</small>' ?></td>
                            <td><?= date('d.m.Y', strtotime($u['created_at'])) ?><br><small><?= $u['creator'] ? 'сотрудником ' . e($u['creator']) : 'самостоятельно' ?></small></td>
                            <td><?= $u['last_login_at'] ? date('d.m.Y H:i', strtotime($u['last_login_at'])) : '—' ?></td>
                            <td><a href="<?= url('/admin/user', ['id' => $u['id']]) ?>">Изменить</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <section class="form-card form-card--small">
            <h2>Создать учётную запись</h2>
            <p class="muted">Если поле «Пароль» оставить пустым, система сформирует временный пароль автоматически.</p>
            <form method="post" action="<?= url('/admin/users') ?>" class="form">
                <?= csrf_field() ?>
                <label class="form__row"><span>ФИО *</span><input type="text" name="full_name" value="<?= e($d['full_name']) ?>" required><?= $err('full_name') ?></label>
                <label class="form__row"><span>Логин *</span><input type="text" name="login" value="<?= e($d['login']) ?>" required><?= $err('login') ?></label>
                <label class="form__row"><span>Электронная почта *</span><input type="email" name="email" value="<?= e($d['email']) ?>" required><?= $err('email') ?></label>
                <label class="form__row"><span>Телефон</span><input type="tel" name="phone" value="<?= e($d['phone']) ?>"><?= $err('phone') ?></label>
                <?php if ($isAdmin): ?>
                    <label class="form__row"><span>Роль</span>
                        <select name="role">
                            <?php foreach (['citizen', 'employee', 'admin'] as $r): ?>
                                <option value="<?= $r ?>"<?= $d['role'] === $r ? ' selected' : '' ?>><?= e(role_name($r)) ?></option>
                            <?php endforeach; ?>
                        </select></label>
                <?php else: ?>
                    <p class="muted">Роль: Гражданин</p>
                <?php endif; ?>
                <label class="form__row"><span>Пароль</span><input type="text" name="password" autocomplete="off" placeholder="сформировать автоматически"><?= $err('password') ?></label>
                <button type="submit" class="btn btn--small">Создать</button>
            </form>
        </section>
    </div>
</div>

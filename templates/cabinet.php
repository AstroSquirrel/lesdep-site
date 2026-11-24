<?php /** @var array $user @var array $appeals @var array $errors */
$err = fn($k) => isset($errors[$k]) ? '<span class="form__error">' . e($errors[$k]) . '</span>' : ''; ?>
<div class="container">
    <h1 class="page-title">Личный кабинет</h1>
    <?php if ($user['must_change_password']): ?>
        <div class="alert alert--info" role="alert">Учётная запись создана сотрудником департамента с временным паролем. Задайте собственный пароль в форме «Смена пароля» — после этого станут доступны все разделы сайта.</div>
    <?php endif; ?>
    <div class="cabinet">
        <section class="cabinet__main">
            <div class="block-head">
                <h2>Мои обращения</h2>
                <a class="btn" href="<?= url('/appeals') ?>#form">Новое обращение</a>
            </div>
            <?php if (!$appeals): ?>
                <p>Вы ещё не направляли обращений.</p>
            <?php else: ?>
                <div class="table-wrap">
                    <table class="table">
                        <thead><tr><th>№</th><th>Тема</th><th>Статус</th><th>Сообщений</th><th>Обновлено</th></tr></thead>
                        <tbody>
                        <?php foreach ($appeals as $a): ?>
                            <tr>
                                <td><?= (int)$a['id'] ?></td>
                                <td><a href="<?= url('/appeals/' . $a['id']) ?>"><?= e($a['subject']) ?></a><br><small><?= e($a['category']) ?></small></td>
                                <td><span class="status status--<?= e($a['status']) ?>"><?= e(appeal_status($a['status'])) ?></span></td>
                                <td><?= (int)$a['msgs'] ?></td>
                                <td><?= ru_date($a['updated_at'], true) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
        <aside class="cabinet__side">
            <div class="form-card form-card--small">
                <h2>Профиль</h2>
                <p class="muted">Логин: <strong><?= e($user['login']) ?></strong><br>Роль: <?= e(role_name($user['role'])) ?></p>
                <form method="post" action="<?= url('/cabinet') ?>" class="form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="profile">
                    <label class="form__row"><span>ФИО</span><input type="text" name="full_name" value="<?= e($user['full_name']) ?>" required><?= $err('full_name') ?></label>
                    <label class="form__row"><span>Электронная почта</span><input type="email" name="email" value="<?= e($user['email']) ?>" required><?= $err('email') ?></label>
                    <label class="form__row"><span>Телефон</span><input type="tel" name="phone" value="<?= e($user['phone']) ?>"><?= $err('phone') ?></label>
                    <button type="submit" class="btn btn--small">Сохранить</button>
                </form>
            </div>
            <div class="form-card form-card--small">
                <h2>Смена пароля</h2>
                <form method="post" action="<?= url('/cabinet') ?>" class="form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="password">
                    <label class="form__row"><span>Текущий пароль</span><input type="password" name="old_password" required autocomplete="current-password"><?= $err('old_password') ?></label>
                    <label class="form__row"><span>Новый пароль</span><input type="password" name="password" required autocomplete="new-password"><?= $err('password') ?></label>
                    <label class="form__row"><span>Повторите пароль</span><input type="password" name="password2" required autocomplete="new-password"><?= $err('password2') ?></label>
                    <button type="submit" class="btn btn--small">Изменить пароль</button>
                </form>
            </div>
        </aside>
    </div>
</div>

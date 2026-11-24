<?php /** @var array $u @var array $me @var bool $isAdmin @var array $errors @var int $appeals */
$err = fn($k) => isset($errors[$k]) ? '<span class="form__error">' . e($errors[$k]) . '</span>' : '';
$self = (int)$u['id'] === (int)$me['id']; ?>
<div class="container">
    <h1 class="page-title">Учётная запись «<?= e($u['login']) ?>»</h1>
    <?php require ROOT . '/templates/admin/_nav.php'; ?>
    <div class="admin-cols">
        <form method="post" class="form form-card" action="<?= url('/admin/user', ['id' => $u['id']]) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="save">
            <p class="muted">Создана <?= ru_date($u['created_at'], true) ?>. Обращений: <?= $appeals ?>.</p>
            <label class="form__row"><span>ФИО</span><input type="text" name="full_name" value="<?= e($u['full_name']) ?>"><?= $err('full_name') ?></label>
            <label class="form__row"><span>Электронная почта</span><input type="email" name="email" value="<?= e($u['email']) ?>"><?= $err('email') ?></label>
            <label class="form__row"><span>Телефон</span><input type="tel" name="phone" value="<?= e($u['phone']) ?>"><?= $err('phone') ?></label>
            <?php if ($isAdmin && !$self): ?>
                <label class="form__row"><span>Роль</span>
                    <select name="role">
                        <?php foreach (['citizen', 'employee', 'admin'] as $r): ?>
                            <option value="<?= $r ?>"<?= $u['role'] === $r ? ' selected' : '' ?>><?= e(role_name($r)) ?></option>
                        <?php endforeach; ?>
                    </select></label>
            <?php else: ?>
                <p>Роль: <strong><?= e(role_name($u['role'])) ?></strong></p>
            <?php endif; ?>
            <?php if (!$self): ?>
                <label class="form__check"><input type="checkbox" name="is_active" value="1"<?= $u['is_active'] ? ' checked' : '' ?>> <span>Учётная запись активна (снимите отметку, чтобы заблокировать вход)</span></label>
            <?php endif; ?>
            <button type="submit" class="btn">Сохранить</button>
        </form>
        <section class="form-card form-card--small">
            <h2>Права роли «<?= e(role_name($u['role'])) ?>»</h2>
            <ul class="check-list">
                <?php if ($u['role'] === 'admin'): ?>
                    <li>управление всеми учётными записями и ролями</li><li>создание, изменение и удаление материалов</li><li>управление баннерами</li><li>работа с обращениями граждан</li>
                <?php elseif ($u['role'] === 'employee'): ?>
                    <li>создание и изменение материалов, снятие с публикации</li><li>ответы на обращения граждан, смена статуса</li><li>создание учётных записей граждан, сброс их паролей</li>
                <?php else: ?>
                    <li>личный кабинет и изменение своих данных</li><li>подача обращений и переписка по ним</li>
                <?php endif; ?>
            </ul>
            <form method="post" action="<?= url('/admin/user', ['id' => $u['id']]) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="reset">
                <button type="submit" class="btn btn--small btn--ghost" data-confirm="Сбросить пароль пользователя <?= e($u['login']) ?>?">Сбросить пароль</button>
            </form>
        </section>
    </div>
</div>

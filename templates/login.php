<?php /** @var string $error @var string $login @var string $back */ ?>
<div class="container narrow">
    <div class="form-card">
        <h1 class="page-title">Вход на сайт</h1>
        <p>Войдите, чтобы направить обращение в департамент и следить за ответом в личном кабинете.</p>
        <?php if ($error): ?><div class="alert alert--error" role="alert"><?= e($error) ?></div><?php endif; ?>
        <form method="post" action="<?= url('/login') ?>" class="form">
            <?= csrf_field() ?>
            <input type="hidden" name="back" value="<?= e($back) ?>">
            <label class="form__row">
                <span>Логин или электронная почта</span>
                <input type="text" name="login" value="<?= e($login) ?>" required autocomplete="username">
            </label>
            <label class="form__row">
                <span>Пароль</span>
                <input type="password" name="password" required autocomplete="current-password">
            </label>
            <button type="submit" class="btn">Войти</button>
        </form>
        <p class="form-card__foot">Нет учётной записи? <a href="<?= url('/register') ?>">Зарегистрируйтесь</a> — это займёт минуту.</p>
    </div>
</div>

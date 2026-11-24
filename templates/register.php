<?php /** @var array $d @var array $errors */
$err = fn($k) => isset($errors[$k]) ? '<span class="form__error">' . e($errors[$k]) . '</span>' : ''; ?>
<div class="container narrow">
    <div class="form-card">
        <h1 class="page-title">Регистрация на сайте</h1>
        <p>Учётная запись нужна, чтобы направлять обращения в интернет-приёмную и получать ответы в личном кабинете.</p>
        <?php if ($errors): ?><div class="alert alert--error" role="alert">Проверьте правильность заполнения полей.</div><?php endif; ?>
        <form method="post" action="<?= url('/register') ?>" class="form" novalidate>
            <?= csrf_field() ?>
            <label class="form__row">
                <span>Фамилия, имя, отчество *</span>
                <input type="text" name="full_name" value="<?= e($d['full_name']) ?>" required autocomplete="name">
                <?= $err('full_name') ?>
            </label>
            <label class="form__row">
                <span>Логин * <small>(латинские буквы и цифры)</small></span>
                <input type="text" name="login" value="<?= e($d['login']) ?>" required autocomplete="username">
                <?= $err('login') ?>
            </label>
            <div class="form__cols">
                <label class="form__row">
                    <span>Электронная почта *</span>
                    <input type="email" name="email" value="<?= e($d['email']) ?>" required autocomplete="email">
                    <?= $err('email') ?>
                </label>
                <label class="form__row">
                    <span>Телефон</span>
                    <input type="tel" name="phone" value="<?= e($d['phone']) ?>" autocomplete="tel" placeholder="+7 (900) 000-00-00">
                    <?= $err('phone') ?>
                </label>
            </div>
            <div class="form__cols">
                <label class="form__row">
                    <span>Пароль * <small>(не менее 8 символов, буквы и цифры)</small></span>
                    <input type="password" name="password" required autocomplete="new-password">
                    <?= $err('password') ?>
                </label>
                <label class="form__row">
                    <span>Повторите пароль *</span>
                    <input type="password" name="password2" required autocomplete="new-password">
                    <?= $err('password2') ?>
                </label>
            </div>
            <label class="form__check">
                <input type="checkbox" name="agree" value="1" <?= !empty($_POST['agree']) ? 'checked' : '' ?>>
                <span>Даю согласие на обработку персональных данных в соответствии с Федеральным законом от 27.07.2006 № 152-ФЗ «О персональных данных»</span>
            </label>
            <?= $err('agree') ?>
            <button type="submit" class="btn">Зарегистрироваться</button>
        </form>
        <p class="form-card__foot">Уже зарегистрированы? <a href="<?= url('/login') ?>">Войдите</a>.</p>
    </div>
</div>

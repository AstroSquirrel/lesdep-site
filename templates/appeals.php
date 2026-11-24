<?php /** @var array|null $user @var array $d @var array $errors */
$err = fn($k) => isset($errors[$k]) ? '<span class="form__error">' . e($errors[$k]) . '</span>' : ''; ?>
<div class="container">
    <h1 class="page-title">Обращения граждан</h1>
    <p class="page-lead">Интернет-приёмная департамента. Обращения рассматриваются в порядке, установленном Федеральным законом от 02.05.2006 № 59-ФЗ «О порядке рассмотрения обращений граждан Российской Федерации».</p>

    <div class="steps">
        <div class="step"><span class="step__num">1</span><div><strong>Регистрация или вход</strong><br>Учётная запись нужна, чтобы получить ответ и вести переписку.</div></div>
        <div class="step"><span class="step__num">2</span><div><strong>Обращение</strong><br>Выберите тематику, укажите тему и подробно опишите ситуацию.</div></div>
        <div class="step"><span class="step__num">3</span><div><strong>Рассмотрение</strong><br>Обращение регистрируется в течение 3 дней и рассматривается до 30 дней.</div></div>
        <div class="step"><span class="step__num">4</span><div><strong>Ответ</strong><br>Ответ специалиста появится в личном кабинете, по нему можно задать уточняющий вопрос.</div></div>
    </div>

    <div class="appeal-layout">
        <section id="form" class="form-card">
            <h2>Направить обращение</h2>
            <?php if (!$user): ?>
                <p>Чтобы направить обращение, <a href="<?= url('/login', ['back' => url('/appeals')]) ?>">войдите на сайт</a>
                    или <a href="<?= url('/register') ?>">зарегистрируйтесь</a>.</p>
                <p class="muted">О лесном пожаре сообщайте сразу по телефону <a href="tel:88001009400">8-800-100-94-00</a> или 112 — не через форму.</p>
            <?php else: ?>
                <?php if ($errors): ?><div class="alert alert--error" role="alert">Проверьте правильность заполнения полей.</div><?php endif; ?>
                <form method="post" action="<?= url('/appeals') ?>" class="form">
                    <?= csrf_field() ?>
                    <p class="muted">Заявитель: <strong><?= e($user['full_name']) ?></strong>, <?= e($user['email']) ?></p>
                    <label class="form__row">
                        <span>Тематика *</span>
                        <select name="category" required>
                            <option value="">— выберите —</option>
                            <?php foreach (appeal_categories() as $c): ?>
                                <option<?= $d['category'] === $c ? ' selected' : '' ?>><?= e($c) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= $err('category') ?>
                    </label>
                    <label class="form__row">
                        <span>Тема обращения *</span>
                        <input type="text" name="subject" value="<?= e($d['subject']) ?>" maxlength="200" required>
                        <?= $err('subject') ?>
                    </label>
                    <label class="form__row">
                        <span>Текст обращения *</span>
                        <textarea name="body" rows="8" maxlength="5000" required><?= e($d['body']) ?></textarea>
                        <?= $err('body') ?>
                    </label>
                    <button type="submit" class="btn">Отправить обращение</button>
                </form>
            <?php endif; ?>
        </section>
        <aside class="appeal-info">
            <h2>Важно знать</h2>
            <ul class="check-list">
                <li>Обращение должно содержать суть вопроса; анонимные обращения не рассматриваются.</li>
                <li>Если вопрос не относится к ведению департамента, обращение в течение 7 дней будет направлено в орган, к компетенции которого он относится.</li>
                <li>Обращение, содержащее нецензурные выражения или угрозы, может быть оставлено без ответа по существу.</li>
                <li>Ход рассмотрения можно отслеживать в <a href="<?= url('/cabinet') ?>">личном кабинете</a>.</li>
            </ul>
            <div class="side-cta side-cta--static">
                <?= icon('phone', 30) ?>
                <span><strong>Личный приём граждан</strong><br>директор департамента — первый вторник месяца, 14:00–17:00. Запись по телефону +7 (3800) 41-02-05.</span>
            </div>
        </aside>
    </div>
</div>

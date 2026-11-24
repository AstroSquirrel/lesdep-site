<?php /** @var array $appeal @var array $messages @var string $error */ ?>
<div class="container narrow-wide">
    <h1 class="page-title">Обращение № <?= (int)$appeal['id'] ?>: <?= e($appeal['subject']) ?></h1>
    <div class="appeal-head">
        <span class="status status--<?= e($appeal['status']) ?>"><?= e(appeal_status($appeal['status'])) ?></span>
        <span>Тематика: <?= e($appeal['category']) ?></span>
        <span>Подано: <?= ru_date($appeal['created_at'], true) ?></span>
        <?php if (is_staff()): ?>
            <span>Заявитель: <?= e($appeal['author_name']) ?>, <?= e($appeal['author_email']) ?><?= $appeal['author_phone'] ? ', ' . e($appeal['author_phone']) : '' ?></span>
        <?php endif; ?>
    </div>

    <?php if (is_staff()): ?>
        <form method="post" class="status-form" action="<?= url('/appeals/' . $appeal['id']) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="status">
            <label>Статус:
                <select name="status">
                    <?php foreach (['new', 'in_work', 'answered', 'closed'] as $s): ?>
                        <option value="<?= $s ?>"<?= $appeal['status'] === $s ? ' selected' : '' ?>><?= e(appeal_status($s)) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button type="submit" class="btn btn--small btn--ghost">Изменить</button>
        </form>
    <?php endif; ?>

    <div class="chat" aria-label="Переписка по обращению">
        <?php foreach ($messages as $m):
            $staff = in_array($m['role'], ['admin', 'employee'], true); ?>
            <div class="msg<?= $staff ? ' msg--staff' : '' ?>">
                <div class="msg__head">
                    <strong><?= $staff ? 'Департамент лесного хозяйства' : e($m['full_name']) ?></strong>
                    <?php if ($staff): ?><span class="msg__who"><?= e($m['full_name']) ?></span><?php endif; ?>
                    <time><?= ru_date($m['created_at'], true) ?></time>
                </div>
                <div class="msg__body"><?= nl2br(e($m['body'])) ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($appeal['status'] !== 'closed'): ?>
        <form method="post" class="form form-card reply-form" action="<?= url('/appeals/' . $appeal['id']) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="reply">
            <?php if ($error): ?><div class="alert alert--error" role="alert"><?= e($error) ?></div><?php endif; ?>
            <label class="form__row">
                <span><?= is_staff() ? 'Ответ заявителю' : 'Уточняющий вопрос или дополнение' ?></span>
                <textarea name="body" rows="5" maxlength="5000" required></textarea>
            </label>
            <button type="submit" class="btn"><?= is_staff() ? 'Отправить ответ' : 'Отправить сообщение' ?></button>
        </form>
    <?php else: ?>
        <p class="note">Обращение закрыто. Если у вас остались вопросы, направьте <a href="<?= url('/appeals') ?>#form">новое обращение</a>.</p>
    <?php endif; ?>
    <p><a href="<?= url(is_staff() ? '/admin/appeals' : '/cabinet') ?>">← К списку обращений</a></p>
</div>

<?php /** @var array $sections @var array $a @var string $secSlug @var array $errors */
$err = fn($k) => isset($errors[$k]) ? '<span class="form__error">' . e($errors[$k]) . '</span>' : ''; ?>
<div class="container">
    <h1 class="page-title"><?= $a['id'] ? 'Редактирование материала' : 'Новый материал' ?></h1>
    <?php require ROOT . '/templates/admin/_nav.php'; ?>
    <?php if ($a['id'] && $secSlug): ?>
        <p><a href="<?= url('/' . $secSlug . '/' . $a['slug']) ?>" target="_blank">Открыть материал на сайте ↗</a></p>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data" class="form form-card editor" action="<?= url('/admin/article', $a['id'] ? ['id' => $a['id']] : []) ?>">
        <?= csrf_field() ?>
        <div class="form__cols">
            <label class="form__row"><span>Раздел *</span>
                <select name="section_id">
                    <?php foreach ($sections as $s): ?>
                        <option value="<?= (int)$s['id'] ?>"<?= (int)$a['section_id'] === (int)$s['id'] ? ' selected' : '' ?>><?= e($s['title']) ?></option>
                    <?php endforeach; ?>
                </select><?= $err('section_id') ?>
            </label>
            <label class="form__row"><span>Дата публикации</span>
                <input type="datetime-local" name="published_at" value="<?= e(str_replace(' ', 'T', substr($a['published_at'], 0, 16))) ?>">
            </label>
        </div>
        <label class="form__row"><span>Заголовок *</span><input type="text" name="title" value="<?= e($a['title']) ?>" required maxlength="255"><?= $err('title') ?></label>
        <label class="form__row"><span>Адрес страницы <small>(латиницей; если оставить пустым, сформируется из заголовка)</small></span>
            <input type="text" name="slug" value="<?= e($a['slug']) ?>" maxlength="120"><?= $err('slug') ?></label>
        <label class="form__row"><span>Краткое описание (анонс)</span><textarea name="summary" rows="3" maxlength="600"><?= e($a['summary']) ?></textarea></label>
        <label class="form__row"><span>Реквизиты документа <small>(только для раздела «Документы»)</small></span>
            <input type="text" name="doc_meta" value="<?= e($a['doc_meta']) ?>" placeholder="Приказ департамента от 01.01.2025 № 1"></label>
        <label class="form__row"><span>Текст материала * <small>(разрешена разметка: p, h2, h3, ul, ol, li, strong, em, a, table)</small></span>
            <textarea name="body" rows="16" class="editor__body"><?= e($a['body']) ?></textarea><?= $err('body') ?></label>
        <div class="form__cols">
            <label class="form__row"><span>Иллюстрация (JPEG, PNG, WebP до 3 МБ)</span><input type="file" name="image" accept="image/jpeg,image/png,image/webp"><?= $err('image') ?></label>
            <?php if ($a['image']): ?>
                <div class="editor__img">
                    <img src="<?= asset('img/articles/' . $a['image']) ?>" alt="">
                    <label class="form__check"><input type="checkbox" name="remove_image" value="1"> <span>убрать иллюстрацию</span></label>
                </div>
            <?php endif; ?>
        </div>
        <label class="form__check"><input type="checkbox" name="is_published" value="1"<?= $a['is_published'] ? ' checked' : '' ?>> <span>Опубликовать на сайте</span></label>
        <button type="submit" class="btn">Сохранить</button>
    </form>
</div>

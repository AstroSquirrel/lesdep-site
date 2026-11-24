<?php /** @var array $items */ ?>
<div class="container">
    <h1 class="page-title">Баннеры главной страницы</h1>
    <?php require ROOT . '/templates/admin/_nav.php'; ?>
    <form method="post" action="<?= url('/admin/banners') ?>">
        <?= csrf_field() ?>
        <div class="banner-list">
            <?php foreach ($items as $b): $n = 'b[' . (int)$b['id'] . ']'; ?>
                <div class="banner-row form-card form-card--small">
                    <img src="<?= asset('img/' . $b['image']) ?>" alt="">
                    <div class="form">
                        <div class="form__cols">
                            <label class="form__row"><span>Заголовок (<?= $b['place'] === 'slider' ? 'слайдер' : 'боковой баннер' ?>)</span><input type="text" name="<?= $n ?>[title]" value="<?= e($b['title']) ?>"></label>
                            <label class="form__row"><span>Ссылка</span><input type="text" name="<?= $n ?>[link]" value="<?= e($b['link']) ?>"></label>
                        </div>
                        <label class="form__row"><span>Текст</span><input type="text" name="<?= $n ?>[text]" value="<?= e($b['text']) ?>"></label>
                        <div class="form__inline">
                            <label>Порядок <input type="number" name="<?= $n ?>[sort]" value="<?= (int)$b['sort'] ?>" class="input-short"></label>
                            <label class="form__check"><input type="checkbox" name="<?= $n ?>[is_active]" value="1"<?= $b['is_active'] ? ' checked' : '' ?>> <span>Показывать</span></label>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="submit" class="btn">Сохранить</button>
    </form>
</div>

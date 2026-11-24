<?php /** @var string $q @var array $results @var string $error @var callable $highlight */ ?>
<div class="container narrow">
    <h1 class="page-title">Поиск по сайту</h1>
    <form class="search-form search-form--big" action="<?= url('/search') ?>" method="get" role="search">
        <label class="visually-hidden" for="q-big">Поисковый запрос</label>
        <input id="q-big" type="search" name="q" value="<?= e($q) ?>" placeholder="Например: заготовка дров" minlength="3" required>
        <button type="submit" class="btn">Найти</button>
    </form>

    <?php if ($error): ?>
        <div class="alert alert--error"><?= e($error) ?></div>
    <?php elseif ($q !== ''): ?>
        <p class="search-count">По запросу «<?= e($q) ?>» найдено материалов: <strong><?= count($results) ?></strong></p>
        <?php if (!$results): ?>
            <p>Ничего не найдено. Попробуйте изменить запрос или посмотрите <a href="<?= url('/sitemap') ?>">карту сайта</a>.</p>
        <?php endif; ?>
        <ol class="search-results">
            <?php foreach ($results as $r): ?>
                <li>
                    <a class="search-results__title" href="<?= url('/' . $r['section_slug'] . '/' . $r['slug']) ?>"><?= $highlight($r['title']) ?></a>
                    <div class="search-results__meta"><?= e($r['section_title']) ?><?= $r['section_slug'] === 'news' ? ' · ' . ru_date($r['published_at']) : '' ?></div>
                    <p><?= $highlight($r['summary']) ?></p>
                </li>
            <?php endforeach; ?>
        </ol>
    <?php else: ?>
        <p>Введите запрос, чтобы найти новости, документы и полезные материалы департамента.</p>
    <?php endif; ?>
</div>

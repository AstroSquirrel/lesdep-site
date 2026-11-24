<?php /** @var array $section @var array $a @var array $siblings */
$slug = $section['slug']; ?>
<div class="container article-layout">
    <article class="article">
        <h1 class="page-title"><?= e($a['title']) ?></h1>
        <div class="article__meta">
            <?php if ($slug === 'news'): ?>
                <time datetime="<?= e(substr($a['published_at'], 0, 10)) ?>"><?= ru_date($a['published_at']) ?></time>
            <?php elseif ($slug === 'documents' && $a['doc_meta']): ?>
                <span><?= e($a['doc_meta']) ?></span>
            <?php else: ?>
                <span>Обновлено <?= ru_date($a['updated_at'] ?: $a['published_at']) ?></span>
            <?php endif; ?>
            <span>Просмотров: <?= (int)$a['views'] + 1 ?></span>
            <?php if (is_staff()): ?>
                <a href="<?= url('/admin/article', ['id' => $a['id']]) ?>">Редактировать</a>
            <?php endif; ?>
        </div>
        <?php if ($a['image']): ?>
            <figure class="article__figure">
                <img src="<?= asset('img/articles/' . $a['image']) ?>" alt="Иллюстрация к материалу «<?= e($a['title']) ?>»">
            </figure>
        <?php endif; ?>
        <?php if ($a['summary']): ?>
            <p class="article__lead"><?= e($a['summary']) ?></p>
        <?php endif; ?>
        <div class="article__body">
            <?php
            // HTML материала вводится сотрудниками и очищается при сохранении (sanitize_html);
            // внутренние ссылки вида /раздел/... дополняются базовым путём сайта
            echo str_replace('href="/', 'href="' . BASE . '/', $a['body']);
            ?>
        </div>
        <p class="article__back"><a href="<?= url('/' . $slug) ?>">← Вернуться в раздел «<?= e($section['title']) ?>»</a></p>
    </article>
    <aside class="article-side" aria-label="Другие материалы раздела">
        <div class="side-nav">
            <div class="side-nav__title"><?= e($section['title']) ?></div>
            <ul>
                <?php foreach ($siblings as $s): ?>
                    <li><a href="<?= url("/$slug/" . $s['slug']) ?>"<?= $s['slug'] === $a['slug'] ? ' class="is-active" aria-current="page"' : '' ?>><?= e($s['title']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <a class="side-cta" href="<?= url('/appeals') ?>">
            <?= icon('mail', 30) ?>
            <span><strong>Остались вопросы?</strong><br>Задайте их через интернет-приёмную</span>
        </a>
    </aside>
</div>

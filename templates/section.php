<?php /** @var array $section @var array $items @var array $pg @var bool $isNews */
$slug = $section['slug']; ?>
<div class="container">
    <h1 class="page-title"><?= e($section['title']) ?></h1>
    <p class="page-lead"><?= e($section['description']) ?></p>

    <?php if (!$items): ?>
        <p>В разделе пока нет материалов.</p>
    <?php elseif ($slug === 'documents'): ?>
        <ul class="doc-list">
            <?php foreach ($items as $a): ?>
                <li class="doc-item">
                    <span class="doc-item__icon"><?= icon('doc', 34) ?></span>
                    <div>
                        <a class="doc-item__title" href="<?= url("/$slug/" . $a['slug']) ?>"><?= e($a['title']) ?></a>
                        <div class="doc-item__meta"><?= e($a['doc_meta']) ?></div>
                        <p class="doc-item__summary"><?= e($a['summary']) ?></p>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="list<?= $isNews ? ' list--news' : '' ?>">
            <?php foreach ($items as $a): ?>
                <article class="list-item">
                    <?php if ($a['image']): ?>
                        <a class="list-item__img" href="<?= url("/$slug/" . $a['slug']) ?>" tabindex="-1">
                            <img src="<?= asset('img/articles/' . $a['image']) ?>" alt="" loading="lazy">
                        </a>
                    <?php endif; ?>
                    <div class="list-item__body">
                        <?php if ($isNews): ?>
                            <time class="card__date" datetime="<?= e(substr($a['published_at'], 0, 10)) ?>"><?= ru_date($a['published_at']) ?></time>
                        <?php endif; ?>
                        <h2 class="list-item__title"><a href="<?= url("/$slug/" . $a['slug']) ?>"><?= e($a['title']) ?></a></h2>
                        <p><?= e($a['summary']) ?></p>
                        <a class="more-link" href="<?= url("/$slug/" . $a['slug']) ?>">Читать далее →</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($pg['pages'] > 1): ?>
        <nav class="pager" aria-label="Страницы">
            <?php for ($i = 1; $i <= $pg['pages']; $i++): ?>
                <?php if ($i === $pg['page']): ?>
                    <span class="pager__item is-current" aria-current="page"><?= $i ?></span>
                <?php else: ?>
                    <a class="pager__item" href="<?= url("/$slug", ['page' => $i]) ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </nav>
    <?php endif; ?>
</div>

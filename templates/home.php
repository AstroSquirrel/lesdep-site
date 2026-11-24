<?php /** @var array $slides @var array $side @var array $news @var array $activity @var array $services */ ?>
<section class="hero" aria-label="Баннеры">
    <div class="container">
        <div class="slider" data-slider>
            <?php foreach ($slides as $i => $b): ?>
                <a class="slide<?= $i === 0 ? ' is-active' : '' ?>" href="<?= url($b['link']) ?>"
                   style="background-image:url('<?= asset('img/' . $b['image']) ?>')" aria-hidden="<?= $i === 0 ? 'false' : 'true' ?>">
                    <span class="slide__text">
                        <span class="slide__title"><?= e($b['title']) ?></span>
                        <span class="slide__lead"><?= e($b['text']) ?></span>
                        <span class="btn btn--light">Подробнее</span>
                    </span>
                </a>
            <?php endforeach; ?>
            <?php if (count($slides) > 1): ?>
                <div class="slider__dots">
                    <?php foreach ($slides as $i => $b): ?>
                        <button type="button" class="slider__dot<?= $i === 0 ? ' is-active' : '' ?>" data-slide="<?= $i ?>" aria-label="Баннер <?= $i + 1 ?>"></button>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="slider__arrow slider__arrow--prev" data-prev aria-label="Предыдущий баннер">‹</button>
                <button type="button" class="slider__arrow slider__arrow--next" data-next aria-label="Следующий баннер">›</button>
            <?php endif; ?>
        </div>
    </div>
</section>

<div class="container">
    <section class="quick" aria-label="Быстрые ссылки">
        <a class="quick__item quick__item--fire" href="<?= url('/activity/ohrana-lesov-ot-pozharov') ?>">
            <span class="quick__icon"><?= icon('fire') ?></span>
            <span><strong>Класс пожарной опасности</strong><br>Сейчас в области: <b>I класс</b> — пожароопасный сезон завершён</span>
        </a>
        <a class="quick__item" href="<?= url('/citizens/zagotovka-drevesiny-dlya-sobstvennyh-nuzhd') ?>">
            <span class="quick__icon"><?= icon('wood') ?></span>
            <span><strong>Древесина для собственных нужд</strong><br>Как получить и сколько стоит</span>
        </a>
        <a class="quick__item" href="<?= url('/appeals') ?>">
            <span class="quick__icon"><?= icon('mail') ?></span>
            <span><strong>Интернет-приёмная</strong><br>Направить обращение в департамент</span>
        </a>
        <a class="quick__item" href="<?= url('/citizens/kak-soobschit-o-nezakonnoy-rubke') ?>">
            <span class="quick__icon"><?= icon('axe') ?></span>
            <span><strong>Незаконная рубка</strong><br>Куда и как сообщить</span>
        </a>
    </section>

    <div class="home-grid">
        <section class="home-news" aria-labelledby="h-news">
            <div class="block-head">
                <h2 id="h-news">Новости</h2>
                <a class="more-link" href="<?= url('/news') ?>">Все новости →</a>
            </div>
            <div class="news-grid">
                <?php foreach ($news as $n): ?>
                    <article class="card">
                        <a class="card__img" href="<?= url('/news/' . $n['slug']) ?>" tabindex="-1">
                            <img src="<?= asset('img/articles/' . $n['image']) ?>" alt="" loading="lazy">
                        </a>
                        <div class="card__body">
                            <time class="card__date" datetime="<?= e(substr($n['published_at'], 0, 10)) ?>"><?= ru_date($n['published_at']) ?></time>
                            <h3 class="card__title"><a href="<?= url('/news/' . $n['slug']) ?>"><?= e($n['title']) ?></a></h3>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <aside class="home-side" aria-label="Полезная информация">
            <?php foreach ($side as $b): ?>
                <a class="side-banner" href="<?= url($b['link']) ?>" style="background-image:url('<?= asset('img/' . $b['image']) ?>')">
                    <span class="side-banner__title"><?= e($b['title']) ?></span>
                    <span class="side-banner__text"><?= e($b['text']) ?></span>
                </a>
            <?php endforeach; ?>
            <div class="services">
                <h2 class="services__title">Гражданам</h2>
                <ul>
                    <?php foreach ($services as $s): ?>
                        <li><a href="<?= url('/citizens/' . $s['slug']) ?>"><?= e($s['title']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </aside>
    </div>

    <section class="home-activity" aria-labelledby="h-act">
        <div class="block-head">
            <h2 id="h-act">Направления деятельности</h2>
            <a class="more-link" href="<?= url('/activity') ?>">Подробнее →</a>
        </div>
        <div class="tiles">
            <?php foreach ($activity as $a): ?>
                <a class="tile" href="<?= url('/activity/' . $a['slug']) ?>">
                    <img src="<?= asset('img/articles/' . $a['image']) ?>" alt="" loading="lazy">
                    <span class="tile__title"><?= e($a['title']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="facts" aria-label="Леса Заречной области в цифрах">
        <div class="fact"><span class="fact__num">6,8 млн га</span><span class="fact__label">площадь земель лесного фонда</span></div>
        <div class="fact"><span class="fact__num">57,4 %</span><span class="fact__label">лесистость территории области</span></div>
        <div class="fact"><span class="fact__num">14</span><span class="fact__label">лесничеств в подчинении департамента</span></div>
        <div class="fact"><span class="fact__num">31,2 тыс. га</span><span class="fact__label">лесовосстановление в 2025 году</span></div>
    </section>
</div>

<div class="container">
    <section class="error-page">
        <div class="error-page__art" aria-hidden="true">
            <svg viewBox="0 0 320 170" width="320" height="170">
                <path d="M0 150 Q80 130 160 145 T320 140 V170 H0z" fill="#cfe3d2"/>
                <g fill="#2e6b3a">
                    <path d="M8 152 l16-36h-8l13-27h-7l12-27 12 27h-7l13 27h-8l16 36z"/>
                    <path d="M262 152 l14-32h-7l12-24h-6l11-24 11 24h-6l12 24h-7l14 32z"/>
                </g>
                <text x="160" y="112" text-anchor="middle" font-size="84" font-weight="700" fill="#1f4d2b" font-family="Golos Text, Arial, sans-serif">404</text>
            </svg>
        </div>
        <h1 class="page-title">Кажется, вы заблудились в лесу</h1>
        <p>Страница, которую вы ищете, не существует или была перемещена. Возможно, в адресе допущена опечатка.</p>
        <p>Попробуйте найти нужный материал через поиск или воспользуйтесь картой сайта.</p>
        <form class="search-form search-form--big" action="<?= url('/search') ?>" method="get" role="search">
            <label class="visually-hidden" for="q404">Поиск по сайту</label>
            <input id="q404" type="search" name="q" placeholder="Что вы искали?" minlength="3" required>
            <button type="submit" class="btn">Найти</button>
        </form>
        <p class="error-page__links">
            <a class="btn" href="<?= url('/') ?>">На главную</a>
            <a class="btn btn--ghost" href="<?= url('/sitemap') ?>">Карта сайта</a>
        </p>
    </section>
</div>

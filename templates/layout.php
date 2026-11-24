<?php
/**
 * Общий макет всех страниц: шапка, панель версии для слабовидящих, главное меню,
 * «хлебные крошки», сообщения, содержимое страницы и подвал.
 *
 * @var string $content   HTML-код страницы
 * @var string $title     заголовок страницы
 */
$user     = current_user();
$active   = $active ?? '';
$crumbs   = $crumbs ?? [];
$pageTitle = ($title ?? '') !== '' ? $title . ' — ' . config('site_short') : config('site_name');
$a11y     = a11y_classes();
?><!DOCTYPE html>
<html lang="ru" class="<?= e($a11y) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($description ?? 'Официальный сайт департамента лесного хозяйства Заречной области: охрана лесов от пожаров, лесовосстановление, услуги гражданам, интернет-приёмная.') ?>">
    <link rel="icon" href="<?= asset('img/favicon.svg') ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/a11y.css') ?>">
    <script>window.SITE_BASE = <?= json_encode(BASE) ?>;</script>
</head>
<body>
<a class="skip-link" href="#main">Перейти к содержимому</a>

<!-- Панель настроек версии для слабовидящих (ГОСТ Р 52872-2012) -->
<div class="a11y-panel" id="a11y-panel" aria-label="Настройки версии для слабовидящих">
    <div class="container a11y-panel__inner">
        <div class="a11y-group">
            <span class="a11y-label">Размер шрифта:</span>
            <button type="button" class="a11y-btn" data-a11y-size="1" aria-label="Обычный размер шрифта">А</button>
            <button type="button" class="a11y-btn a11y-btn--m" data-a11y-size="2" aria-label="Увеличенный размер шрифта">А</button>
            <button type="button" class="a11y-btn a11y-btn--l" data-a11y-size="3" aria-label="Крупный размер шрифта">А</button>
        </div>
        <div class="a11y-group">
            <span class="a11y-label">Цвет сайта:</span>
            <button type="button" class="a11y-btn a11y-sw a11y-sw--bw" data-a11y-scheme="bw" aria-label="Чёрным по белому">Ц</button>
            <button type="button" class="a11y-btn a11y-sw a11y-sw--wb" data-a11y-scheme="wb" aria-label="Белым по чёрному">Ц</button>
            <button type="button" class="a11y-btn a11y-sw a11y-sw--blue" data-a11y-scheme="blue" aria-label="Тёмно-синим по голубому">Ц</button>
        </div>
        <div class="a11y-group">
            <span class="a11y-label">Изображения:</span>
            <button type="button" class="a11y-btn a11y-btn--wide" data-a11y-img="1">Вкл.</button>
            <button type="button" class="a11y-btn a11y-btn--wide" data-a11y-img="0">Выкл.</button>
        </div>
        <div class="a11y-group">
            <span class="a11y-label">Интервал:</span>
            <button type="button" class="a11y-btn a11y-btn--wide" data-a11y-space="1">Обычный</button>
            <button type="button" class="a11y-btn a11y-btn--wide" data-a11y-space="2">Средний</button>
            <button type="button" class="a11y-btn a11y-btn--wide" data-a11y-space="3">Большой</button>
        </div>
        <button type="button" class="a11y-btn a11y-btn--wide a11y-off" data-a11y-off>Обычная версия сайта</button>
    </div>
</div>

<header class="site-header">
    <div class="topbar">
        <div class="container topbar__inner">
            <div class="topbar__hotline">
                <span class="topbar__fire" aria-hidden="true"></span>
                Прямая линия лесной охраны: <a href="tel:88001009400"><strong>8-800-100-94-00</strong></a>
                <span class="topbar__note">(круглосуточно)</span>
            </div>
            <div class="topbar__tools">
                <button type="button" class="topbar__a11y" id="a11y-toggle" aria-controls="a11y-panel">
                    <svg width="22" height="14" viewBox="0 0 22 14" aria-hidden="true"><path d="M11 1C6 1 2.3 4.3 1 7c1.3 2.7 5 6 10 6s8.7-3.3 10-6c-1.3-2.7-5-6-10-6zm0 10a4 4 0 110-8 4 4 0 010 8zm0-6a2 2 0 100 4 2 2 0 000-4z" fill="currentColor"/></svg>
                    Версия для слабовидящих
                </button>
                <?php if ($user): ?>
                    <a class="topbar__link" href="<?= url('/cabinet') ?>">Кабинет (<?= e($user['login']) ?>)</a>
                    <?php if (is_staff()): ?><a class="topbar__link" href="<?= url('/admin') ?>">Управление</a><?php endif; ?>
                    <a class="topbar__link" href="<?= url('/logout') ?>">Выйти</a>
                <?php else: ?>
                    <a class="topbar__link" href="<?= url('/login') ?>">Вход</a>
                    <a class="topbar__link" href="<?= url('/register') ?>">Регистрация</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="container brand">
        <a class="brand__logo" href="<?= url('/') ?>">
            <img src="<?= asset('img/logo.svg') ?>" alt="Герб-эмблема департамента: ель на зелёном щите" width="64" height="74">
            <span class="brand__name">
                <span class="brand__title">Департамент лесного хозяйства</span>
                <span class="brand__region">Заречной области</span>
            </span>
        </a>
        <form class="search-form" action="<?= url('/search') ?>" method="get" role="search">
            <label class="visually-hidden" for="search-q">Поиск по сайту</label>
            <input id="search-q" type="search" name="q" placeholder="Поиск по сайту" value="<?= e(str_param('q')) ?>" minlength="3" required>
            <button type="submit" aria-label="Найти">
                <svg width="18" height="18" viewBox="0 0 20 20" aria-hidden="true"><path d="M8.5 2a6.5 6.5 0 015.2 10.4l4.4 4.4-1.4 1.4-4.4-4.4A6.5 6.5 0 118.5 2zm0 2a4.5 4.5 0 100 9 4.5 4.5 0 000-9z" fill="currentColor"/></svg>
            </button>
        </form>
    </div>
    <nav class="main-nav" aria-label="Главное меню">
        <div class="container">
            <button type="button" class="main-nav__burger" aria-expanded="false" aria-controls="main-menu">Меню</button>
            <ul class="main-nav__list" id="main-menu">
                <?php foreach (main_menu() as $href => $name): ?>
                    <li><a href="<?= url($href) ?>"<?= $active === $href ? ' class="is-active" aria-current="page"' : '' ?>><?= e($name) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </nav>
</header>

<main id="main" class="site-main">
    <div class="container">
        <?php if ($crumbs): ?>
            <nav class="crumbs" aria-label="Вы находитесь здесь">
                <a href="<?= url('/') ?>">Главная</a>
                <?php foreach ($crumbs as $c): ?>
                    <span class="crumbs__sep" aria-hidden="true">›</span>
                    <?php if (!empty($c[1])): ?><a href="<?= url($c[1]) ?>"><?= e($c[0]) ?></a><?php else: ?><span><?= e($c[0]) ?></span><?php endif; ?>
                <?php endforeach; ?>
            </nav>
        <?php endif; ?>
        <?php foreach (take_flashes() as $f): ?>
            <div class="alert alert--<?= e($f['type']) ?>" role="status"><?= e($f['text']) ?></div>
        <?php endforeach; ?>
    </div>
    <?= $content ?>
</main>

<footer class="site-footer">
    <div class="container footer__grid">
        <div>
            <div class="footer__name">Департамент лесного хозяйства Заречной области</div>
            <p>650014, г. Заречинск, ул. Лесная, д. 12<br>
               Приёмная: <a href="tel:+73800410200">+7 (3800) 41-02-00</a><br>
               Почта: <a href="mailto:info@les-zarechye.ru">info@les-zarechye.ru</a></p>
            <p>Пн–Чт 9:00–18:00, Пт 9:00–16:45<br>перерыв 13:00–13:48</p>
        </div>
        <div>
            <div class="footer__title">Разделы</div>
            <ul class="footer__menu">
                <?php foreach (main_menu() as $href => $name): ?>
                    <li><a href="<?= url($href) ?>"><?= e($name) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div>
            <div class="footer__title">Полезное</div>
            <ul class="footer__menu">
                <li><a href="<?= url('/sitemap') ?>">Карта сайта</a></li>
                <li><a href="<?= url('/search') ?>">Поиск по сайту</a></li>
                <li><a href="<?= url('/citizens/pravila-pozharnoy-bezopasnosti-v-lesah') ?>">Пожарная безопасность в лесу</a></li>
                <li><a href="<?= url('/appeals') ?>">Интернет-приёмная</a></li>
            </ul>
            <div class="footer__hotline">Сообщить о лесном пожаре:<br><a href="tel:88001009400">8-800-100-94-00</a></div>
        </div>
    </div>
    <div class="footer__bottom">
        <div class="container">
            © 2025 Департамент лесного хозяйства Заречной области.
            Учебный проект: организация, регион и приведённые сведения условные.
        </div>
    </div>
</footer>

<script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>

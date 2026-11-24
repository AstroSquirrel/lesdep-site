<?php /** @var array $tree */ ?>
<div class="container">
    <h1 class="page-title">Карта сайта</h1>
    <p class="page-lead">Все разделы и материалы сайта на одной странице.</p>
    <div class="sitemap">
        <?php foreach (main_menu() as $href => $name):
            $slug = trim($href, '/'); ?>
            <section class="sitemap__block">
                <h2><a href="<?= url($href) ?>"><?= e($name) ?></a></h2>
                <?php if (isset($tree[$slug]) && $tree[$slug]['items']): ?>
                    <ul>
                        <?php foreach ($tree[$slug]['items'] as $it): ?>
                            <li><a href="<?= url("/$slug/" . $it['slug']) ?>"><?= e($it['title']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                <?php elseif ($slug === 'appeals'): ?>
                    <ul>
                        <li><a href="<?= url('/appeals') ?>#form">Направить обращение</a></li>
                        <li><a href="<?= url('/cabinet') ?>">Мои обращения (личный кабинет)</a></li>
                    </ul>
                <?php elseif ($slug === 'contacts'): ?>
                    <ul>
                        <li><a href="<?= url('/contacts') ?>#units">Структурные подразделения</a></li>
                        <li><a href="<?= url('/contacts') ?>#forestries">Лесничества</a></li>
                    </ul>
                <?php elseif ($slug === ''): ?>
                    <ul>
                        <li><a href="<?= url('/search') ?>">Поиск по сайту</a></li>
                        <li><a href="<?= url('/login') ?>">Вход</a></li>
                        <li><a href="<?= url('/register') ?>">Регистрация</a></li>
                    </ul>
                <?php endif; ?>
            </section>
        <?php endforeach; ?>
    </div>
</div>

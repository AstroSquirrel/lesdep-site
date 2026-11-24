<?php /** @var array $units @var array $forestries */ ?>
<div class="container">
    <h1 class="page-title">Контакты</h1>
    <div class="contacts-top">
        <div class="contact-cards">
            <div class="contact-card"><?= icon('pin') ?><div><strong>Адрес</strong><br>650014, Заречная область, г. Заречинск, ул. Лесная, д. 12</div></div>
            <div class="contact-card"><?= icon('phone') ?><div><strong>Приёмная</strong><br><a href="tel:+73800410200">+7 (3800) 41-02-00</a><br>факс +7 (3800) 41-02-01</div></div>
            <div class="contact-card"><?= icon('mail') ?><div><strong>Электронная почта</strong><br><a href="mailto:info@les-zarechye.ru">info@les-zarechye.ru</a></div></div>
            <div class="contact-card"><?= icon('clock') ?><div><strong>Режим работы</strong><br>Пн–Чт 9:00–18:00, Пт 9:00–16:45<br>перерыв 13:00–13:48</div></div>
            <div class="contact-card contact-card--fire"><?= icon('fire') ?><div><strong>Прямая линия лесной охраны</strong><br><a href="tel:88001009400">8-800-100-94-00</a> — круглосуточно</div></div>
        </div>
        <figure class="contacts-map">
            <img src="<?= asset('img/map.svg') ?>" alt="Схема проезда к зданию департамента: ул. Лесная, д. 12, рядом остановка «Площадь Лесников»" width="560" height="380">
            <figcaption>Схема проезда. Остановка общественного транспорта «Площадь Лесников»: автобусы № 3, 17, 24, троллейбус № 5.</figcaption>
        </figure>
    </div>

    <h2 id="units">Структурные подразделения</h2>
    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Подразделение</th><th>Телефон</th><th>Электронная почта</th><th>Кабинет</th></tr></thead>
            <tbody>
            <?php foreach ($units as $u): ?>
                <tr><td><?= e($u[0]) ?></td><td><a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $u[1])) ?>"><?= e($u[1]) ?></a></td><td><a href="mailto:<?= e($u[2]) ?>"><?= e($u[2]) ?></a></td><td><?= e($u[3]) ?></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <h2 id="forestries">Лесничества</h2>
    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Лесничество</th><th>Адрес</th><th>Телефон</th></tr></thead>
            <tbody>
            <?php foreach ($forestries as $f): ?>
                <tr><td><?= e($f[0]) ?></td><td><?= e($f[1]) ?></td><td><?= e($f[2]) ?></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <p class="note">Письменные обращения можно направить почтой по адресу департамента или через <a href="<?= url('/appeals') ?>">интернет-приёмную</a> на сайте.</p>
</div>

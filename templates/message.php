<?php /** @var string $title @var string $text */ ?>
<div class="container">
    <h1 class="page-title"><?= e($title) ?></h1>
    <p><?= e($text) ?></p>
    <p><a class="btn" href="<?= url('/') ?>">На главную</a></p>
</div>

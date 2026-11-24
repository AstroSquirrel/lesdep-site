<?php
/** Главная страница */

$slides = q_all("SELECT * FROM banners WHERE is_active = 1 AND place = 'slider' ORDER BY sort");
$side   = q_all("SELECT * FROM banners WHERE is_active = 1 AND place = 'side' ORDER BY sort");

$news = q_all("SELECT a.*, s.slug AS section_slug FROM articles a JOIN sections s ON s.id = a.section_id
               WHERE s.slug = 'news' AND a.is_published = 1 ORDER BY a.published_at DESC LIMIT 4");

$activity = q_all("SELECT a.title, a.slug, a.summary, a.image FROM articles a JOIN sections s ON s.id = a.section_id
                   WHERE s.slug = 'activity' AND a.is_published = 1 ORDER BY a.published_at LIMIT 6");

$services = q_all("SELECT a.title, a.slug FROM articles a JOIN sections s ON s.id = a.section_id
                   WHERE s.slug = 'citizens' AND a.is_published = 1 ORDER BY a.published_at LIMIT 6");

render('home', [
    'title'    => '',
    'active'   => '/',
    'slides'   => $slides,
    'side'     => $side,
    'news'     => $news,
    'activity' => $activity,
    'services' => $services,
]);

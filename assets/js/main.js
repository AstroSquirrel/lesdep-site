/**
 * Сценарии сайта департамента:
 *  1) версия для слабовидящих – применение и сохранение настроек;
 *  2) слайдер баннеров на главной странице;
 *  3) мобильное меню;
 *  4) подтверждение опасных действий в разделе управления.
 */
(function () {
  'use strict';

  /* ---------- 1. Версия для слабовидящих ---------- */
  var html = document.documentElement;
  var panel = document.getElementById('a11y-panel');
  var toggle = document.getElementById('a11y-toggle');
  var COOKIE = 'a11y';
  var defaults = { on: 'on', size: '1', scheme: 'bw', img: '1', space: '1' };

  function readSettings() {
    var m = document.cookie.match(/(?:^|;\s*)a11y=([^;]*)/);
    if (!m) return null;
    var p = decodeURIComponent(m[1]).split('|');
    if (p[0] !== 'on') return null;
    return { on: 'on', size: p[1] || '1', scheme: p[2] || 'bw', img: p[3] || '1', space: p[4] || '1' };
  }

  function saveSettings(s) {
    var value = s ? [s.on, s.size, s.scheme, s.img, s.space].join('|') : 'off';
    var path = (window.SITE_BASE || '') + '/';
    document.cookie = COOKIE + '=' + encodeURIComponent(value) + ';path=' + path + ';max-age=' + (60 * 60 * 24 * 365) + ';samesite=lax';
  }

  function apply(s) {
    // удаляем все классы версии для слабовидящих и ставим заново
    html.className = html.className.replace(/\ba11y[\w-]*\b/g, '').trim();
    if (s) {
      html.classList.add('a11y', 'a11y-size-' + s.size, 'a11y-' + s.scheme, 'a11y-space-' + s.space);
      if (s.img === '0') html.classList.add('a11y-noimg');
    }
    markButtons(s);
  }

  function markButtons(s) {
    if (!panel) return;
    var map = { size: 'data-a11y-size', scheme: 'data-a11y-scheme', img: 'data-a11y-img', space: 'data-a11y-space' };
    Object.keys(map).forEach(function (key) {
      panel.querySelectorAll('[' + map[key] + ']').forEach(function (b) {
        var on = !!s && b.getAttribute(map[key]) === s[key];
        b.classList.toggle('is-on', on);
        b.setAttribute('aria-pressed', on ? 'true' : 'false');
      });
    });
  }

  if (panel && toggle) {
    markButtons(readSettings());
    toggle.addEventListener('click', function () {
      var s = readSettings();
      if (!s) {
        s = Object.assign({}, defaults);
        saveSettings(s);
        apply(s);
      }
      panel.classList.add('is-open');
      var first = panel.querySelector('button');
      if (first) first.focus();
    });
    panel.addEventListener('click', function (e) {
      var b = e.target.closest('button');
      if (!b) return;
      if (b.hasAttribute('data-a11y-off')) {
        saveSettings(null);
        apply(null);
        panel.classList.remove('is-open');
        toggle.focus();
        return;
      }
      var s = readSettings() || Object.assign({}, defaults);
      if (b.dataset.a11ySize) s.size = b.dataset.a11ySize;
      if (b.dataset.a11yScheme) s.scheme = b.dataset.a11yScheme;
      if (b.dataset.a11yImg) s.img = b.dataset.a11yImg;
      if (b.dataset.a11ySpace) s.space = b.dataset.a11ySpace;
      saveSettings(s);
      apply(s);
    });
  }

  /* ---------- 2. Слайдер баннеров ---------- */
  var slider = document.querySelector('[data-slider]');
  if (slider) {
    var slides = slider.querySelectorAll('.slide');
    var dots = slider.querySelectorAll('.slider__dot');
    var current = 0, timer = null;

    var show = function (i) {
      current = (i + slides.length) % slides.length;
      slides.forEach(function (s, k) {
        s.classList.toggle('is-active', k === current);
        s.setAttribute('aria-hidden', k === current ? 'false' : 'true');
        s.tabIndex = k === current ? 0 : -1;
      });
      dots.forEach(function (d, k) { d.classList.toggle('is-active', k === current); });
    };
    var start = function () {
      stop();
      if (!html.classList.contains('a11y')) timer = setInterval(function () { show(current + 1); }, 6000);
    };
    var stop = function () { if (timer) clearInterval(timer); timer = null; };

    dots.forEach(function (d) { d.addEventListener('click', function () { show(+d.dataset.slide); start(); }); });
    var prev = slider.querySelector('[data-prev]'), next = slider.querySelector('[data-next]');
    if (prev) prev.addEventListener('click', function () { show(current - 1); start(); });
    if (next) next.addEventListener('click', function () { show(current + 1); start(); });
    slider.addEventListener('mouseenter', stop);
    slider.addEventListener('mouseleave', start);
    slider.addEventListener('focusin', stop);
    show(0);
    start();
  }

  /* ---------- 3. Мобильное меню ---------- */
  var burger = document.querySelector('.main-nav__burger');
  var menu = document.getElementById('main-menu');
  if (burger && menu) {
    burger.addEventListener('click', function () {
      var open = menu.classList.toggle('is-open');
      burger.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

  /* ---------- 4. Подтверждение действий ---------- */
  document.addEventListener('click', function (e) {
    var el = e.target.closest('[data-confirm]');
    if (el && !window.confirm(el.getAttribute('data-confirm'))) {
      e.preventDefault();
    }
  });
})();

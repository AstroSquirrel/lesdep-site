<?php
/**
 * Вспомогательные функции: экранирование, ссылки, шаблоны, даты, CSRF, сообщения.
 */
declare(strict_types=1);

/** Экранирование вывода (защита от XSS) */
function e(?string $s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Ссылка внутри сайта с учётом базового пути */
function url(string $path = '/', array $query = []): string
{
    $u = BASE . '/' . ltrim($path, '/');
    if ($query) {
        $u .= '?' . http_build_query($query);
    }
    return $u;
}

/** Ссылка на статический файл (с версией для сброса кэша браузера) */
function asset(string $path): string
{
    $file = ROOT . '/assets/' . ltrim($path, '/');
    $v = is_file($file) ? filemtime($file) : 0;
    return BASE . '/assets/' . ltrim($path, '/') . '?v=' . $v;
}

function redirect(string $path, array $query = []): void
{
    header('Location: ' . url($path, $query));
    exit;
}

function config(string $key)
{
    global $CONFIG;
    return $CONFIG[$key] ?? null;
}

/** Главное меню сайта: адрес => название */
function main_menu(): array
{
    return [
        '/'          => 'Главная',
        '/about'     => 'О департаменте',
        '/news'      => 'Новости',
        '/activity'  => 'Деятельность',
        '/citizens'  => 'Гражданам',
        '/documents' => 'Документы',
        '/appeals'   => 'Обращения',
        '/contacts'  => 'Контакты',
    ];
}

/**
 * Вывод страницы: шаблон страницы оборачивается в общий макет (шапка, меню, подвал).
 * $data – переменные для шаблона; специальные ключи: title, crumbs, active, description.
 */
function render(string $view, array $data = []): void
{
    extract($data, EXTR_SKIP);
    ob_start();
    require ROOT . '/templates/' . $view . '.php';
    $content = ob_get_clean();
    require ROOT . '/templates/layout.php';
}

/** Страница «не найдено» с правильным HTTP-кодом */
function not_found(): void
{
    http_response_code(404);
    render('404', ['title' => 'Страница не найдена', 'crumbs' => []]);
    exit;
}

/** Дата по-русски: 12 ноября 2025 */
function ru_date(?string $dt, bool $withTime = false): string
{
    if (!$dt) {
        return '';
    }
    $months = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля',
               'августа', 'сентября', 'октября', 'ноября', 'декабря'];
    $t = strtotime($dt);
    $s = (int)date('j', $t) . ' ' . $months[(int)date('n', $t) - 1] . ' ' . date('Y', $t);
    if ($withTime) {
        $s .= ', ' . date('H:i', $t);
    }
    return $s;
}

/* ---------- CSRF-защита форм ---------- */

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_check(): void
{
    $sent = $_POST['csrf'] ?? '';
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(400);
        exit('Срок действия формы истёк. Обновите страницу и попробуйте ещё раз.');
    }
}

/** Строковый параметр запроса (массивы и прочие типы отбрасываются) */
function str_param(string $key, string $default = ''): string
{
    $v = $_POST[$key] ?? $_GET[$key] ?? $default;
    return is_string($v) ? $v : $default;
}

/** Адрес возврата допускается только внутри сайта: /путь, без схемы, // и обратной косой черты */
function safe_back(string $back): string
{
    if ($back === '' || !preg_match('#^/[A-Za-z0-9_\-./?=&%]*$#', $back) || str_starts_with($back, '//')) {
        return '';
    }
    return $back;
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

/* ---------- Одноразовые сообщения пользователю ---------- */

function flash(string $type, string $text): void
{
    $_SESSION['flash'][] = ['type' => $type, 'text' => $text];
}

function take_flashes(): array
{
    $f = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $f;
}

/* ---------- Прочее ---------- */

/** Короткий фрагмент текста без HTML-разметки */
function excerpt(string $html, int $len = 200): string
{
    $text = trim(preg_replace('/\s+/u', ' ', strip_tags($html)));
    if (mb_strlen($text) <= $len) {
        return $text;
    }
    $cut = mb_substr($text, 0, $len);
    $cut = mb_substr($cut, 0, (int)mb_strrpos($cut, ' '));
    return rtrim($cut, ' ,.;:—-') . '…';
}

/** Транслитерация заголовка для адреса статьи */
function slugify(string $s): string
{
    $map = ['а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'zh','з'=>'z','и'=>'i',
            'й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t',
            'у'=>'u','ф'=>'f','х'=>'h','ц'=>'ts','ч'=>'ch','ш'=>'sh','щ'=>'sch','ъ'=>'','ы'=>'y','ь'=>'',
            'э'=>'e','ю'=>'yu','я'=>'ya'];
    $s = strtr(mb_strtolower($s), $map);
    $s = preg_replace('/[^a-z0-9]+/', '-', $s);
    $s = trim($s, '-');
    return mb_substr($s !== '' ? $s : 'material', 0, 80);
}

/** Настройки версии для слабовидящих хранятся в cookie, чтобы страница сразу выводилась в нужном виде */
function a11y_classes(): string
{
    $c = $_COOKIE['a11y'] ?? '';
    if (!is_string($c) || $c === '' || $c === 'off') {
        return '';
    }
    // формат: on|размер|схема|изображения|интервал, например on|2|bw|1|1
    $p = explode('|', $c);
    if (($p[0] ?? '') !== 'on') {
        return '';
    }
    $size   = in_array($p[1] ?? '1', ['1', '2', '3'], true) ? $p[1] : '1';
    $scheme = in_array($p[2] ?? 'bw', ['bw', 'wb', 'blue'], true) ? $p[2] : 'bw';
    $img    = ($p[3] ?? '1') === '0' ? ' a11y-noimg' : '';
    $space  = in_array($p[4] ?? '1', ['1', '2', '3'], true) ? $p[4] : '1';
    return "a11y a11y-size-$size a11y-$scheme a11y-space-$space$img";
}

/** Простая постраничная навигация */
function paginate(int $total, int $perPage, int $page): array
{
    $pages = max(1, (int)ceil($total / $perPage));
    $page = min(max(1, $page), $pages);
    return ['page' => $page, 'pages' => $pages, 'offset' => ($page - 1) * $perPage, 'limit' => $perPage];
}

/** Названия статусов обращений */
function appeal_status(string $s): string
{
    return [
        'new'      => 'Зарегистрировано',
        'in_work'  => 'На рассмотрении',
        'answered' => 'Дан ответ',
        'closed'   => 'Закрыто',
    ][$s] ?? $s;
}

function role_name(string $r): string
{
    return ['admin' => 'Администратор', 'employee' => 'Сотрудник департамента', 'citizen' => 'Гражданин'][$r] ?? $r;
}

/** Тематики обращений */
function appeal_categories(): array
{
    return [
        'Лесные пожары и пожарная безопасность',
        'Незаконная рубка и лесонарушения',
        'Заготовка древесины для собственных нужд',
        'Аренда лесных участков',
        'Лесовосстановление и посадка леса',
        'Работа сайта',
        'Другое',
    ];
}

/** Встроенные SVG-иконки (простые контурные пиктограммы) */
function icon(string $name, int $size = 28): string
{
    $p = [
        'fire'  => '<path d="M12 2c1 3.5-1.5 5-1.5 7.5 0 1.4 1 2.5 2.3 2.5 1.6 0 2.4-1.3 2.2-3 2.3 1.8 3.5 4.3 3.5 6.5A6.5 6.5 0 015.5 15.5C5.5 10.5 10 8 12 2z"/>',
        'wood'  => '<ellipse cx="6.5" cy="12" rx="3.5" ry="5"/><path d="M6.5 7h11c2 0 3.5 2.2 3.5 5s-1.5 5-3.5 5h-11"/><ellipse cx="6.5" cy="12" rx="1.3" ry="2"/>',
        'mail'  => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3.5 6l8.5 7 8.5-7"/>',
        'axe'   => '<path d="M14 4l6 6-3 3-6-6z"/><path d="M12.5 8.5L4 17l3 3 8.5-8.5"/>',
        'doc'   => '<path d="M6 2.5h8l4 4V21H6z"/><path d="M14 2.5V7h4M9 12h6M9 15.5h6M9 9h3"/>',
        'phone' => '<path d="M6 3h3l2 5-2.5 1.5a11 11 0 005 5L15 12l5 2v3a2 2 0 01-2 2A15 15 0 014 5a2 2 0 012-2z"/>',
        'user'  => '<circle cx="12" cy="8" r="4"/><path d="M4 21c1.2-4 4.3-6 8-6s6.8 2 8 6"/>',
        'tree'  => '<path d="M12 2l5 7h-3l4 6h-4l3 4H7l3-4H6l4-6H7z"/><path d="M12 19v3"/>',
        'pin'   => '<path d="M12 22s7-7.2 7-12.5A7 7 0 005 9.5C5 14.8 12 22 12 22z"/><circle cx="12" cy="9.5" r="2.5"/>',
        'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/>',
        'seed'  => '<path d="M12 21v-9"/><path d="M12 12c0-4 3-6.5 7-6.5 0 4-3 6.5-7 6.5zM12 14c0-3-2.3-5-5.5-5 0 3 2.3 5 5.5 5z"/>',
        'shield'=> '<path d="M12 2.5l8 3v6c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10v-6z"/><path d="M8.5 12l2.5 2.5 4.5-5"/>',
    ];
    return '<svg class="icon" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
        . ($p[$name] ?? '') . '</svg>';
}

/**
 * Очистка HTML-текста материала: остаются только разрешённые теги и атрибуты,
 * удаляются скрипты, обработчики событий и ссылки вида javascript:.
 */
function sanitize_html(string $html): string
{
    $allowed = ['p' => [], 'br' => [], 'h2' => [], 'h3' => [], 'h4' => [], 'ul' => [], 'ol' => [], 'li' => [],
                'strong' => [], 'b' => [], 'em' => [], 'i' => [], 'a' => ['href', 'title'], 'blockquote' => [],
                'table' => [], 'thead' => [], 'tbody' => [], 'tr' => [], 'th' => ['colspan', 'rowspan'],
                'td' => ['colspan', 'rowspan'], 'figure' => [], 'figcaption' => [], 'img' => ['src', 'alt'],
                'div' => ['class'], 'span' => ['class']];
    $doc = new DOMDocument();
    libxml_use_internal_errors(true);
    $doc->loadHTML('<?xml encoding="utf-8"?><div id="root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();
    $root = $doc->getElementById('root');
    if (!$root) {
        return '';
    }
    $walk = function (DOMNode $node) use (&$walk, $allowed) {
        for ($i = $node->childNodes->length - 1; $i >= 0; $i--) {
            $child = $node->childNodes->item($i);
            if ($child instanceof DOMElement) {
                $tag = strtolower($child->tagName);
                if (in_array($tag, ['script', 'style', 'iframe', 'object', 'embed', 'form'], true)) {
                    $node->removeChild($child);
                    continue;
                }
                if (!isset($allowed[$tag])) {
                    // неразрешённый тег заменяется его содержимым
                    $walk($child);
                    while ($child->firstChild) {
                        $node->insertBefore($child->firstChild, $child);
                    }
                    $node->removeChild($child);
                    continue;
                }
                foreach (iterator_to_array($child->attributes) as $attr) {
                    $name = strtolower($attr->name);
                    $val = trim($attr->value);
                    if (!in_array($name, $allowed[$tag], true)
                        || (in_array($name, ['href', 'src'], true) && preg_match('/^\s*(javascript|data|vbscript):/i', $val))) {
                        $child->removeAttribute($attr->name);
                    }
                }
                $walk($child);
            }
        }
    };
    $walk($root);
    $out = '';
    foreach ($root->childNodes as $c) {
        $out .= $doc->saveHTML($c);
    }
    return trim($out);
}

<?php
/**
 * Поиск по сайту.
 * Запрос разбивается на слова; материал попадает в выдачу, если содержит все слова
 * (в заголовке, аннотации или тексте). Совпадения в заголовке поднимают материал выше.
 */

$q = trim(str_param('q'));
$q = mb_substr(preg_replace('/\s+/u', ' ', $q), 0, 100);
$results = [];
$error = '';

if ($q !== '') {
    // слова короче 3 букв не учитываются; у длинных слов отбрасывается окончание,
    // чтобы «пожары», «пожаров», «пожарам» находились по одной основе
    $words = [];
    foreach (explode(' ', mb_strtolower($q)) as $w) {
        $w = preg_replace('/[^\p{L}\p{N}-]/u', '', $w);
        if (mb_strlen($w) >= 3) {
            $words[] = mb_strlen($w) > 5 ? mb_substr($w, 0, mb_strlen($w) - 2) : $w;
        }
    }
    if (!$words) {
        $error = 'Введите слово длиной не менее трёх букв.';
    } else {
        $where = [];
        $params = [];
        $score = [];
        $scoreParams = [];
        foreach ($words as $w) {
            $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $w) . '%';
            $where[] = '(a.title LIKE ? OR a.summary LIKE ? OR a.body LIKE ?)';
            array_push($params, $like, $like, $like);
            $score[] = '(a.title LIKE ?) * 3 + (a.summary LIKE ?)';
            $scoreParams[] = $like;
            $scoreParams[] = $like;
        }
        $sql = 'SELECT a.title, a.slug, a.summary, a.body, a.published_at, s.slug AS section_slug, s.title AS section_title, '
             . implode(' + ', $score) . ' AS score
                FROM articles a JOIN sections s ON s.id = a.section_id
                WHERE a.is_published = 1 AND ' . implode(' AND ', $where) . '
                ORDER BY score DESC, a.published_at DESC LIMIT 50';
        $results = q_all($sql, array_merge($scoreParams, $params));
        // LIKE ищет и внутри HTML-разметки, поэтому каждое совпадение перепроверяется по тексту без тегов
        $results = array_values(array_filter($results, function (array $r) use ($words): bool {
            $text = mb_strtolower($r['title'] . ' ' . $r['summary'] . ' ' . html_entity_decode(strip_tags($r['body'])));
            foreach ($words as $w) {
                if (!str_contains($text, $w)) {
                    return false;
                }
            }
            return true;
        }));
    }
}

/** Подсветка найденных слов в тексте */
$highlight = function (string $text) use ($q): string {
    $out = e($text);
    foreach (explode(' ', $q) as $w) {
        $w = preg_replace('/[^\p{L}\p{N}-]/u', '', $w);
        if (mb_strlen($w) >= 3) {
            $stem = mb_strlen($w) > 5 ? mb_substr($w, 0, mb_strlen($w) - 2) : $w;
            $out = preg_replace('/(' . preg_quote(e($stem), '/') . ')/iu', '<mark>$1</mark>', $out);
        }
    }
    return $out;
};

render('search', [
    'title'     => 'Поиск по сайту',
    'crumbs'    => [['Поиск по сайту', '']],
    'q'         => $q,
    'results'   => $results,
    'error'     => $error,
    'highlight' => $highlight,
]);

<?php
/**
 * Мастер установки сайта на хостинг.
 * Создаёт файл config.php, таблицы базы данных и начальное наполнение.
 * После успешной установки файл удаляет сам себя.
 */
declare(strict_types=1);
mb_internal_encoding('UTF-8');

$root = __DIR__;
$done = is_file($root . '/config.php');
$error = '';
$ok = false;
$f = ['host' => 'localhost', 'port' => '3306', 'name' => '', 'user' => '', 'pass' => ''];

function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

/** Разбор SQL-файла на отдельные запросы */
function sql_statements(string $sql): array
{
    $parts = preg_split('/;\s*\n(?=\s*(?:INSERT|CREATE|DROP|SET|ALTER|--|$))/i', $sql . "\n");
    $out = [];
    foreach ($parts as $p) {
        $lines = array_filter(explode("\n", $p), fn($l) => !str_starts_with(ltrim($l), '--'));
        $q = trim(implode("\n", $lines));
        if ($q !== '') {
            $out[] = rtrim($q, ';');
        }
    }
    return $out;
}

if (!$done && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    foreach ($f as $k => $v) {
        $f[$k] = trim((string)($_POST[$k] ?? $v));
    }
    try {
        $pdo = new PDO(sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $f['host'], (int)$f['port'], $f['name']),
            $f['user'], $f['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        foreach (['schema.sql', 'seed.sql'] as $file) {
            foreach (sql_statements((string)file_get_contents($root . '/sql/' . $file)) as $q) {
                $pdo->exec($q);
            }
        }
        $cfg = "<?php\n// Файл создан мастером установки\nreturn " . var_export([
            'db' => ['host' => $f['host'], 'port' => (int)$f['port'], 'name' => $f['name'], 'user' => $f['user'],
                     'pass' => $f['pass'], 'charset' => 'utf8mb4'],
            'site_name'  => 'Департамент лесного хозяйства Заречной области',
            'site_short' => 'Департамент лесного хозяйства',
            'per_page'   => 6,
            'debug'      => false,
        ], true) . ";\n";
        if (file_put_contents($root . '/config.php', $cfg) === false) {
            throw new RuntimeException('Не удалось записать файл config.php: проверьте права на запись.');
        }
        $ok = true;
        @unlink(__FILE__);
    } catch (Throwable $e) {
        $error = $e instanceof PDOException
            ? 'Ошибка базы данных: ' . $e->getMessage()
            : $e->getMessage();
    }
}
?><!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Установка сайта</title>
<style>
body{font-family:Arial,sans-serif;background:#f3f7f3;color:#1d2620;margin:0;padding:30px}
.box{max-width:520px;margin:0 auto;background:#fff;border-radius:10px;padding:24px 30px;box-shadow:0 2px 10px rgba(0,0,0,.08)}
h1{color:#163a22;font-size:24px}label{display:block;margin:12px 0 4px;font-weight:bold}
input{width:100%;padding:9px;border:1.5px solid #bccabf;border-radius:6px;font-size:15px;box-sizing:border-box}
button{margin-top:18px;background:#1f5a33;color:#fff;border:0;border-radius:7px;padding:11px 22px;font-size:16px;cursor:pointer}
.err{background:#fdecea;border:1px solid #f0b4ae;color:#8c1d18;padding:10px;border-radius:6px}
.ok{background:#e5f4e8;border:1px solid #9fd0aa;color:#174d26;padding:10px;border-radius:6px}
small{color:#5c6a60}
</style>
</head>
<body><div class="box">
<h1>Установка сайта департамента</h1>
<?php if ($ok): ?>
    <div class="ok">Установка завершена: таблицы созданы, наполнение загружено, файл настроек сохранён. Мастер установки удалён.</div>
    <p><a href="./">Открыть сайт</a></p>
<?php elseif ($done): ?>
    <div class="ok">Сайт уже установлен.</div>
    <p><a href="./">Открыть сайт</a></p>
<?php else: ?>
    <p>Укажите параметры базы данных MySQL из панели управления хостингом.</p>
    <?php if ($error): ?><div class="err"><?= h($error) ?></div><?php endif; ?>
    <form method="post">
        <label>Сервер MySQL (MySQL Hostname)</label><input name="host" value="<?= h($f['host']) ?>" required>
        <label>Порт</label><input name="port" value="<?= h($f['port']) ?>" required>
        <label>Имя базы данных (MySQL Database Name)</label><input name="name" value="<?= h($f['name']) ?>" required>
        <label>Пользователь (MySQL Username)</label><input name="user" value="<?= h($f['user']) ?>" required>
        <label>Пароль (MySQL Password)</label><input name="pass" type="password" autocomplete="off">
        <small>Внимание: существующие таблицы сайта в этой базе будут пересозданы.</small><br>
        <button type="submit">Установить</button>
    </form>
<?php endif; ?>
</div></body>
</html>

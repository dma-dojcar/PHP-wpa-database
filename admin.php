<?php
/**********************************************
 *  Jednoduchý admin panel pro blokování uživatelů
 *  VŠE V JEDNOM SOUBORU
 **********************************************/

// --- Připojení k databázi ---
$host = '127.0.0.1';
$db   = 'socialapp';
$user = 'phpuser';
$pass = 'heslo123';
$dsn = "mysql:host=$host;dbname=$db";

$pdo = new PDO($dsn, $user, $pass);

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


// --- Zpracování blokace / odblokace ---
if (isset($_GET['block'])) {
    $id = intval($_GET['block']);
    $pdo->prepare("UPDATE users SET block_blocked = 1 WHERE id = ?")->execute([$id]);
}

if (isset($_GET['unblock'])) {
    $id = intval($_GET['unblock']);
    $pdo->prepare("UPDATE users SET block_blocked = 0 WHERE id = ?")->execute([$id]);
}


// --- Načtení uživatelů ---
$users = $pdo->query("SELECT id, name, block_blocked, created_at FROM users ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Admin panel - blokace uživatelů</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; padding: 20px; }
        table { border-collapse: collapse; width: 600px; background: white; }
        th, td { padding: 10px; border: 1px solid #ddd; }
        th { background: #e3e3e3; }
        .blocked { color: red; font-weight: bold; }
        a.button {
            padding: 5px 10px;
            border: 1px solid black;
            text-decoration: none;
            border-radius: 4px;
        }
        a.block { background: #ffb3b3; }
        a.unblock { background: #b3ffb3; }
    </style>
</head>
<body>

<h2>Admin – blokování uživatelů</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Jméno</th>
        <th>Vytvořen</th>
        <th>Stav</th>
        <th>Akce</th>
    </tr>

    <?php foreach ($users as $u): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['name']) ?></td>
            <td><?= $u['created_at'] ?></td>
            <td>
                <?php if ($u['block_blocked']): ?>
                    <span class="blocked">BLOKOVANÝ</span>
                <?php else: ?>
                    Aktivní
                <?php endif; ?>
            </td>
            <td>
                <?php if (!$u['block_blocked']): ?>
                    <a class="button block" href="?block=<?= $u['id'] ?>">Blokovat</a>
                <?php else: ?>
                    <a class="button unblock" href="?unblock=<?= $u['id'] ?>">Odblokovat</a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>

</table>

</body>
</html>

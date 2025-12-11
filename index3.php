
<?php
$host = '127.0.0.1';
$db   = 'socialapp';
$user = 'phpuser';
$pass = 'heslo123';
$dsn = "mysql:host=$host;dbname=$db";

$pdo = new PDO($dsn, $user, $pass);

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$users   = $pdo->query("SELECT * FROM users ORDER BY id ASC")->fetchAll();
$friends = $pdo->query("SELECT * FROM friends")->fetchAll();
$groups  = $pdo->query("SELECT * FROM groups")->fetchAll();
$texts   = $pdo->query("SELECT * FROM texts")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Výpis dat bez JOIN</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin-bottom: 30px; }
        th, td { border: 1px solid #888; padding: 8px; }
        th { background: #eee; }
    </style>
</head>
<body>

<h2>Uživatelé</h2>
<table>
<tr><th>ID</th><th>Jméno</th><th>Heslo</th><th>Datum vytvoření</th></tr>
<?php foreach ($users as $u): ?>
<tr>
    <td><?= $u['id'] ?></td>
    <td><?= htmlspecialchars($u['name']) ?></td>
    <td><?= htmlspecialchars($u['passwd']) ?></td>
    <td><?= $u['created_at'] ?></td>
</tr>
<?php endforeach; ?>
</table>

<h2>Přátelé</h2>
<table>
<tr><th>User ID</th><th>Friend ID</th></tr>
<?php foreach ($friends as $f): ?>
<tr>
    <td><?= $f['user_id'] ?></td>
    <td><?= $f['friend_id'] ?></td>
</tr>
<?php endforeach; ?>
</table>

<h2>Skupiny</h2>
<table>
<tr><th>ID</th><th>Název</th></tr>
<?php foreach ($groups as $g): ?>
<tr>
    <td><?= $g['id'] ?></td>
    <td><?= htmlspecialchars($g['name']) ?></td>
</tr>
<?php endforeach; ?>
</table>

<h2>Zprávy (Texts)</h2>
<table>
<tr><th>ID</th><th>Group ID</th><th>User ID</th><th>Obsah</th><th>Čas</th></tr>
<?php foreach ($texts as $t): ?>
<tr>
    <td><?= $t['id'] ?></td>
    <td><?= $t['group_id'] ?></td>
    <td><?= $t['user_id'] ?></td>
    <td><?= htmlspecialchars($t['content']) ?></td>
    <td><?= $t['sent_at'] ?></td>
</tr>
<?php endforeach; ?>
</table>

</body>
</html>

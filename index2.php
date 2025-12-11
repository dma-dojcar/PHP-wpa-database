<?php
$host = '127.0.0.1';
$db   = 'socialapp';
$user = 'phpuser';
$pass = 'heslo123';
$dsn = "mysql:host=$host;dbname=$db";

$pdo = new PDO($dsn, $user, $pass);

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_GET['block-user'])) {
    $id = intval($_GET['block-user']);
    $pdo->prepare("UPDATE users SET blocked = NOT blocked WHERE id = ?")
            ->execute([$id]);
    exit;
}

/*
    * add users
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $pass = $_POST['passwd'];
    $date = date("Y-m-d");

    $stmt = $pdo->prepare("INSERT INTO users (name, passwd, created_at) VALUES (?, ?, ?)");
    $stmt->execute([$name, $pass, $date]);
    header("Refresh:0");

    exit;
}

/*
 * nacteni uzivatele
*/
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();

/*
   JOIN DOTAZY
*/
$joined_friends = $pdo->query("
    SELECT u.name AS user_name, f2.name AS friend_name
    FROM friends f
    JOIN users u ON u.id = f.user_id
    JOIN users f2 ON f2.id = f.friend_id
")->fetchAll();
/*
 * nacteni skupiny
 */

$joined_groups = $pdo->query("
    SELECT texts.content, texts.sent_at, users.name AS user_name, groups.name AS group_name
    FROM texts
    JOIN users ON users.id = texts.user_id
    JOIN groups ON groups.id = texts.group_id
")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin panel – SocialApp</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f9f9f9; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 40px; }
        th, td { border: 1px solid #aaa; padding: 8px; background: white; }
        th { background: #eaeaea; }
        h2 { margin-top: 40px; }
        form { margin-bottom: 20px; }
        input { padding: 5px; }
        button { padding: 6px 15px; cursor: pointer; }
        .delete { color: red; text-decoration: none; }
    </style>
</head>
<body>

<h1>Administrace – SocialApp</h1>

<!-- ============================ -->
<!-- FORMULÁŘ PRO PŘIDÁNÍ UŽIVATELE -->
<!-- ============================ -->
<h2>Přidat uživatele</h2>
<form method="post">
    Jméno: <input type="text" name="name" required>
    Heslo: <input type="text" name="passwd" required>
    <button type="submit">Přidat</button>
</form>

<!-- ============================ -->
<!-- SEZNAM UŽIVATELŮ -->
<!-- ============================ -->
<h2>Seznam uživatelů</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Jméno</th>
        <th>Heslo</th>
        <th>Datum založení</th>
        <th>Blocked</th>
        <th>Akce</th>
    </tr>

    <?php foreach ($users as $u): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['name']) ?></td>
            <td><?= htmlspecialchars($u['passwd_hashed']) ?></td>
            <td><?= $u['created_at'] ?></td>
            <td><?= htmlspecialchars($u["blocked"])?></td>
            <td>
                <a class="delete" href="?block-user=<?= $u['id'] ?>">block</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<!-- ============================ -->
<!-- JOIN – UŽIVATELÉ A PŘÁTELÉ -->
<!-- ============================ -->
<h2>Uživatelé a jejich přátelé (JOIN)</h2>
<table>
    <tr>
        <th>Uživatel</th>
        <th>Přítel</th>
    </tr>
    <?php foreach ($joined_friends as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['user_name']) ?></td>
            <td><?= htmlspecialchars($row['friend_name']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<!-- ============================ -->
<!-- JOIN – ZPRÁVY VE SKUPINÁCH -->
<!-- ============================ -->
<h2>Zprávy ve skupinách (JOIN)</h2>
<table>
    <tr>
        <th>Autor</th>
        <th>Skupina</th>
        <th>Zpráva</th>
        <th>Čas</th>
        <th>Akce</th>
    </tr>

    <?php foreach ($joined_groups as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['user_name']) ?></td>
            <td><?= htmlspecialchars($row['group_name']) ?></td>
            <td><?= htmlspecialchars($row['content']) ?></td>
            <td><?= $row['sent_at'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>


</body>
</html>

<?php
session_start();

/* ---------------------------------------------
   DATABASE CONNECTION
---------------------------------------------- */

$host = '127.0.0.1';
$db   = 'socialapp';
$user = 'phpuser';
$pass = 'heslo123';
$dsn = "mysql:host=$host;dbname=$db";

$pdo = new PDO($dsn, $user, $pass);
/* ---------------------------------------------
   HELPER FUNCTIONS
---------------------------------------------- */
function isLogged() {
    return isset($_SESSION['user_id']);
}

function redirect($url) {
    header("Location: $url");
    exit;
}

/* ---------------------------------------------
   LOGIN
---------------------------------------------- */
if (isset($_POST['login'])) {
    $name = $_POST['name'];
    $pass = $_POST['pass'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE name = ?");
    $stmt->execute([$name]);
    $u = $stmt->fetch();

    if ($u && $u['passwd_hashed'] === $pass) {
        $_SESSION['user_id'] = $u['id'];
        $_SESSION['user_name'] = $u['name'];
        redirect("index.php");
    } else {
        $error = "Špatné jméno nebo heslo!";
    }
}

/* ---------------------------------------------
   REGISTER
---------------------------------------------- */
if (isset($_POST['register'])) {

    $stmt = $pdo->prepare("INSERT INTO users (name, passwd_hashed, created_at) VALUES (?, ?, NOW())");

    try {
        $stmt->execute([$_POST['name'], $_POST['pass']]);
        $msg = "Registrace proběhla úspěšně! Můžeš se přihlásit.";
    } catch (Exception $e) {
        $error = "Uživatel již existuje!";
    }
}

/* ---------------------------------------------
   LOGOUT
---------------------------------------------- */
if (isset($_GET['logout'])) {
    session_destroy();
    redirect("index.php");
}

/* ---------------------------------------------
   SEND MESSAGE
---------------------------------------------- */
if (isset($_POST['send_message']) && isLogged()) {
    $stmt = $pdo->prepare("INSERT INTO texts (group_id, user_id, content, sent_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$_POST['group_id'], $_SESSION['user_id'], $_POST['content']]);
    redirect("index.php?group=" . $_POST['group_id']);
}

/* ============================================================
   HTML START
============================================================ */
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Jednoduchý Chat</title>
    <style>
        body { font-family: Arial; background:#f0f0f0; padding:20px; }
        .box { background:white; padding:20px; margin-bottom:20px; border-radius:5px; }
        a { text-decoration:none; }
    </style>
</head>
<body>

<?php if (!isLogged()): ?>

    <h2>Přihlášení</h2>
    <?php if (isset($error)) echo "<p style='color:red'>$error</p>"; ?>
    <form method="post">
        <input type="text" name="name" placeholder="Jméno"><br><br>
        <input type="password" name="pass" placeholder="Heslo"><br><br>
        <button name="login">Přihlásit</button>
    </form>

    <h3>Registrace</h3>
    <?php if (isset($msg)) echo "<p style='color:green'>$msg</p>"; ?>
    <form method="post">
        <input type="text" name="name" placeholder="Jméno"><br><br>
        <input type="password" name="pass" placeholder="Heslo"><br><br>
        <button name="register">Registrovat</button>
    </form>

<?php else: ?>

    <div class="box">
        Přihlášen jako <b><?= $_SESSION['user_name'] ?></b>
        <a href="?logout=1" style="float:right">Odhlásit</a>
    </div>

    <!-- MENU -->
    <div class="box">
        <a href="index.php">🏠 Domů</a> |
        <a href="index.php?page=friends">👥 Přátelé</a> |
        <a href="index.php?page=profile">👤 Profil</a>
    </div>

    <?php
    /* ---------------------------------------------
       SWITCH STRÁNEK
    ---------------------------------------------- */

    if (isset($_GET['page']) && $_GET['page'] === "friends") {

        echo "<h2>Přátelé</h2>";

        $stmt = $pdo->prepare("
            SELECT u2.name AS friend
            FROM friends 
            JOIN users u2 ON u2.id = friends.friend_id
            WHERE friends.user_id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $friends = $stmt->fetchAll();

        foreach ($friends as $f) {
            echo "<div class='box'>" . $f['friend'] . "</div>";
        }

    } elseif (isset($_GET['page']) && $_GET['page'] === "profile") {

        echo "<h2>Můj profil</h2>";

        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $u = $stmt->fetch();

        echo "<div class='box'>
                <b>Jméno:</b> {$u['name']}<br>
                <b>Vytvořen:</b> {$u['created_at']}
              </div>";

    } elseif (isset($_GET['group'])) {

        /* ---------------------------------------------
           ZOBRAZENÍ ZPRÁV VE SKUPINĚ
        ---------------------------------------------- */

        $group_id = intval($_GET['group']);

        // název skupiny
        $stmt = $pdo->prepare("SELECT * FROM groups WHERE id = ?");
        $stmt->execute([$group_id]);
        $g = $stmt->fetch();

        echo "<h2>Skupina: {$g['name']}</h2>";

        // zprávy
        $stmt = $pdo->prepare("
            SELECT texts.*, users.name AS author 
            FROM texts
            JOIN users ON users.id = texts.user_id
            WHERE texts.group_id = ?
            ORDER BY texts.sent_at
        ");
        $stmt->execute([$group_id]);
        $msgs = $stmt->fetchAll();

        foreach ($msgs as $m) {
            echo "<div class='box'>
                    <b>{$m['author']}</b><br>
                    {$m['content']}<br>
                    <small>{$m['sent_at']}</small>
                  </div>";
        }

        // formulář
        ?>
        <form method="post" class="box">
            <textarea name="content" placeholder="Napiš zprávu..." style="width:100%;height:80px"></textarea><br><br>
            <input type="hidden" name="group_id" value="<?= $group_id ?>">
            <button name="send_message">Odeslat</button>
        </form>
        <?php

    } else {

        /* ---------------------------------------------
           DOMOVSKÁ STRÁNKA — SEZNAM SKUPIN
        ---------------------------------------------- */

        echo "<h2>Skupiny</h2>";

        foreach ($pdo->query("SELECT * FROM groups") as $g) {
            echo "<div class='box'>
                    <a href='index.php?group={$g['id']}'>
                    {$g['name']}
                    </a>
                  </div>";
        }
    }

    ?>

<?php endif; ?>

</body>
</html>

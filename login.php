<?php
/*require_once('head.php');
require_once('db.php');*/
if($_POST['login'] == 'Login') {
    //login
    try {
        $stmt = $db->prepare('SELECT * FROM users WHERE name = ?');
        $stmt->execute(array(trim($_POST['name'])));
        $data = $stmt->fetch();
        if($data['password'] == crypt($_POST['password'], 'd2y-98u')) {
            $_SESSION['loggedIn'] = true;
            $_SESSION['user'] = $data;
            header('Location: ' . ROOT . '/');
            include('list.php');
            return;
        } else {
            $_SESSION['loggedIn'] = false;
            throw new Exception("Passwort oder Benutzername falsch");
        }
    } catch(PDOException $e) {
        echo "<strong>Datenbankfehler:</strong> " . $e->getMessage() . "<br />";
    } catch(Exception $e) {
        echo "<strong>Fehler:</strong> " . $e->getMessage() . "<br />";
    }
}
$titlePrepend = 'Login - ';
?>
<form name="loginForm" action="" method="post" enctype="application/x-www-form-urlencoded">
    <fieldset>
        <legend>Login:</legend>
        <p><em>Wichtig: zum Einloggen müssen Cookies in deinem Browser aktiviert sein!</em></p>
        <p>Klicke <a href="/register">hier</a>, um einen neuen Benutzer zu erstellen.</p>
        <p><label for="name">Benutzername:</label>
        <input type="text" name="name" placeholder="Benutzername" /></p>
        <p><label for="password">Passwort:</label>
        <input type="password" name="password" placeholder="Passwort" /></p>
        <p><input type="submit" name="login" value="Login" /></p>
    </fieldset>
</form>

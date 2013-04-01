<?php
/*require_once('head.php');
require_once('db.php');*/
$name = trim($_POST['name']);
if(empty($name)) $name = null;
$passwd = $_POST['password'];
if(empty($passwd)) $passwd = null;
$rpasswd = $_POST['repeatpw'];
$email = trim($_POST['email']);
if(empty($email)) $email = null;
$fullName = trim($_POST['full_name']);
if(empty($fullName)) $fullName = null;
$course = trim($_POST['course']);
if(empty($course)) $course = null;
$image = trim($_POST['image']);
if(empty($image)) $image = null;
if($_POST['submit'] == 'Registrieren') {
    // registrieren
    try {
        if(preg_match('/^[0-9a-zA-Z\-]{3,32}$/', $name) == 0) {
            throw new Exception("Name muss mind. 3 und max. 32 Zeichen haben und kann aus Buchstaben und Zahlen bestehen");
        }
        if(strlen($passwd) < 4 or strlen($passwd) > 32) {
            throw new Exception("Passwort muss mind. 4 und max. 32 Zeichen haben");
        }
        if($passwd != $rpasswd) {
            throw new Exception("Passwörter stimmen nicht überein!");
        }
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Ungültige E-Mail-Adresse");
        }
        if(strlen($fullName) < 3) {
            throw new Exception("Name muss mind. 3 Zeichen haben");
        }
        if(strlen($course) > 5) {
            throw new Exception("Kursnummer darf max. 5 Zeichen haben");
        }
        $stmt = $db->prepare('INSERT INTO users (name, password, email, full_name, course, image, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())');
        $stmt->execute(array(
            $_POST['name'],
            crypt($_POST['password'], 'd2y-98u'),
            $_POST['email'],
            $_POST['full_name'],
            $_POST['course'],
            $_POST['image']
        ));
        if(MAILS) {
            mail("$fullName <$email>", "[Abi-12] Registrierung erfolgreich", "Hallo $fullName,\n\ndu wurdest soeben erfolgreich auf http://" . HOST . " registriert!\n\nDein Benutzername: $name\n\n Viel Spaß!", "Content-Type: text/plain; charset=utf-8\nFrom: Abizeitung <info@" . HOST . ">");
        }
        header('Location: ' . ROOT . '/login');
    } catch(PDOException $e) {
        echo "<strong>Datenbankfehler:</strong> " . $e->getMessage() . "<br />Evtl. wurde dieser Name bereits registriert.<br />";
    } catch(Exception $e) {
        echo "<strong>Fehler:</strong> " . $e->getMessage() . "<br />";
    }
}
$titlePrepend = 'Registrieren - ';
?>
<form name="registerForm" action="" method="POST" enctype="application/x-www-form-urlencoded">
    <fieldset>
        <legend>Deine Daten:</legend>
        <label for="name">Nickname:</label>
        <input type="text" name="name" placeholder="Nickname" value="<?= $name ?>" /><br />
        <label for="password">Passwort:</label>
        <input type="password" name="password" placeholder="Passwort" /><br />
        <label for="repeatpw">Passwort wiederholen:</label>
        <input type="password" name="repeatpw" placeholder="Passwort wiederholen" /><br />
        <label for="email">E-Mail:</label>
        <input type="text" name="email" placeholder="Adresse" value="<?= $email ?>" /><br />
        <label for="full_name">Vorname Nachname:</label>
        <input type="text" name="full_name" placeholder="Vorname Nachname" value="<?= $fullName ?>" /><br />
        <label for="course">Tutorenkurs:</label>
        <input type="text" name="course" placeholder="Tutorenkurs" value="<?= $course ?>" /><br />
        <!--<label for="image">Bild-URL:</label>
        <input type="text" name="image" placeholder="Bild-URL" value="<?= $image ?>" /><br />-->
        <input type="submit" name="submit" value="Registrieren" />
    </fieldset>
</form>

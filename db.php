<?php
try {
    $db = new PDO('mysql:host=' . $mysql_host . ';dbname=' . $mysql_database, $mysql_user, $mysql_password, array(
        //PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ));
    $db->query('DELETE FROM comments WHERE deleted = 1 AND (TO_DAYS(CURDATE()) - TO_DAYS(updated_at)) > 5');
} catch(PDOException $e) {
    die("Datenbankfehler: " . $e->getMessage() . '<br /><a href="javascript:location.reload()">Nochmal versuchen</a>');
}
?>

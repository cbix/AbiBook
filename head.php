<?php
/*if(!$_SESSION['loggedIn'] and $_SERVER['REQUEST_URI'] != '/register.php') {
    header('Location: /login.php');
    die('Nicht eingeloggt.');
}*/

?>
<div id="header">
<div id="navi" style="text-align: center;">
    <?php if(!$_SESSION['loggedIn']) { ?>
    <a href="<?= ROOT ?>/register">Registrieren</a> |
    <a href="<?= ROOT ?>/login">Login</a> |
    <?php } ?>
    <a href="<?= ROOT ?>/">Übersicht</a>
    | <a href="<?= ROOT ?>/newest">Neueste Einträge</a>
    <?php if($_SESSION['loggedIn']) { ?>
    | Eingeloggt als <a href="<?= ROOT ?>/user/<?= $_SESSION['user']['name'] ?>"><?= $_SESSION['user']['full_name'] ?>
    (<?= $_SESSION['user']['name'] ?>)</a><?= ($_SESSION['user']['admin'] ? ' [admin]' : '') ?>
    | <a href="<?= ROOT ?>/logout">Logout</a>
    <?php } ?>
</div>
</div>

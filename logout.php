<?php
session_start();
$_SESSION['user'] = null;
$_SESSION['loggedIn'] = false;
session_destroy();
header('Location: ' . ROOT . '/');
?>

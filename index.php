<?php
header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding("UTF-8");
date_default_timezone_set('Europe/Berlin');
require_once('config.php');
session_start();
if(!isset($_SESSION['loggedIn'])) {
    $_SESSION['loggedIn'] = false;
}
define('LOGGED_IN', $_SESSION['loggedIn']);

function getUserLink($user, $showCourse = true, $makeLink = true) {
    if($makeLink) {
        return '<a class="userlink" href="' . ROOT . '/user/' . $user['name'] . '">' . $user['full_name'] . '</a>' .
        ($showCourse ? ' (' . $user['course'] . ')' : '');
    } else {
        return $user['full_name'] . ($showCourse ? ' (' . $user['course'] . ')' : '');
    }
}

$page = $_GET['page'];
$titlePrepend = '';
$titleAppend = '';
$currentUserPageId = -1;
require_once('db.php');
ob_start();
switch($page) {
    case 'logout': include('logout.php'); break;
    case 'register': include('register.php'); break;
    case 'user': include('list.php'); break;
    case 'lastComments': include('lastComments.php'); break;
    default:
    if(LOGGED_IN) {
        $name = $_SESSION['user']['name'];
        $currentUserPageId = $_SESSION['user']['id'];
        include('list.php');
    } else {
        include('login.php');
    }
}
$content = ob_get_contents();
ob_end_clean();
?>
<!DOCTYPE html>
<html>
<head>
<link rel="icon" href="http://<?= HOST . ROOT ?>/favicon.ico" type="image/x-icon">
<title><?= $titlePrepend ?>Abizeitung 2012<?= $titleAppend ?></title>
<style type="text/css">
html, body {
    font-family: sans-serif;
    height: 100%;
    margin: 0px;
}
div#header {
    position: fixed;
    top: 0px;
    width: 100%;
    text-align: center;
    padding: 0px;
}
div#navi {
    background-color: black;
    color: white;
    padding: 4px;
    font-weight: bold;
}
div#navi a {
    color: #AEBAF7;
}
div#list {
    position: fixed;
    bottom: 0px;
    right: 0px;
    width: 40%;
    height: 90%;
    overflow: auto;
    float: right;
    padding: 5px;
}
li.usersEntry.active {
    font-weight: bold;
}
div#content {
    width: 55%;
    padding: 13px;
}
div#footer {
    clear: both;
    padding: 10px;
}
ul.commentList {
    list-style-type: none;
}
ul.commentList div {
    background-color: #D4ECFF;
    padding: 13px;
    margin: 10px;
    text-align: justify;
}
li.comment.deleted > div {
    background-color: #FFAE9F;
}
li#moreComments > a {
    text-decoration: none;
}
li#moreComments > a > div {
    background-color: #9FFFA0;
    text-align: center;
    font-size: 2em;
    color: black;
    padding: 20px;
    font-weight: bold;
    text-shadow: 2px 2px 2px;
}
span.commentAuthor {
    font-weight: bold;
}
span.commentDate, span.commentDelete {
    float: right;
    color: #A8A8A8;
    margin-left: 22px;
}
span.commentText {
    font-style: italic;
}
h1, h1.fullName, li.usersEntry.active {
    text-shadow: 1px 1px 4px #9B0000;
}
</style>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
</head>
<body>
<?php
include_once('head.php');
?>
<div id="list">
<h3>Schülerliste:</h3>
<?php
include('users.php');
?>
</div>
<div id="content">
    <br/>
    <?= $content ?>
</div>
<div id="footer">
    <p>Programmiert von <a href="<?= ROOT ?>/user/flo">Florian Hülsmann</a> 2012 | <a href="<?= ROOT ?>/abi12.zip">Code</a></p>
</div>
</body>
</html>
<?php
$db = null;
exit();
?>

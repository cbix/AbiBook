<?php
// Liste aller User ausgeben
/*require_once('head.php');
require_once('db.php');*/
?>
<ul class="usersList">
<?php
foreach($db->query('SELECT u.*, (SELECT COUNT(*) FROM comments c WHERE c.user_id = u.id AND c.deleted = 0) AS "num_comments" FROM users u ORDER BY upper(u.full_name) ASC') as $user) {
    $active = ($user['id'] == $currentUserPageId);
    ?>
    <li class="usersEntry<?= $active ? ' active' : '' ?>"><?= getUserLink($user, !$active, !$active) . ' [' . $user['num_comments'] . ']' . ($_SESSION['user']['id'] == $user['id'] ? " (Das bist du :D)" : "") ?></li>
<?php } ?>
</ul>

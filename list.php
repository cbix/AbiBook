<?php
/*require_once('head.php');
require_once('db.php');*/
// Liste der Kommentare eines Users
$oldHash = $_SESSION['formHash'];
$newHash = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'),0,16);
if(!isset($name)) {
    $name = $_GET['name'];
}
$comment = "";
if(empty($name)) {
    echo "Kein User angegeben";
} else {
    try {
        $stmt = $db->prepare('SELECT * FROM users WHERE name = ? LIMIT 1');
        $stmt->execute(array($name));
        $user = $stmt->fetch();
        if($user === false) {
            throw new Exception("Unbekannter Name");
        }
        $currentUserPageId = $id = $user['id'];
        $fullName = $user['full_name'];
        $titlePrepend = $fullName . ' - ';
        $email = $user['email'];
        $course = $user['course'];
        echo "<h1 class=\"fullName\">$fullName</h1>\n<h3>Tutorenkurs: $course</h3>";
        echo "<small class=\"email\"><a href=\"mailto:$email\">E-Mail schreiben</a></small>";
        if(isset($_POST['comment'])) {
            $comment = $_POST['comment'];
            $commentHash = $_POST['hash'];
            if(empty($comment) or $commentHash !== $_SESSION['formHash']) {
                echo "<p><em>Leerer Kommentar oder Hash abgelaufen! Bitte erneut versuchen!</em></p>";
            } else {
                if(!LOGGED_IN) {
                    echo "<p><em>Nicht eingeloggt!</em></p>";
                } else {
                    $stmt = $db->prepare('INSERT INTO comments (user_id, text, created_at, created_by, updated_at, updated_by) VALUES (?, ?, NOW(), ?, NOW(), ?)');
                    $myId = $_SESSION['user']['id'];
                    $myFullName = $_SESSION['user']['full_name'];
                    $stmt->execute(array($id, $comment, $myId, $myId));
                    if($stmt->rowcount() == 1) {
                        if($id != $myId and MAILS) {
                            mail(
                                $fullName . ' <' . $email . '>',
                                '[Abi-12] Neuer Kommentar auf deiner Seite!',
                                "Hey $fullName,\n\nJemand hat soeben folgenden Kommentar zu deiner Seite hinzugefügt:\n\n\"$comment\"\n\nHier ist der Link zu deiner Seite: http://" . HOST . ROOT . "/user/$name\n\nps: Bitte nicht auf diese E-Mail antworten..!",
                                "Content-Type: text/plain; charset=utf-8\nFrom: Abizeitung <info@" . HOST . ">"
                            );
                        }
                    } else {
                        echo "<p><em>Irgendwas ist da schief gegangen...</em></p>";
                    }
                }
            }
        } else {
            if(isset($_POST['delete']) and isset($_POST['checkDelete'])) {
                $stmt = $db->prepare('UPDATE comments SET updated_at = NOW(), updated_by = :activeuser, deleted = :deleteflag
                                      WHERE id = :cid
                                      AND deleted <> :deleteflag
                                      AND (
                                        user_id = :activeuser
                                        OR updated_by = :activeuser
                                        OR (deleted = 0 AND created_by = :activeuser)
                                        OR (:isadmin = 1)
                                      )');
                $stmt->bindParam(':activeuser', $_SESSION['user']['id']);
                $stmt->bindParam(':isadmin', $_SESSION['user']['admin']);
                $stmt->bindParam(':cid', $cid);
                $stmt->bindParam(':deleteflag', $deletedFlag);
                foreach($_POST['checkDelete'] as $cid => $deleted) {
                    $deletedFlag = (bool)$deleted;
                    $stmt->execute();
                    // TODO: try/catch
                }
            }
        }
        echo "<ul class=\"commentList\">\n";
        $hasComments = false;
        if(LOGGED_IN) {
            $_SESSION['formHash'] = $newHash;
            echo '<li><form action="" method="post" enctype="application/x-www-form-urlencoded"><input type="hidden" name="hash" value="' . $newHash . '" /><input type="text" name="comment" placeholder="neuer Kommentar" /></form></li>';
        }
        $stmt = $db->prepare("
            SELECT
                c.id AS \"cid\",
                c.user_id AS \"uid_to\",
                u.id AS \"uid_from\",
                c.text,
                c.deleted,
                c.created_at,
                c.created_by,
                c.updated_at,
                c.updated_by,
                c.priority,
                (TO_DAYS(CURDATE()) - TO_DAYS(c.updated_at)) AS \"age\"
            FROM comments c, users u
            WHERE
                c.user_id = ?
                AND c.created_by = u.id
            ORDER BY
                c.deleted ASC,
                c.priority DESC,
                c.created_at DESC,
                c.id DESC
        ");
        $stmt->execute(array($id));
        while($comment = $stmt->fetch()) {
            if(!$hasComments) {
                echo '<form action="" method="post" enctype="application/x-www-form-urlencoded">';
                echo '<li>&nbsp;</li>';
                echo '<li><input type="submit" name="delete" value="Gewählte löschen" /></li>';
                echo '<li><em>Die markierten Kommentare werden erst ausgeblendet und nach 5 Tagen automatisch komplett gelöscht!</em></li>';
                $hasComments = true;
            }
            if($comment['deleted']) {
                if($_SESSION['user']['id'] == $comment['uid_to'] or $_SESSION['user']['id'] == $comment['uid_from'] or $_SESSION['user']['admin']) {
                    $divClass = ' deleted';
                } else {
                    continue;
                }
            } else {
                $divClass = '';
            }
            ?>
            <li class="comment<?= $divClass ?>">
            <div>
                <?php
                if($_SESSION['user']['id'] == $comment['created_by']) {
                    echo '<span class="commentAuthor"><a class="userlink" href="' . ROOT . '/user/' . $_SESSION['user']['name'] . '">Du:</a></span>';
                }
                ?>
                <span class="commentDate"><?= date('d.m.Y', strtotime($comment['created_at'])) ?><?= ($comment['deleted'] ? ' (vor ' . $comment['age'] . ' Tag' . ($comment['age'] == 1 ? '' : 'en') . ' gelöscht)' : '') ?></span>
                <br />
                <span class="commentDelete"><?php
                if($comment['uid_to'] == $_SESSION['user']['id'] or $comment['updated_by'] == $_SESSION['user']['id'] or $_SESSION['user']['admin'] or ($_SESSION['user']['id'] == $comment['created_by'] and !$comment['deleted'])) {
                    ?>
                    <label for="checkDelete_<?= $comment['cid'] ?>">löschen:</label>
                    <input type="hidden" name="checkDelete[<?= $comment['cid'] ?>]" value="0" />
                    <input type="checkbox" name="checkDelete[<?= $comment['cid'] ?>]" id="checkDelete_<?= $comment['cid'] ?>"<?= ($comment['deleted'] ? ' checked="checked"' : '') ?> value="1" />
                    <?php
                }
                ?></span>
                <span class="commentText"><?= stripslashes(strip_tags($comment['text'])) ?></span>
            </div>
            </li>
            <?php
        }
        if($hasComments) {
            echo '<li>&nbsp;</li>';
            echo '<li><input type="submit" name="delete" value="Gewählte löschen" /></li>';
            echo '</form>';
        }
        echo "\n</ul>";
    } catch(PDOException $e) {
        echo "<strong>Datenbankfehler:</strong> " . $e->getMessage() . "<br />";
    } catch(Exception $e) {
        echo "<strong>Fehler:</strong> " . $e->getMessage() . "<br />";
    }
}
?>

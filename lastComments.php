<?php
require_once('config.php');
require_once('db.php');
$limit = 10;
if(isset($_GET['pageNumber'])) {
    $pageNum = (int) $_GET['pageNumber'];
    $ajax = true;
} else {
    $pageNum = 0;
    $ajax = false;
}
$limitFrom = $pageNum * $limit;
try {
    $stmt = $db->prepare("
        SELECT
            c.id AS \"cid\",
            c.user_id AS \"uid_to\",
            c.text,
            c.created_at,
            u.name,
            u.full_name
        FROM comments c, users u
        WHERE
            c.user_id = u.id
            AND c.deleted = 0
        ORDER BY
            c.created_at DESC,
            c.id DESC
        LIMIT $limitFrom, $limit
    ");
    $stmt->execute();
    if($ajax) {
        header('Content-Type: text/html; charset=utf-8');
        mb_internal_encoding("UTF-8");
    } else {
        $titlePrepend = "Letzte $limit Kommentare - ";
        ?>
        <script type="text/javascript">
        var latestCommentsPage = 1;
        $(document).ready(function() {
            $('li#moreComments a').click(function(e) {
                e.preventDefault();
                if(latestCommentsPage < 0) {
                    return;
                }
                $.ajax({
                    url: '<?= ROOT ?>/lastComments.php',
                    data: {pageNumber: latestCommentsPage},
                    dataType: 'html',
                    success: function(data) {
                        if(data) {
                            latestCommentsPage++;
                            $(data).hide().insertBefore('li#moreComments').fadeIn();
                        } else {
                            $('li#moreComments').fadeOut();
                            latestCommentsPage = -1;
                        }
                    }
                });
            });
        });
        </script>
        <h1>Letzte <?= $limit ?> Kommentare:</h1>
        <ul class="commentList">
        <?php
    }
while($comment = $stmt->fetch()) { ?>
    <li class="comment">
        <div>
            <span class="commentAuthor"><a class="userlink" href="<?= ROOT ?>/user/<?= $comment['name'] ?>"><?= $comment['full_name'] ?></a></span>
            <span class="commentDate"><?= date('d.m.Y', strtotime($comment['created_at'])) ?></span>
            <span class="commentText"><?= stripslashes(strip_tags($comment['text'])) ?></span>
        </div>
    </li>
<?php
}
if($ajax) {
    exit();
} else {
    echo '<li id="moreComments"><a href="#"><div>Weitere Einträge laden ...</div></a></li></ul>';
}
} catch(PDOException $e) {
    echo "Datenbankfehler: " . $e->getMessage();
} catch(Exception $e) {
    echo "Fehler: " . $e->getMessage();
}
?>

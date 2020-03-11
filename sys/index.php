<?php
session_start();
if (!isset($_SESSION['nicname'])) {
    header('Location: login.php'); exit();
}
require_once 'core/functions.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <?php show_pagetitle(3) ?>
        <?php include_head() ?>
    </head>
	<body>
        <?php get_header("private") ?>
        <div class="wrapper">
            <div class="inner">
                <div class="container main_content">
                    <?php get_sidebar() ?>
                    <main id="primary">
                        <?php if (isset($_GET['mailSetPage'])): ?>
                            <?php require_once './settings/mail_set.php' ?>
                        <?php elseif (isset($_GET['autoTw'])): ?>
                            <?php require_once 'autotw/tweet_set.php' ?>
                        <?php else: ?>
                        <h2 class="blue">ツイートタイムライン</h2>
                        <a class="twitter-timeline" data-height="1000" data-theme="dark"
                        data-link-color="#FAB81E" data-chrome="noheader nofooter" data-lang="ja"
                        href="https://twitter.com/<?php echo $_SESSION['user_id'] ?>?ref_src=twsrc%5Etfw"></a>
                        <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script> 
                        <!--<p><?php var_dump($_SESSION) ?></p>-->
                        <?php endif; ?>
                    </main>
                </div>
            </div>
        </div>
        <?php get_footer(); ?>
    </body>
</html>
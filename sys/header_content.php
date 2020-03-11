<!-- 共通メッセージウィンドウ -->
<?php $messagemodale = isset($_GET['rt_mg']) ? 'true' : 'false'; ?>
<div id="message_modal_mask" data-messagemodale="<?php echo $messagemodale ?>">
    <button type="button" id="message_modal_close">
        <i class="fas fa-times fa-2x"></i><br/>CLOSE
    </button>
</div>
<article id="message_modal_wrapper">
        <div id="message_modal_content">
            <?php if ($messagemodale === "true"): ?>
                <h2>通知</h2>
                <?php echo htmlspecialchars($_GET['rt_mg']) ?>
            <?php endif; ?>
        </div>
</article>
<!-- // 共通メッセージウィンドウ -->
<?php if ($header_type === "simple"): ?>
    <header>
        <div class="container">
            <div class="header_content">
                <h1><?php show_pagetitle() ?></h1>
            </div>
        </div>
    </header>
<?php elseif ($header_type === "private"): ?>
    <?php require_once 'load_myaccounts.php' ?>
    <header class="add_hdeader_bg">
        <div class="container">
            <div class="header_content">
                <h1><a class="site_title_link" href="<?php echo SITE_URL ?>"><?php show_pagetitle() ?></a></h1>
                <div class="header_btns">
                    <a id="logout_btn" href="logout.php">ログアウト</a>
                </div>
            </div>
        </div>
    </header>
<?php endif; ?>
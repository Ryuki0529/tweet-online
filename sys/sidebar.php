<aside id="sideber">
    <div class="select_tworth_box">
        <img src="<?php echo $_SESSION['prof_img'] ?>" />
        <center><p style="margin:5px 0px;"><?php echo $_SESSION['nicname'] ?></p>
        <a href="https://twitter.com/<?php echo $_SESSION['user_id'] ?>" target="_blank">
            @<?php echo $_SESSION['user_id'] ?>
        </a></center>
        <button type="button" id="myaccounts_menu_show">
        <i class="fas fa-check-double"></i>&nbsp;アカウント切り替え
        </button>
        <button type="button" id="add_myaccount">
            <i class="fas fa-plus"></i>&nbsp;アカウント追加
        </button>
        <!--<button type="button" id="tweet_currenttime">
            <i class="fas fa-plus"></i>&nbsp;ツイートする
        </button>-->
    </div>
    <h2 class="green mdium-font">ツールメニュー</h2>
    <nav><ul>
        <li><a class="nav-menu-link" href=".?autoTw">つぶやき予約登録</a></li>
        <li><a class="nav-menu-link" href=".?twEdit">つぶやき編集と投稿履歴(準備中)</a></li>
        <li><a class="nav-menu-link" href=".?hashGp">ハッシュタググループ登録(準備中)</a></li>
        <li><a class="nav-menu-link" href=".?flCp">フォロワーコピー(準備中)</a></li>
    </ul></nav>
    <h2 class="yellow mdium-font">管理メニュー</h2>
    <nav><ul>
        <li><a class="nav-menu-link" href=".?mailSetPage">メールアドレスログイン設定</a></li>
        <li><a class="nav-menu-link" href="./tagput/">アカウント退会手続き(準備中)</a></li>
    </ul></nav>
    <p style="border: 1px dotted white; padding: 8px;">
    <a href="https://twitter.com/prog_php" target="_blank">お問い合わせはザキヤマまで。</a>
    </p>
</aside>
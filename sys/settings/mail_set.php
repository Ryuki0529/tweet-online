<?php
if (!$mysqli = db_conect()) die('データベース接続エラー'); ?>

<h2 class="orange">メールアドレスログイン設定</h2>
<?php if (isset($_POST['loginset'])): ?>
    <?php
    $loginData = db_query_escape($mysqli, array(
        'mail' => $_POST['mail'],
        'pass' => password_hash($_POST['pass'], PASSWORD_DEFAULT),
        'group_id' => $_SESSION['group_id']
    ));
    if (is_mail($loginData['mail'])) {
        $res = $mysqli->query(
            "INSERT INTO users_mail(
                group_id, mail, pass, is_active
            )VALUE (
                '${loginData['group_id']}', '${loginData['mail']}',
                '${loginData['pass']}', 1
            )"
        );
    }
    ?>
    <p>登録完了しまさいた。：<?php print_r($res) ?>：<?php echo $mysqli->error; ?></p>
    <a class="nav-menu-link" href=".?mailSetPage">🔙&nbsp;戻る</a>
<?php else: ?>
    <?php
    $query_escapes = db_query_escape($mysqli, array(
        'group_id' => $_SESSION['group_id']
    ));
    $res = $mysqli->query(
        "SELECT mail FROM users_mail
        WHERE group_id = '${query_escapes['group_id']}'"
    );
    if ($res->num_rows === 1): ?>
        <?php
        $mail = db_resload_select($res);
        ?>
        <p>ログイン設定済みです。</p>
        <div class="form-area" method="post" action="">
            <label class="form-label" for="mail">メールアドレス</label>
            <div class="form-item">
                <?php echo $mail[0]['mail']; ?>
            </div>
            <div class="form-validation" id="mail_validation"></div>
        </div>
    <?php else: ?>
        <p>
            ログイン時にメールアドレスとパスワードが利用できるようになります。
            メールアドレスをご登録いただく上での留意点はページ下の「留意事項」を御覧ください。
            留意事項に予め同意した上で、メールアドレスの登録をお願いします。
        </p>
        <form class="form-area" method="post" action="">
            <label class="form-label" for="mail">メールアドレス</label>
            <div class="form-item">
                <input type="mail" name="mail" id="mail">
            </div>
            <div class="form-validation" id="mail_validation"></div>
            <label class="form-label" for='pass'>パスワード</label>
            <div class="form-item">
                <input type="password" name="pass" id="pass">
            </div>
            <div class="form-validation">
                <button type="button" id="show_pass">パスワードを表示</button>
            </div>
            <div class="form-submit">
                <center><p>※必ず上記内容で間違いないこと確認ください。</p>
                <input type="submit" id="mail_submit" name="loginset" value="登録する"></center>
            </div>
        </form>
    <?php endif; ?>
<?php endif;
$mysqli->close(); ?>

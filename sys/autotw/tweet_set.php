<?php
$is_twset = FALSE; $is_twdel = FALSE;
if (isset($_POST['autoTwSet'])) {
    if (isset($_POST['twstock'])) {
        $twstock = 1;
    }else {
        $twstock = 0;
    }
    if ($mysqli = db_conect()) {
        $query_data = array(
            'text' => $_POST['twtext'], 'is_hist' => $twstock,
            'settime' => $_POST['date'].' '.$_POST['hour'].':'.$_POST['minute'],
            'group_id' => $_SESSION['group_id'], 'at_id' => $_SESSION['id']
        );
        $query_escapes = db_query_escape($mysqli, $query_data);
        if ($res = $mysqli->query(
            "INSERT INTO auto_tweet(
                group_id, text, settime, is_hist, at_id
            )VALUE (
                '${query_escapes['group_id']}', '${query_escapes['text']}',
                '${query_escapes['settime']}', '${query_escapes['is_hist']}',
                '${query_escapes['at_id']}'
            )"
        )) {
            $is_twset = TRUE;
        }else echo $mysqli->error;
    }
    $mysqli->close();
}elseif (isset($_GET['del'])) {
    if ($mysqli = db_conect()) {
        $query_data = array(
            'group_id' => $_SESSION['group_id'], 'id' => $_GET['del']
        );
        $query_escapes = db_query_escape($mysqli, $query_data);
        if ($res = $mysqli->query(
        "DELETE FROM auto_tweet
            WHERE id = '${query_escapes['id']}' AND group_id = '${query_escapes['group_id']}'"
        )) {
            $is_twdel = TRUE;
        }else echo 'エラー：'.$mysqli->error;
    }
    $mysqli->close();
}
?>

<h2 class="blue">ツイート予約登録</h2>
<form class="form-area" method="post" action="">
    <textarea id="twtext" name="twtext" placeholder="ツイート内容の入力（１４０文字以内）"></textarea>
    <span id="twTextCount">0 / 140</span>
    <label class="form-label" for="date">つぶやき日時</label>
    <div class="form-item" style="max-width:60%;">
        <input type="date"
            name="date" value="<?php echo date('Y-m-d') ?>"
            min="<?php echo date('Y-m-d') ?>"
            id="date" style="width:unset;"
        >
        <input type="number" name="hour"
            value="<?php echo date('H') ?>"
            style="width:60px;" min ="00" max="23"
        >：
        <input type="number" name="minute"
            value="00" style="width:60px;" step="15" min="00" max="59"
        >
        <input type="checkbox" name="twstock" id="twstock" checked>
        <label for="twstock"><i class="far fa-check-square"></i>&nbsp;履歴を残す</label>
    </div>
    <div class="form-submit-mini">
        <input type="submit" name="autoTwSet" id="autoTwSet"
            value="登録する" style="width:100%;"
        >
    </div>
</form>
<?php if ($is_twset === TRUE): ?>
    <span id="twset-message"><i class="fas fa-check-circle"></i>
        &nbsp;つぶやきを登録しました！
        <i class="fas fa-times-circle" id="twset-message-close"></i>
    </span>
<?php elseif ($is_twdel === TRUE): ?>
    <span id="twset-message" style="background: rgb(255, 0, 204);">
        <i class="fas fa-check-circle"></i>&nbsp;予約済みつぶやきを削除しました。
        <i class="fas fa-times-circle" id="twset-message-close"></i>
    </span>
<?php endif; ?>
<h3 class="gray"><i class="fas fa-check-circle"></i>&nbsp;最近登録したつぶやき</h3>
<?php
if ($mysqli = db_conect()) {
    $query_data = array(
        'group_id' => $_SESSION['group_id'],
        'at_id' => $_SESSION['id']
    );
    $query_escapes = db_query_escape($mysqli, $query_data);
    $res = $mysqli->query(
        "SELECT * FROM auto_tweet
            WHERE group_id = '${query_escapes['group_id']}'
                AND at_id = '${query_escapes['at_id']}'
            ORDER BY setdate DESC LIMIT 0, 7"
    );
}
?>
<div class="twhist-area">
    <?php if ($res->num_rows !== 0): ?>
        <?php
        $twhist = db_resload_select($res);
        foreach ($twhist as $record) { ?>
            <div class="twhist-bg">
                <div class="twhist-content">
                    <span class="settime-text">
                        予約日時：<?php echo date('Y年m月d日 H時i分', strtotime($record['settime'])) ?>
                    </span>
                    <hr>
                    <?php echo nl2br(htmlspecialchars($record['text'])) ?>
                    <span class="setdate-text">
                        登録日：<?php echo date('Y年m月d日', strtotime($record['setdate'])) ?>
                        <div class="twhist-edit-area">
                            <div class="twhist-edit-window">
                                <a class="twdel-btn" href=".?autoTw&del=<?php echo htmlspecialchars($record['id']) ?>">取消</a>
                            </div>
                            <button type="button" class="twhist-edit-btn">
                                <i class="fas fa-sliders-h"></i>
                            </button>
                        </div>
                    </span>
                </div>
            </div>
        <?php } ?>
    <?php else: ?>
        <p>登録されているつぶやきはありません。</p>
    <?php endif; ?>
</div>
<?php $mysqli->close(); ?>
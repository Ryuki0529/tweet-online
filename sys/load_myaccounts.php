<?php
if ($mysqli = db_conect()) {
    $query_data = array('group_id' => $_SESSION['group_id']);
    $query_escapes = db_query_escape($mysqli, $query_data);
    if ($res = $mysqli->query(
        "SELECT nicname, user_id, prof_img FROM users
        WHERE group_id = '${query_escapes['group_id']}'"
    )) {
        $userdata = db_resload_select($res);
    }
}
$mysqli->close();
?>
<div id="myaccounts_menu_mask">
    <button type="button" id="myaccounts_menu_close">
        <i class="fas fa-times fa-2x"></i><br/>CLOSE
    </button>
    <article id="myaccounts_menu">
        <h2>アカウントを選択</h2>
        <div id="myaccounts_wrapper">
            <div id="myaccounts_entries">
                <?php foreach ($userdata as $data): ?>
                <a class="myaccounts_entry" href=".?at_set=<?php echo $data['user_id'] ?>">
                    <div class="profile_img">
                        <img src="<?php echo $data['prof_img'] ?>" />
                    </div>
                    <div class="profile_meta">
                        <span style="font-size:17px;"><?php echo $data['nicname'] ?></span><br/>
                        <span style="color:gray;">@<?php echo $data['user_id'] ?></span>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </article>
</div>
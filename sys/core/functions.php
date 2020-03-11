<?php
require_once 'config.php';
// twitteroauth の読み込み
require_once "twitteroauth/autoload.php";

// データベースに接続し、インスタンスを返す。
function db_conect($db_array = array(DB_HOST, DB_USER, DB_PASS, DB_NAME)) {
    $mysqli = new mysqli($db_array[0], $db_array[1], $db_array[2], $db_array[3]);
    if ($mysqli->connect_error) {
        error_log($mysqli->connect_error);
        return FALSE;
    }else {
        $mysqli->set_charset('utf8');
        return $mysqli;
    }
}

// SQL文の一括エスケープ処理
function db_query_escape($sqlobj, $query_array) {
    $escape_string = array();
    foreach ($query_array as $dataname => $data) {
        $escape_string[$dataname] = $sqlobj->real_escape_string($data);
    }
    return $escape_string;
}

// SQL文「SELECT」で取得したレコードを一括返還
function db_resload_select($resalt) {
    $res = array();
    while ($data = $resalt->fetch_assoc()) {
        $res[] = $data;
    }
    return $res;
}

// ユーザーセッションをもろもろセットする
function set_user_session($key_array) {
    foreach ($key_array as $key => $data) {
        $_SESSION[$key] = $data;
    }
}

// ページタイトル出力
function show_pagetitle($mode = 0, $text = "", $title = SITE_TITLE) {
    if ($mode === 1) {
        echo $text."｜".$title;
    }else if ($mode === 0) {
        echo $title;
    }else if ($mode === 2) {
        echo "<title>".$text."｜".$title."</title>";
    }else if ($mode === 3) {
        echo "<title>".$title."</title>";
    }
}

// 共通の<head>タグ内読み込み
function include_head() {
    require_once 'head.php';
}

// <header>タグ内共通
function get_header($type = "simple") {
    $header_type = $type;
    require_once 'header_content.php';
}

// フッター読み込み
function get_footer() {
    require_once 'footer.php';
}

// サイドバー読み込み
function get_sidebar() {
    require_once 'sidebar.php';
}

// メアドログイン時のファーストアカウント表示フラグ
function set_firstview_flag($sqlobj) {
    $query_data = array(
        'id' => $_SESSION['id'],
        'group_id' => $_SESSION['group_id']
    );
    $query_escapes = db_query_escape($sqlobj, $query_data);
    if ($sqlobj->query(
        "UPDATE users SET default_show = REPLACE(default_show, 1, 0)
        WHERE group_id = '${query_escapes['group_id']}'"
    )) {
        if ($sqlobj->query(
            "UPDATE users SET default_show = 1
            WHERE group_id = '${query_escapes['group_id']}' AND id = '${query_escapes['id']}'"
        )) {
            //echo "デバック：".$sqlobj->error.get_class($sqlobj);
            return TRUE;
        }else return FALSE;
    }
}

// アカウント切り替え
function switching_act() {
    if (isset($_GET['at_set'])) {
        if ($mysqli = db_conect()) {
            $current_data = array(
                'user_id' => $_GET['at_set'],
                'group_id' => $_SESSION['group_id']
            );
            $query_escapes = db_query_escape($mysqli, $current_data);
            if ($res = $mysqli->query(
                "SELECT id, nicname, user_id, access_token, access_token_secret, prof_bg_img, prof_img
                FROM users WHERE user_id = '${query_escapes['user_id']}' AND group_id = '${query_escapes['group_id']}'"
            )) {
                if ($res->num_rows === 1) {
                    $swich_data = db_resload_select($res);
                    set_user_session($swich_data[0]);
                    set_firstview_flag($mysqli);
                }//else echo '該当なし'; echo $mysqli->error;
            }
            $mysqli->close();
        }
    }
}
switching_act();

// 各ページでheadタグ内で読み込むファイルを制御
function head_load_select($pagetag, $filepath, $filetype = "script") {
    if (isset($_GET[$pagetag])) { ?>
        <?php if ($filetype === "script") { ?>
            <script src="<?php echo $filepath ?>"></script>
        <?php }else if ($filetype === "style") { ?>
            <link rel="stylesheet" href="<?php echo $filepath ?>">
        <?php } ?>
    <?php }
}

// メールアドレスのバリデーション
function is_mail($text) {
    if (preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $text)) {
            return TRUE;
    } else {
            return FALSE;
    }
}
?>
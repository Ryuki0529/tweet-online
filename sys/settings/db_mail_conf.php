<?php
session_start();
if (!isset($_SESSION['nicname'])) {
    header('Location: login.php'); exit();
}
require_once '../core/functions.php';

if (isset($_POST['mail_check'])) {
    if (!$mysqli = db_conect()) die('データベース接続エラー');
    $mail = $mysqli->real_escape_string($_POST['mail']);
    if ($res = $mysqli->query(
        "SELECT mail FROM users_mail WHERE mail = '$mail'"
    )) {
        if ($res->num_rows === 0) {
            echo "success";
        }else {
            echo "failure";
        }
    }
    $mysqli->close();
}else {
    echo "不正なアクセスです。";
}
?>
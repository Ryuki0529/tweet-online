<?php
session_start();
if (isset($_SESSION['nicname'])) {
  header('Location: .'); exit();
}
require_once 'core/functions.php';
use Abraham\TwitterOAuth\TwitterOAuth;

// メールログイン処理
if (isset($_POST['login'])) {
  $error = FALSE;
  if ($mysqli = db_conect()) {
    $query_escapes = db_query_escape($mysqli, array(
      'mail' => $_POST['mail'], 'pass' => $_POST['pass']
    ));
    if ($res = $mysqli->query(
      "SELECT * FROM users_mail WHERE mail = '${query_escapes['mail']}'"
    )) {
      if ($res->num_rows === 1) {
        $userdata = db_resload_select($res);
        if (password_verify($query_escapes['pass'], $userdata[0]['pass'])) {
          $groupID = $userdata[0]['group_id'];
          if ($res = $mysqli->query(
            "SELECT nicname, user_id, access_token, access_token_secret,
                    prof_bg_img, prof_img
              FROM users
              WHERE group_id = '$groupID' AND default_show = 1"
          )) {
            $userdata = db_resload_select($res);
            $userdata[0]['group_id'] = $groupID;
            set_user_session($userdata[0]);
            header('Location: .'); exit();
          }else echo $mysqli->error;
        }else $error = TRUE;
      }else $error = TRUE;
    }else echo $mysqli->error;
    $mysqli->close();
  }
}

//「abraham/twitteroauth」ライブラリのインスタンスを生成し、Twitterからリクエストトークンを取得する
$connection = new TwitterOAuth(TWITTER_API_KEY, TWITTER_API_SECRET);
$request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => CALLBACK_URL_1));

//リクエストトークンはコールバックページでも利用するためセッションに格納しておく
$_SESSION['oauth_token'] = $request_token['oauth_token'];
$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

//Twitterの認証画面のURL
$oauthUrl = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
?>
<!DOCTYPE html>
<html>
  <head>
    <?php show_pagetitle(2, "ログインページ") ?>
    <?php include_head() ?>
    <link rel="stylesheet" href="./css/loginpage.css">
	</head>
	<body>
    <?php get_header() ?>
    <div class="wrapper">
    <div class="bg_bura">
    <div class="container">
      <div class="login_content">
        <div class="login_form">
          <form class="login-box" method="post" action="">
            <h2>ログイン</h2>
            <p style="color:yellow; clear:both;">
              <?php if (isset($error) && $error === TRUE) {
                echo 'メールアドレスまたはパスワードが間違っています。';
              } ?>
            </p>
            <div class="textbox">
              <i class="fas fa-user"></i>
              <input type="email" name="mail" placeholder="メールアドレス">
            </div>
            <div class="textbox">
                <i class="fas fa-lock"></i>
                <input type="password" name="pass" placeholder="パスワード">
            </div>
            <input type="submit" class="btn" name="login" value="サインイン">
          </form>
          <center><p>または</p></center>
          <a href="<?php echo $oauthUrl; ?>" class="loginbtn_twitter">
            <i class="fab fa-twitter"></i>&nbsp;Twitterでログイン・登録
          </a>
          <p>
            ※Twitterにアカウントをお持ちの方は、「Twitterでログイン」ボタンよりサインインしてください。
            既にメールアドレスとパスワードを設定している場合には、上記フォームでもログインできます。
          </p>
        </div>
        <div class="login_form">
          <div class="login-box">
            <h2>会員登録</h2>
            <p style="clear:both;">
            まずはTwitterに会員登録を行ってください。
            アカウントを作成しましたら、当サービスログインページより、
            「Twitterでログイン・登録」ボタンより操作をお願いします。
            </p>
            <center><a href="https://twitter.com/" class="register_twitter" target="_blank">
              <i class="fab fa-twitter"></i>&nbsp;Twitterに登録
            </a></center>
          </div>
        </div>
      </div>
    </div>
    </div>
    </div>
  </body>
</html>
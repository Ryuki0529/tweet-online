<?php
session_start();
//if (isset($_SESSION['nicname'])) header('Location: index.php');

require_once 'core/functions.php';
use Abraham\TwitterOAuth\TwitterOAuth;
$mg = ""; $error = FALSE;

//リクエストトークンを使い、アクセストークンを取得する
$twitter_connect = new TwitterOAuth(
	TWITTER_API_KEY, TWITTER_API_SECRET,
	$_SESSION['oauth_token'], $_SESSION['oauth_token_secret']
);
$access_token = $twitter_connect->oauth('oauth/access_token', array(
		'oauth_verifier' => $_GET['oauth_verifier'],
		'oauth_token'=> $_GET['oauth_token']
	)
);

//アクセストークンからユーザの情報を取得する
$user_connect = new TwitterOAuth(
	TWITTER_API_KEY, TWITTER_API_SECRET,
	$access_token['oauth_token'], $access_token['oauth_token_secret']
);
$user_info = $user_connect->get('account/verify_credentials');
// ↑アカウントの有効性を確認するためのエンドポイント

if ($mysqli = db_conect()) {
	$query_data = array(
		'oauth_token' => $access_token['oauth_token'],
		'access_token_secret' => $access_token['oauth_token_secret']
	);
	$query_escapes = db_query_escape($mysqli, $query_data);
	if ($res = $mysqli->query(
		"SELECT * FROM users
		WHERE
			access_token = '${query_escapes['oauth_token']}'
			AND
			access_token_secret = '${query_escapes['access_token_secret']}'"
	)) {
		if ($res->num_rows > 0) {
			$userdata = db_resload_select($res);
			set_user_session($userdata[0]);
			$mg = "ログインしました。";
		}else if ($res->num_rows === 0) {
			//echo 'デバック１：';
			if (isset($user_info->profile_banner_url)) {
				$prof_bg_img_url = $user_info->profile_banner_url;
			}else {
				$prof_bg_img_url = $user_info->profile_background_image_url_https;
			}
			$query_data = array(
				'nicname' => $user_info->name,
				'user_id' => $user_info->screen_name,
				'access_token' => $access_token['oauth_token'],
				'access_token_secret' => $access_token['oauth_token_secret'],
				'prof_bg_img' => $prof_bg_img_url,
				'prof_img' => str_replace("_normal", "", $user_info->profile_image_url_https)
			);
			$query_escapes = db_query_escape($mysqli, $query_data);
			if ($mysqli->query(
				"INSERT INTO users(
					nicname, user_id, access_token, access_token_secret,
					prof_bg_img, prof_img, default_show
				)VALUES(
					'${query_escapes['nicname']}', '${query_escapes['user_id']}',
					'${query_escapes['access_token']}', '${query_escapes['access_token_secret']}',
					'${query_escapes['prof_bg_img']}', '${query_escapes['prof_img']}', 1
				)"
			)) {
				//echo 'デバック２：';
				if ($res = $mysqli->query(
					"SELECT id FROM users
					WHERE
						access_token = '${query_escapes['access_token']}'
						AND
						access_token_secret = '${query_escapes['access_token_secret']}'"
				)) {
					//echo 'デバック３';
					$id = db_resload_select($res);
					$id = $id[0]['id'];
					$group_id = $_SERVER['REMOTE_ADDR'].":".$id;
					if ($mysqli->query(
						"UPDATE users SET group_id = '${group_id}' WHERE id = '$id'"
					)) {
						if ($res = $mysqli->query(
							"SELECT * FROM users WHERE access_token = '${query_escapes['access_token']}'"
						)) {
							$userdata = db_resload_select($res);
							set_user_session($userdata[0]);
							$mg = '会員登録完了';
						}else {
							$mg = '会員登録失敗（E-04）'; $error = TRUE;
						}
					}else {
						$mg = '会員登録失敗（E-03）'; $error = TRUE;
					}
				}else {
					$mg = '会員登録失敗（E-02）'.$mysqli->error; $error = TRUE;
				}
			}else {
				$mg = '会員登録失敗（E-01）'; $error = TRUE;
			}
		}
	}
}else print("データベース接続確立エラー");
$mysqli->close();
?>

<!DOCTYPE html>
<html>
	<head>
		<?php show_pagetitle(2, "コールバックページ") ?>
		<?php include_head() ?>
		<link rel="stylesheet" href="./css/loginpage.css">
		<?php if ($error === FALSE): ?>
		<meta http-equiv="refresh" content="3; url=index.php">
		<?php endif; ?>
	</head>
	<body>
		<?php get_header() ?>
		<div class="wrapper">
			<div class="bg_bura">
				<div class="container">
					<?php if ($error === FALSE): ?>
					<strong><p style="font-size:24px;"><?php echo $mg; ?></p></strong>
					<p>３秒後にユーザーページに移動します。</p>
					<?php elseif ($error === TRUE): ?>
					<strong><p style="font-size:24px;"><?php echo $mg; ?></p></strong>
					<p>無効な操作が行われたか、処理に問題が発生しました。<br/>
					再試行しても状況が変わらない場合、管理者までお問い合わせください。</p>
					<a href="login.php">ログインページに戻る</a>
					<?php endif; ?>
				</div>
			</div>
		</div>

	<?php if(isset($user_info->id_str) === 100000): ?>
			<p>ログイン成功：<?php echo $user_info->name; ?>さん</p>
			<p>ユーザー情報：<?php var_dump($query_escapes); ?></p>
			<p>アクセストークン：<?php echo $_SESSION['oauth_token'] ?></p>
			<p>アクセストークンシークレット：<?php echo $_SESSION['oauth_token_secret'] ?></p>
			<p>アクセストークン：<?php echo $access_token['oauth_token'] ?></p>
			<p>アクセストークンシークレット：<?php echo $access_token['oauth_token_secret'] ?></p>
			<a href="index.php">ホーム</a>
			<p>プロフィールバナー：<?php echo $user_info->profile_banner_url ?></p>
			<p>プロフィール画像：<?php echo $user_info->profile_image_url_https ?></p>
			<p><?php //var_dump($user_info) ?></p>
	<?php endif; ?>
	</body>
</html>
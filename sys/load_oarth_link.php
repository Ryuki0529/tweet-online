<?php
session_start();
require_once 'core/functions.php';
use Abraham\TwitterOAuth\TwitterOAuth;
?>
<?php if (isset($_SESSION['nicname'])): ?>
    <?php if (isset($_POST['oarthlink'])): ?>
    <?php
    $connection = new TwitterOAuth(TWITTER_API_KEY, TWITTER_API_SECRET);
    $request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => CALLBACK_URL_2));
    $_SESSION['oauth_token'] = $request_token['oauth_token'];
    $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
    $oauthUrl = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
    ?>
    <h2>アカウント追加</h2>
    <p>下記リンクを押していただくと、Tweetログイン認証画面に遷移します。<br/>
    そこで、新規に追加したいアカウントにログインしてください。</p>
    <p>その差異、既にログイン状態になっている場合は、一度Twitterからログアウトしていただき、
        再度追加対象アカウントにログインしていだだく必要があります。</p>
    <center><a href="<?php echo $oauthUrl ?>" class="loginbtn_twitter">
        <i class="fab fa-twitter"></i>&nbsp;Twitterアカウント追加
    </a></center>
    <?php elseif (isset($_GET['oauth_token']) && isset($_GET['oauth_verifier'])): ?>
        <?php
        $twitter_connect = new TwitterOAuth(
            TWITTER_API_KEY, TWITTER_API_SECRET,
            $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']
        );
        $access_token = $twitter_connect->oauth('oauth/access_token', array(
                'oauth_verifier' => $_GET['oauth_verifier'],
                'oauth_token'=> $_GET['oauth_token']
            )
        );
        $user_connect = new TwitterOAuth(
            TWITTER_API_KEY, TWITTER_API_SECRET,
            $access_token['oauth_token'], $access_token['oauth_token_secret']
        );
        $user_info = $user_connect->get('account/verify_credentials');

        if ($mysqli = db_conect()) {
            $query_data = array('oauth_token' => $access_token['oauth_token']);
            $query_escapes = db_query_escape($mysqli, $query_data);
            if ($res = $mysqli->query(
                "SELECT * FROM users WHERE access_token = '${query_escapes['oauth_token']}'"
            )) {
                if ($res->num_rows > 0) {
                    $rt_mg = urlencode('既にアカウントが存在しています。');
                }else if ($res->num_rows === 0) {
                    $query_data = array('group_id' => $_SESSION['group_id']);
                    $query_escapes = db_query_escape($mysqli, $query_data);
                    if ($mysqli->query(
                        "UPDATE users SET default_show = REPLACE(default_show, 1, 0)
                        WHERE group_id = '${query_escapes['group_id']}'"
                    )) {
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
                                prof_bg_img, prof_img, default_show, group_id
                            )VALUES(
                                '${query_escapes['nicname']}', '${query_escapes['user_id']}',
                                '${query_escapes['access_token']}', '${query_escapes['access_token_secret']}',
                                '${query_escapes['prof_bg_img']}', '${query_escapes['prof_img']}', 1, '${_SESSION['group_id']}'
                            )"
                        )) {
                            //echo 'デバック２：';
                            if ($res = $mysqli->query(
                                "SELECT * FROM users WHERE access_token = '${query_escapes['access_token']}'"
                            )) {
                                //echo 'デバック３';
                                $userdata = db_resload_select($res);
                                set_user_session($userdata[0]);
                                $rt_mg = 'アカウントを追加しました。';
                            }else {
                                $rt_mg = '問題が発生しました。（E-02）';
                            }
                        }else {
                            $rt_mg = '問題が発生しました。（E-01）';
                        }
                    }else {
                        $rt_mg = '問題が発生しました。（E-05）';
                    }
                }
            }
        }else $rt_mg = '問題が発生しました。（E-06）';
        $mysqli->close();
        header("Location: index.php?rt_mg=$rt_mg");　
        ?>
    <?php endif; ?>
<?php endif; ?>
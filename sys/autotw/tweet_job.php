<?php
chdir(__DIR__);
require_once '../core/functions.php';
use Abraham\TwitterOAuth\TwitterOAuth;

if ($mysqli = db_conect()) {
    $query_data = array('settime' => date('Y-m-d H:i'));
    $query_escapes = db_query_escape($mysqli, $query_data);
    echo $query_escapes['settime']."<br/>";
    $res = $mysqli->query(
        "SELECT auto_tweet.id, auto_tweet.text,
                auto_tweet.is_hist, auto_tweet.settime,
                users.access_token, users.access_token_secret
        FROM auto_tweet
            INNER JOIN users ON auto_tweet.at_id = users.id
        WHERE settime LIKE '${query_escapes['settime']}%'"
    );
    echo $mysqli->error."<br/>";
    if ($res->num_rows !== 0) {
        $settwlist = db_resload_select($res);
        foreach ($settwlist as $record) {
            $connection = new TwitterOAuth(
                TWITTER_API_KEY, TWITTER_API_SECRET,
                $record['access_token'], $record['access_token_secret']
            );
            $res = $connection->post(
                "statuses/update", array("status" => $record['text'])
            );
            if ($record['is_hist'] === 0) {
                $mysqli->query("DELETE FROM auto_tweet WHERE id = ${record['id']}");
            }
            echo $record['settime']." | ".$record['text']."|".$record['access_token']."<br/>";
        }
    }
}
$mysqli->close();
?>
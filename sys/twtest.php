<?php

// OAuthライブラリの読み込み
require_once 'core/functions.php';
use Abraham\TwitterOAuth\TwitterOAuth;

//認証情報４つ
$consumerKey = TWITTER_API_KEY;
$consumerSecret = TWITTER_API_SECRET;
$accessToken = "3139485001-yJugM4EgHArPaaGtHJida5ZvqtcTnWTUFg2gPSt";
$accessTokenSecret = "yFp7D9Vy0HfFkpB5gMCzt3iR2AAH4PSo8I7kbI2IQmLes";

//接続
$connection = new TwitterOAuth($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);

//ツイート
$res = $connection->post("statuses/update", array("status" => "テストメッセージ"));

//レスポンス確認
var_dump($res);

?>
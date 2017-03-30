<?php
session_start();
require __DIR__ . '/../twitteroauth-master/autoload.php';

use Abraham\TwitterOAuth\TwitterOAuth;
$post_date = file_get_contents("php://input");
$datos = json_decode($post_date);

$_SESSION['status']=$datos->status;
$_SESSION['media']=$datos->media;

$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);//, $access_token, $access_token_secret);
$request_token = $connection->oauth('oauth/request_token');//, ['oauth_callback' => OAUTH_CALLBACK]);
syslog(LOG_INFO, "Tras request token:" .$request_token['oauth_token']);
switch ($connection->getLastHttpCode()) {
    case 200:
        /* Save temporary credentials to session. */
        $_SESSION['oauth_token'] = $request_token['oauth_token'];
        $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
        /* Build authorize URL and redirect user to Twitter. */
        $url = $connection->url('oauth/authorize', ['oauth_token' => $request_token['oauth_token']]);
        break;
    default:
        /* Show notification if something went wrong. */
        syslog(LOG_ERR, 'Could not connect to Twitter. Refresh the page or try again later.');
        print "ERROR";
        $url="";
}
syslog(LOG_INFO, "Tras oauth/authorize:" .$url);
//header("Location: ".$url);
print json_encode($url);

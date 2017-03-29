<?php
session_start();

require __DIR__ . '/../twitteroauth-master/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;
$request_token['oauth_token'] = $_SESSION['oauth_token'];
$request_token['oauth_token_secret'] = $_SESSION['oauth_token_secret'];
/* If denied, bail. */
if (isset($_REQUEST['denied'])) {
//    header('Location: /myImage/app');
    session_destroy();
    exit('Permission was denied. Please start over.');
}
/* If the oauth_token is not what we expect, bail. */
if (isset($_REQUEST['oauth_token']) && $request_token['oauth_token'] !== $_REQUEST['oauth_token']) {
    //$_SESSION['oauth_status'] = 'oldtoken';
    session_destroy();
    //header('Location: /myImage/app');
    exit;
}
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET,  $request_token['oauth_token'], $request_token['oauth_token_secret']);

syslog(LOG_INFO,"TOKEN:". $_GET['oauth_token']);
//syslog(LOG_INFO,"TOKEN SECRET:". $_GET['oauth_token_secret']);
syslog(LOG_INFO,"VERI:". $_GET['oauth_verifier']);
$access_token = $connection->oauth("oauth/access_token", ["oauth_verifier" => $_REQUEST['oauth_verifier']]);

switch ($connection->getLastHttpCode()) {
    case 200:
        /* Create a TwitterOauth object with consumer/user tokens. */
        $connectionWrite = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);

        $status = $_SESSION['status'];
        $media = $_SESSION['media'];

        $media1 = $connectionWrite->upload('media/upload', ['media' => $media]);
        //$media2 = $connection->upload('media/upload', ['media' => '/path/to/file/kitten2.jpg']);

        $parameters = [ 
            'status' => $status,
            'media_ids' => implode(',', [$media1->media_id_string])
        ];
        $result = $connectionWrite->post('statuses/update', $parameters);
        //print json_encode($result);
        syslog(LOG_INFO, "Tweet escrito y volviendo a la app");
        //header('Location: /myImage/app');    
        break;
    default:
        print "ERROR:OAUTH ERRÓNEO";
}
session_destroy();
?>
<html>
<head>End of proccess</head>
<body onLoad="setTimeout('window.close()',5000)">
This is a popup window,<br>
which may not open on any random system thanks to popup blockers<br>
on the chance that it does open, I want to close it 5 seconds later
</body>
</html>

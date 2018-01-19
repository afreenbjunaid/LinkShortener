<?php
date_default_timezone_set('Asia/Singapore');
session_start();
require_once 'classes/shortener.php';

$host = $_SERVER['HTTP_HOST'];
$uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$pg = 'index.php';

$short = new shortener();

if(isset($_POST['url'])) {
    
    $url = $_POST['url'];
    
    if($shortCode = $short->createShort($url)) {
        
        $_SESSION['feedback'] = '<center> <h2 class="result"> Generated! Your short URL is: </h2>
                                    <a href = "http://'.$host.$uri.'/'.$shortCode.'"
                                    style="margin:5px auto; text-align:center; display:block;" class="shortLink">
                                    http://'.$host.$uri.'/'.$shortCode.'</a> <br>
                                    <a href = "http://'.$host.$uri.'/'.$shortCode.'/stats"
                                    style="margin:5px auto; text-align:center; display:block;" class="stats">
                                    Show URL Stats...</a></center>';
    } else {
        // Problem encountered
        $_SESSION['feedback'] = 'There was a problem! Please enter a valid URL.';
    }
}

// Redirect to index page
header("Location: http://".$host.$uri."/".$pg);

?>

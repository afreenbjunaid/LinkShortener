<?php

// Receive long URL, create short code, display Short URL and Stats

date_default_timezone_set('Asia/Singapore');
session_start();
require_once 'classes/shortener.php';

$host = $_SERVER['HTTP_HOST'];
$uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$pg = 'index.php';

$short = new shortener();

// If long URL is submitted
if(isset($_POST['url'])) {
    
    $url = $_POST['url'];
    
    // If short code is generated and returned
    if($shortCode = $short->createShort($url)) {
        
        // Display short URL and stats links
        $_SESSION['feedback'] = '<center> <h2 class="result"> Generated! Your short URL is: </h2>
                                    <a href = "http://'.$host.$uri.'/redirect.php?code='.$shortCode.'"
                                    style="margin:5px auto; text-align:center; display:block;" class="shortLink">
                                    http://'.$host.$uri.'/'.$shortCode.'</a> <br>
                                    <a href = "http://'.$host.$uri.'/urlStats.php?short='.$shortCode.'&stats=stats"
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

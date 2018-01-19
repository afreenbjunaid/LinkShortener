<?php

// Redirect the short URL to the long URL destination

date_default_timezone_set('Asia/Singapore');
require_once 'classes/shortener.php';

$host = $_SERVER['HTTP_HOST'];
$uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$pg = 'index.php';

$s = new shortener();

// On click of short URL link, receive the passed short code
if(isset($_GET['code'])) {
    
    // Get short URL code
    $code = $_GET['code'];
    
    // Retrieve and redirect to the long URL destination
    if($destURL = $s->getURL($code)) {
        
        header ('Location:' . $destURL);
        die();
        
    }
    
} else { // If no code is received redirect to index page
    header("Location: http://".$host.$uri."/".$pg);
}

?>
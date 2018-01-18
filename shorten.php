<?php

session_start();
require_once 'classes/shortener.php';

$short = new shortener();

if(isset($_POST['url'])) {
    $url = $_POST['url'];
    
    if($shortCode = $short->createShort($url)) {
        echo $shortCode;
    } else {
        // Problem encountered
    }
}

?>
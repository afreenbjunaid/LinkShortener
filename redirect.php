<?php

require_once 'classes/shortener.php';

$host = $_SERVER['HTTP_HOST'];
$uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$pg = 'index.php';

$s = new shortener();

if(isset($_GET['code'])) {
    
    // Get short URL code
    $code = $_GET['code'];
    
    // Redirect to the long URL
    if($destURL = $s->getURL($code)) {
        
        header ('Location:' . $destURL);
        die();
        
    }
    
} else {
    header("Location: http://".$host.$uri."/".$pg);
}

?>
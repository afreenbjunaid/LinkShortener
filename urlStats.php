<?php

date_default_timezone_set('Asia/Singapore');
session_start();

require_once 'classes/dbConnection.php';

$host = $_SERVER['HTTP_HOST'];
$uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$pg = 'index.php';

$val = 'stats';

if((isset($_GET['short'])) && (isset($_GET['stats']))) {
    
    header("Location: http://".$host.$uri."/".$pg);
    
    $short = $_GET['short'];
    $short = trim($short);
    $short = pg_escape_string($short);
    
    $stats = $_GET['stats'];
    $stats = trim($stats);
    $stats = pg_escape_string($stats);
    
    
    if(strcmp($stats, $val) == 0) {
        
        header("Location: http://".$host.$uri."/".$pg);
        
        // Queries to retrieve summary and access logs of short URL
        $dbcon = new dbConnection();
        $dbcon->openDBCon();
        
        $IDQuery = 'SELECT * FROM "public"."url_Table" WHERE "shortURL" = \'' . $short . '\';';
        $refURLIDRes = pg_query($IDQuery);
        $queryURLRes = pg_fetch_object($refURLIDRes);
        
        $refURLID = $queryURLRes->ID;
        $creatorIP = $queryURLRes->creatorIP;
        $createdOn = $queryURLRes->createdOn;
        $lastVisited = $queryURLRes->lastVisited;
        
        // Query the access logs
        $clicksQuery = 'SELECT * FROM "public"."stats_Table" WHERE "URLID" = ' . $refURLID .';';
        $clicksRes = pg_query($clicksQuery);
        
        if(pg_num_rows($clicksRes) > 0) {
            $clicks = pg_num_rows($clicksRes);
        } else {
            $clicks = 'URL not visited yet!';
            $lastVisited = 'No access logs available!';
        }
        
        // Feedback the access log results for display
        $feedback =
        '<table id="summaryTable">
                    <tr> <th><u>Summary for short URL</u> : http://'.$host.$uri.'/'.$short.' </th></tr>
                    <tr><td> <pre>Total Visits         : '.$clicks.'</pre></td></tr>
                    <tr><td> <pre>Created On           : '.$createdOn.'</pre> </td></tr>
                    <tr><td> <pre>Created By User IP   : '.$creatorIP.'</pre> </td></tr>
                    <tr><td> <pre>Last Visited On      : '.$lastVisited.'</pre> </td></tr>
                </table>';
        
        // Send feedback to display in index.php
        $_SESSION['feedback'] = $feedback;
        
        $dbcon->closeDBCon();
        
    } else {
        
        header("Location: http://".$host.$uri."/".$pg);
        // If there was any error
        $_SESSION['feedback'] = 'There was a problem! Stats not available at the moment!';
        
    }
    
} else {
    
    header("Location: http://".$host.$uri."/".$pg);
    // There was a problem
    $_SESSION['feedback'] = 'There was a problem! Stats not available at the moment!';
    
}

?>
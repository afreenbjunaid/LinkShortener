<?php

// Redirect the Stats link to retrieve and display stats

date_default_timezone_set('Asia/Singapore');
session_start();

require_once 'classes/dbConnection.php';

$host = $_SERVER['HTTP_HOST'];
$uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$pg = 'index.php';

$val = 'stats';
$page = '';

// Set page numbers when access logs are displayed in stats
if (isset($_GET['page'])) { $page  = $_GET['page']; } else { $page=1; };

// On click of stats link get the passed short and stats codes 
if((isset($_GET['short'])) && (isset($_GET['stats']))) {
    
    header("Location: http://".$host.$uri."/".$pg);
    
    $short = $_GET['short'];
    $short = trim($short);
    $short = pg_escape_string($short);
    
    $stats = $_GET['stats'];
    $stats = trim($stats);
    $stats = pg_escape_string($stats);
    
    // If stats code passed matches 'stats', proceed 
    if(strcmp($stats, $val) == 0) {
        
        header("Location: http://".$host.$uri."/".$pg);
        
        // Queries to retrieve summary and access logs of short URL
        $dbcon = new dbConnection();
        $dbcon->openDBCon();
        
        // Get summary details of short URL
        $IDQuery = 'SELECT * FROM "public"."url_Table" WHERE "shortURL" = \'' . $short . '\';';
        $refURLIDRes = pg_query($IDQuery);
        $queryURLRes = pg_fetch_object($refURLIDRes);
        
        $refURLID = $queryURLRes->ID;
        $creatorIP = $queryURLRes->creatorIP;
        $createdOn = $queryURLRes->createdOn;
        $lastVisited = $queryURLRes->lastVisited;
        
        // Gather access log values for pagination
        $recordsPerPg = 10; // Number of records per page
        $startPg = ($page-1) * $recordsPerPg;
        
        // Get total number of visits for the short URL
        $recordsCount = pg_query('SELECT "ID" FROM "public"."stats_Table" WHERE "URLID" = ' . $refURLID . ';');
        
        if(pg_num_rows($recordsCount) > 0) {
            $clicks = pg_num_rows($recordsCount);
        } else {
            $clicks = 'URL not visited yet!';
            $lastVisited = 'No access logs available!';
        }
        
        $totRecords = $clicks;
        $totPgs = ceil($totRecords / $recordsPerPg);
        
        // Get the access log records
        $clicksQuery = 'SELECT * FROM "public"."stats_Table" WHERE "URLID" = ' . $refURLID .
        ' LIMIT ' . $recordsPerPg . ' OFFSET ' . $startPg .';';
        $clicksRes = pg_query($clicksQuery);
        
        // Feedback the access log results for display
        $feedback =
        '<table id="summaryTable">
                    <tr> <th><u>Summary for short URL</u> : http://'.$host.$uri.'/'.$short.' </th></tr>
                    <tr><td> <pre>Total Visits         : '.$clicks.'</pre></td></tr>
                    <tr><td> <pre>Created On           : '.$createdOn.'</pre> </td></tr>
                    <tr><td> <pre>Created By User IP   : '.$creatorIP.'</pre> </td></tr>
                    <tr><td> <pre>Last Visited On      : '.$lastVisited.'</pre> </td></tr>
                </table>
                <div class="accessLog" style="overflow-x:auto;"><p><b><u> Access Log </u></b></p></div>
                <table width=100% id="logTable">
                <tr>
                    <th> Visitor IP </th>
                    <th> Visited On </th>
                    <th> Referer URL </th>
                    <th> Visitor Browser </th>
                </tr>';
        
        // Display every record in a new row
        while ($row = pg_fetch_row($clicksRes)) {
            $feedback = $feedback . '<tr>
                        <td>'.$row[2].'</td>
                        <td>'.$row[3].'</td>
                        <td>'.$row[4].'</td>
                        <td>'.$row[5].'</td> </tr>';
        }
        
        $feedback = $feedback . '</table><br><center>~~&nbsp;&nbsp;';
        
        // Display page links
        for ($i=1; $i<=$totPgs; $i++) {
            $feedback = $feedback .
            '<a href="http://'.$host.$uri.'/urlStats.php?page='.$i.'&short='.$short.'&stats='.$stats.'" class="page">
                        '.$i.'</a>&nbsp;&nbsp;';
        }
        
        $feedback = $feedback.'~~</center>';
        
        // Parse final feedback string to display in index.php
        $_SESSION['feedback'] = $feedback;
        
        // Close DB connection
        $dbcon->closeDBCon();
        
    } else {
        
        header("Location: http://".$host.$uri."/".$pg);
        // If stats code is corrupt
        $_SESSION['feedback'] = 'There was a problem! Try Again!';
        
    }
    
} else {
    
    header("Location: http://".$host.$uri."/".$pg);
    // If both short and stats codes are not passed
    $_SESSION['feedback'] = 'There was a problem! Try Again!';
    
}

?>
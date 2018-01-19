<?php

require_once 'classes/dbConnection.php';

class shortener{
    
    public $dbcon;
    protected $alphabet = '23456789bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ-_';
    protected $base = 51;
 
    // Open DB connection
    public function __construct() {
        
        // Initialize DB connection properties
        $this->dbcon = new dbConnection();
        
        // Open a PostgreSQL connection
        $this->dbcon->openDBCon();
        
    }
    
    // Generate short code based on ID
    protected function generateShort($id) {
        
        // Bijective conversion from natural number (ID) to short string
        // Char set includes numbers, large & small alphabets
        // Total 51 chars as "aeiou AEIOU l1 O0" are removed to avoid undesired and ambiguous words
        
        $short = '';
        
        while ($id > 0) {
            $mod = $id % $this->base;
            $short = $this->alphabet[$mod] . $short;
            $id = ($id - $mod) / $this->base;
        }
        return $short;
        
    }
    
    // Create short for URL
    public function createShort($url) {
        $url = trim($url);
        
        if(!filter_var($url, FILTER_VALIDATE_URL)) {
            return '';
        }
        
        $url = pg_escape_string($url);
        
        // Check if URL already exists
        $query = 'SELECT "shortURL" FROM "public"."url_Table" WHERE "longURL" = \'' . $url . '\';';
        $queryRes = pg_query($query);
        
        // If URL does not exist in table
        if(pg_num_rows($queryRes) > 0) {
            
            // Return short code of corresponding URL
            $resCode = pg_fetch_object($queryRes);
            return $resCode->shortURL;
            
        } else {
            
            // Get user IP
            $creatorIP = $this->get_userIP();
            
            // Save url and other details to DB
            $insert = 'INSERT INTO "public"."url_Table" ("longURL", "createdOn", "creatorIP")
                            VALUES (\'' . $url . '\', now(), \''.$creatorIP.'\')
                                RETURNING "ID";';
            $retInsID = pg_query($insert);
            
            $urlID = pg_fetch_object($retInsID);
            
            // Generate short code based on inserted ID
            $shortCode = $this->generateShort($urlID->ID);
            
            //Update the record with the generated short code
            $updateShort = 'UPDATE "public"."url_Table" SET "shortURL" = \'' . $shortCode . '\' WHERE "ID" = ' . $urlID->ID . ';';
            pg_query($updateShort);
            
            return $shortCode;
        }
    }
    
    // To get the long URL destination entered
    public function getURL($shortCode) {
        
        $shortCode = pg_escape_string($shortCode);
        
        $longURLQuery = 'SELECT "longURL" FROM "public"."url_Table" WHERE "shortURL" = \'' . $shortCode . '\';';
        $dest = pg_query($longURLQuery);
        
        // Update last visited timestamp
        $updateLastVisited = 'UPDATE "public"."url_Table" SET "lastVisited" = now() WHERE "shortURL" = \'' . $shortCode . '\' RETURNING "ID";';
        $resultUpdate = pg_query($updateLastVisited);
        $keyID = pg_fetch_object($resultUpdate);
        
        // Update access log in stats table
        // User IP
        $currUserIP = $this->get_userIP();
        
        // Referrer
        $ref = $this->get_referer();
        
        // Browser
        $browser = @$_SERVER[HTTP_USER_AGENT];
        
        $insertLog = 'INSERT INTO "public"."stats_Table" ("URLID", "visitorIP", "visitedOn", "referrerURL", "browserAgent")
                            VALUES (\'' . $keyID->ID . '\', \''.$currUserIP.'\', now(), \''.$ref.'\', \''.$browser.'\');';
        pg_query($insertLog);
        
        if(pg_num_rows($dest) > 0) {
            $retLongURL = pg_fetch_object($dest);
            return $retLongURL->longURL;
        } else {
            return ''; // Default when no value found
        }
        
    }
    
    // Function to get the client ip address
    public function get_userIP() {
        
        $userIP = '';
        
        if ($_SERVER['REMOTE_ADDR'])
            $userIP = $_SERVER['REMOTE_ADDR'];
            else
                $userIP = 'UNKNOWN';
                
                return $userIP;
    }
        
    // Function to get the referer url
    public function get_referer() {
        
        // Get referer info
        if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
            $ref = $_SERVER['HTTP_REFERER'];
        } else {
            $ref = 'No Referer Info/Direct URL Entry';
        }
        
        return $ref;
    }
    
    // Close DB connection
    public function __destruct() {
        
        // Close DB connection
        $this->dbcon->closeDBCon();
    }
    
}
?>
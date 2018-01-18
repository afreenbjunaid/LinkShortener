<?php

require_once 'classes/dbConnection.php';

class shortener{
    
    public $dbcon;
 
    // Open DB connection
    public function __construct() {
        
        // Initialize DB connection properties
        $this->dbcon = new dbConnection();
        
        // Open a PostgreSQL connection
        $this->dbcon->openDBCon();
        
    }
    
    // Generate short code based on ID
    protected function generateShort($id) {
        
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
            // Generate short code based on inserted ID
        }
    }
    
    // To get the long URL destination entered
    public function getURL($shortCode) {
        
    }
    
    // Close DB connection
    public function __destruct() {
        
        // Close DB connection
        $this->dbcon->closeDBCon();
    }
    
}
?>
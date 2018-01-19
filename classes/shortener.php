 <?php
 
 // Generate short code, capture access logs for long URL 
 
 date_default_timezone_set('Asia/Singapore');
 require_once 'classes/dbConnection.php';
 
 class shortener{
     
     public $dbcon;
     // Character set used for creating the short code
     protected $alphabet = '23456789bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ-_';
     protected $base = 51; // Total number of characters used to convert the ID to the base of
     
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
         return $short; // Returns the generated short code
         
     }
     
     // Request to create short for the long URL
     public function createShort($url) {
         
         $url = trim($url);
         
         if(!filter_var($url, FILTER_VALIDATE_URL)) {
             return '';
         }
         
         $url = pg_escape_string($url);
         
         // Check if URL already exists in DB
         $query = 'SELECT "shortURL" FROM "public"."url_Table" WHERE "longURL" = \'' . $url . '\';';
         $queryRes = pg_query($query);
         
         // If URL exists in DB
         if(pg_num_rows($queryRes) > 0) {
             
             $resCode = pg_fetch_object($queryRes);
             return $resCode->shortURL; // Return short code of corresponding long URL
             
         } else { // If URL does not exist in DB, create and add new record to DB
             
             // Get creator user IP address
             $creatorIP = $this->get_userIP();
             
             // Insert into DB: long url, created timestamp and creator IP to DB
             $insert = 'INSERT INTO "public"."url_Table" ("longURL", "createdOn", "creatorIP")
                            VALUES (\'' . $url . '\', now(), \''.$creatorIP.'\')
                                RETURNING "ID";';
             $retInsID = pg_query($insert);
             
             $urlID = pg_fetch_object($retInsID);
             
             // Generate short code based on auto-generated ID
             $shortCode = $this->generateShort($urlID->ID);
             
             //Update the inserted record with the generated short code
             $updateShort = 'UPDATE "public"."url_Table" SET "shortURL" = \'' . $shortCode . '\' WHERE "ID" = ' . $urlID->ID . ';';
             pg_query($updateShort);
             
             return $shortCode; // Return the generated short code
             
         }
                 
     }
     
     // Get long URL destination for redirection on clicking the short URL
     public function getURL($shortCode) {
         
         $shortCode = pg_escape_string($shortCode);
         
         // Get the long URL destination from the DB
         $longURLQuery = 'SELECT "longURL" FROM "public"."url_Table" WHERE "shortURL" = \'' . $shortCode . '\';';
         $dest = pg_query($longURLQuery);
         
         // Update last visited on timestamp in the DB
         $updateLastVisited = 'UPDATE "public"."url_Table" SET "lastVisited" = now() WHERE "shortURL" = \'' . $shortCode . '\' RETURNING "ID";';
         $resultUpdate = pg_query($updateLastVisited);
         $keyID = pg_fetch_object($resultUpdate); 
         
         // Get visitor IP address
         $currUserIP = $this->get_userIP();
         
         // Get Referrer URL
         $ref = $this->get_referer();
         
         // Get Browser Agent
         $browser = @$_SERVER[HTTP_USER_AGENT];
         
         // Update access log in DB
         $insertLog = 'INSERT INTO "public"."stats_Table" ("URLID", "visitorIP", "visitedOn", "referrerURL", "browserAgent")
                            VALUES (\'' . $keyID->ID . '\', \''.$currUserIP.'\', now(), \''.$ref.'\', \''.$browser.'\');';
         pg_query($insertLog);
         
         // If long URL value matches short code
         if(pg_num_rows($dest) > 0) {
             $retLongURL = pg_fetch_object($dest);
             return $retLongURL->longURL; // Return the long URL
         } else {
             return ''; // Return default when no long URL matching the short code is found
         }
         
     }
     
     // Get the client/visitor IP address
     public function get_userIP() {
         
         $userIP = '';
         
         if ($_SERVER['REMOTE_ADDR'])
             $userIP = $_SERVER['REMOTE_ADDR'];
         else
             $userIP = 'UNKNOWN';
                                     
             return $userIP;
     }
     
     // Get the referer url
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

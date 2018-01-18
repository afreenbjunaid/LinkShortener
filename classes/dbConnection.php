<?php

class dbConnection {
    
    protected $hostname = "horton.elephantsql.com";
    protected $username = "wfxfxqkx";
    protected $pwd = "kwxarQMBMlHxtDbYlfWo8ipuA94-aQ7-";
    protected $dbname = "wfxfxqkx";
    public $con;
    
    // Open PostgreSQL connection
    public function openDBCon() {
        $this->con = pg_connect("host=$this->hostname dbname=$this->dbname user=$this->username password=$this->pwd")
        or die ("Could not connect to server!\n");
    }
    
    // Close PostgreSQL connection
    public function closeDBCon() {
        pg_close($this->con);
    }
    
}

?>
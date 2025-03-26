<?php
class Database {
    // Connection details - just hardcoded for simplicity, though much less secure this way...
    private $host = 'dpg-cvi6ee5umphs73cvjeig-a.virginia-postgres.render.com';
    private $db_name = 'quotesdb_ymmy';
    private $username = 'quotes_user';
    private $password = 'deQqEF67MBi1acwQOD6G2Ut14PvofcVx';
    
    // Var to hold connection
    public $conn;

    // Secure connection func.
    public function connect() {
        // Instantiate connection to null
        $this->conn = null;

        // Standard try/catch block for connection
        try {
            $this->conn = new PDO("pgsql:host=$this->host;dbname=$this->db_name", 
                                  $this->username, 
                                  $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }

        // Naturally, return the connection...
        return $this->conn;
    }
}
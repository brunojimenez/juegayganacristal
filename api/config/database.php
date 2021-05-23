<?php
class Database {
   
    // specify your own database credentials
    private $host = "162.241.62.0";
    private $db_name = "juegayg1_juego";
    private $username = "juegayg1";
    private $password = "7MTv9)a(oF1g4F";
    public $conn;
   
    // get the database connection
    public function getConnection() {
   
        $this->conn = null;
   
        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
        } catch (PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
   
        return $this->conn;
    }

}
?>
<?php

// CREATE TABLE `juegayg1_juego`.`tokens` ( 
//`code` VARCHAR(6) NOT NULL , 
// `name` VARCHAR(100) NOT NULL , 
// `rut` VARCHAR(50) NOT NULL , 
// `email` VARCHAR(50) NOT NULL , 
// `bar` VARCHAR(50) NOT NULL , 
// `prize_id` INT NOT NULL , 
// `taken` BOOLEAN NOT NULL , 
// `burned` BOOLEAN NOT NULL , 
// PRIMARY KEY (`code`(6))) ENGINE = InnoDB;

class Token {
  
    // database connection and table name
    private $conn;
    private $table_name = "tokens";
  
    // object properties
    public $code;
    public $name;
    public $rut;
    public $email;
    public $bar;
    public $time_elapsed;
    public $play_errors;
    public $prize;
    public $burned;

    public function __construct($db){
        $this->conn = $db;
    }

    public function import($object) {   
        foreach (get_object_vars($object) as $key => $value) {
            $this->$key = htmlspecialchars(strip_tags($value));
        }
    }

    function select($code) {
        $query = "SELECT code, name, rut, email, bar, prize, time_elapsed, play_errors, burned, updated_at FROM " . $this->table_name;

        if (!empty($code)) {
            $query .= " WHERE code = \"" . $code . "\"";
        }
        
        $query .= " ORDER BY updated_at";

        if ($GLOBALS['debug'] ) echo $query . "\n";
        $this->dbLog("select", $query);
  
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $data = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $record = new stdClass();
            $record->code = $code;
            $record->name = $name;
            $record->rut = $rut;
            $record->email = $email;
            $record->bar = $bar;
            $record->time_elapsed = $time_elapsed;
            $record->play_errors = $play_errors;           
            $record->prize = $prize; // TODO revisar
            $record->burned = $burned;
            $record->updated_at = $updated_at;
            array_push($data,$record);
        }
  
        return $data;
    }

    function readName(){
        
        $query = "SELECT name FROM " . $this->table_name . " WHERE id = ? limit 0,1";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
    
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->name = $row['name'];
    }

    function update() {

        $this->burned = "1";

        $query = "UPDATE " . $this->table_name . " SET"
            . " name = \"" . $this->name . "\""
            . " , rut = \"" . $this->rut . "\""
            . " , email = \"" . $this->email . "\""
            . " , bar = \"" .  $this->bar . "\""
            . " , time_elapsed = \"" .  $this->time_elapsed . "\""
            . " , play_errors = \"" .  $this->play_errors . "\""

            // TODO check asingacion
            // . " , prize = \"" . $this->prize . "\""

            . " , burned = \"1\"" 
            . " , updated_at = now()"
            . " WHERE code = \"" . $this->code . "\"" ;

        if ($GLOBALS['debug'] ) echo $query . "\n";
        $this->dbLog("update", $query);
        
        $stmt = $this->conn->prepare($query);
        
        if ($stmt->execute()) {
            $data = new stdClass();
            $data->status = "OK";
            return $data;
        } else {
            $data = new stdClass();
            $data->status = "NOK";
            return $data;
        }

    }

    function insert() {
        $query = "INSERT INTO " . $this->table_name . "(code, name, rut, email, bar, prize_id, burned)"
            . " VALUES (\"" . $this->name . "\",\"" . $this->rut . "\",\"" . $this->email . "\",\"" .  $this->bar . "\",\"" . $this->prize_id . "\"," . $this->burned . ")"
            . " WHERE code = \"" . $this->code . "\"" ;  
        echo $query;
        //$stmt = $this->conn->prepare($query);
        // $stmt->execute();
    }

    function dbLog($step, $query) {
        $query_log = "INSERT INTO log (step, query, updated_at) VALUES (\"" . $step . "\", \"". htmlspecialchars(strip_tags($query)) . "\" ,now())";
        if ($GLOBALS['debug'] ) echo $query_log . "\n";
        $stmt = $this->conn->prepare($query_log);
        $stmt->execute();
    }

    public static function writeJsonResponse($data) {

        $json = json_encode($data);

        if ($json === false) {
            // Avoid echo of empty string (which is invalid JSON), and
            // JSONify the error message instead:
            $json = json_encode(["jsonError" => json_last_error_msg()]);

            if ($json === false) {
                // This should not happen, but we go all the way now:
                $json = '{"jsonError":"unknown"}';
            }
            // Set HTTP response status code to: 500 - Internal Server Error
            http_response_code(500);
        }

        header('Content-Type: application/json');
        echo $json;
        exit();
    }
 
}
?>
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
    private $log;
  
    // object properties
    public $code;
    public $name;
    public $rut;
    public $email;
    public $bar;
    public $time_elapsed;
    public $won;
    public $lost;
    public $burned;

    public function __construct($db){
        $this->conn = $db;
        $this->log = new DbLog($db);
    }

    public function import($object) {   
        foreach (get_object_vars($object) as $key => $value) {
            $this->$key = htmlspecialchars(strip_tags($value));
        }
    }

    function check($code) {
        $query = "SELECT code, burned, updated_at FROM " . $this->table_name;

        if (!empty($code)) {
            $query .= " WHERE code = \"" . $code . "\"";
        }
        
        $query .= " ORDER BY updated_at";

        if ($GLOBALS['debug'] ) echo $query . "\n";
        $this->log->info("select", $query);
  
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $data = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $record = new stdClass();
            $record->code = $code;
            $record->burned = $burned;
            $record->updated_at = $updated_at;
            array_push($data,$record);
        }
  
        return $data;
    }

    function select($code) {
        $query = "SELECT code, name, rut, email, bar, time_elapsed, won, lost, burned, updated_at FROM " . $this->table_name;

        if (!empty($code)) {
            $query .= " WHERE code = \"" . $code . "\"";
        }
        
        $query .= " ORDER BY updated_at";

        if ($GLOBALS['debug'] ) echo "query=" . $query . "\n";
        $this->log->info("select", $query);
  
        $stmt = $this->conn->prepare($query);
        if ($GLOBALS['debug'] ) echo "errorInfo=" . $this->conn->errorInfo() . "\n";
        
        $status = $stmt->execute();
        if ($GLOBALS['debug'] ) echo "status=" . ($status ? 'true' : 'false') . "\n";

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
            $record->won = $won;
            $record->lost = $lost;
            $record->burned = $burned;
            $record->updated_at = $updated_at;
            array_push($data,$record);
        }
  
        return $data;
    }

    function update() {

        $this->burned = "1";

        $query = "UPDATE " . $this->table_name . " SET"
            . " name = \"" . $this->name . "\""
            . " , rut = \"" . $this->rut . "\""
            . " , email = \"" . $this->email . "\""
            . " , bar = \"" .  $this->bar . "\""
            . " , time_elapsed = \"" .  $this->time_elapsed . "\""
            . " , won = \"" .  $this->won . "\""
            . " , lost = \"" .  $this->lost . "\""
            . " , burned = \"1\"" 
            . " , updated_at = now()"
            . " WHERE code = \"" . $this->code . "\"" ;
            // SECURE TEST
            //. " AND burned = \"0\""  


        if ($GLOBALS['debug'] ) echo "query=" . $query . "\n";
        $this->log->info("update", $query);
        
        $stmt = $this->conn->prepare($query);
        
        $status = $stmt->execute();
        if ($GLOBALS['debug'] ) echo "status=" . ($status ? 'true' : 'false') . "\n";
        
        if ($status) {
            http_response_code(200);
            $data = new stdClass();
            $data->status = "OK";
            return $data;
        } else {
            http_response_code(500);
            $data = new stdClass();
            $data->status = "NOK";
            $data->message = implode(",", $stmt->errorInfo());
            return $data;
        }

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
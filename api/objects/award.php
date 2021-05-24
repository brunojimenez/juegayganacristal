<?php

class Award {
  
    // database connection and table name
    private $conn;
    private $table_name = "awards";
    private $log;
  
    // object properties
    public $id;
    public $token_id;
    public $name;
    public $bar;
    public $wins;
    public $updated_at;

    public function __construct($db){
        $this->conn = $db;
        $this->log = new DbLog($db);
    }

    /*
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
        $query = "SELECT code, name, rut, email, bar, time_elapsed, wins, burned, updated_at FROM " . $this->table_name;

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
            $record->name = $name;
            $record->rut = $rut;
            $record->email = $email;
            $record->bar = $bar;
            $record->time_elapsed = $time_elapsed;
            $record->wins = $wins;
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
            . " , wins = \"" .  $this->wins . "\""
            . " , burned = \"1\"" 
            . " , updated_at = now()"
            . " WHERE code = \"" . $this->code . "\"" ;
            // SECURE TEST
            //. " AND burned = \"0\""  


        if ($GLOBALS['debug'] ) echo $query . "\n";
        $this->log->info("update", $query);
        
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
    */

    function assign($bar, $code, $wins) {

        $query = "SELECT id, name FROM " . $this->table_name 
            . " WHERE bar = \"" . $bar ."\""
            . "   AND token_id = \"\" "
            . "   AND wins <= " . $wins 
            . " ORDER BY wins DESC LIMIT 1";
        
        if ($GLOBALS['debug'] ) echo $query . "\n";
        $this->log->info("assign", $query);

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $this->id = $id;
            $this->name = $name;
        }
        if ($GLOBALS['debug'] ) echo "id=" . $this->id . "\n";
        if ($GLOBALS['debug'] ) echo "name=" . $this->name . "\n";

        if (is_null($this->id)) {
            return "";
        }

        $query = "UPDATE " . $this->table_name . " SET token_id = \"" . $code . "\" WHERE id = \"" . $this->id . "\"";
        if ($GLOBALS['debug'] ) echo $query . "\n";
        $this->log->info("assign", $query);

        $stmt = $this->conn->prepare($query);

        if ($stmt->execute()) {
            return $this->name;
        } else {
            return "";
        }
    }
 
}
?>
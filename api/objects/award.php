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
    public $won;
    public $updated_at;

    public function __construct($db){
        $this->conn = $db;
        $this->log = new DbLog($db);
    }

    function report() {
        $query = "SELECT a.id, a.name award, a.bar, t.code, t.name, t.rut, t.email, t.won, t.lost, t.time_elapsed, a.updated_at"
            . " FROM awards a"
            . " INNER JOIN tokens t ON a.token_id = t.code";
        
        $query .= " ORDER BY a.updated_at";

        if ($GLOBALS['debug'] ) echo $query . "\n";
  
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $data = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $record = new stdClass();
            $record->id = $id;
            $record->award = $award;
            $record->bar = $bar;
            $record->code = $code;
            $record->name = $name;
            $record->rut = $rut;
            $record->email = $email;
            $record->won = $won;
            $record->lost = $lost;
            $record->time_elapsed = $time_elapsed;
            $record->updated_at = $updated_at;
            array_push($data,$record);
        }
  
        return $data;
    }

    /*
    function update() {

        $this->burned = "1";

        $query = "UPDATE " . $this->table_name . " SET"
            . " name = \"" . $this->name . "\""
            . " , rut = \"" . $this->rut . "\""
            . " , email = \"" . $this->email . "\""
            . " , bar = \"" .  $this->bar . "\""
            . " , time_elapsed = \"" .  $this->time_elapsed . "\""
            . " , won = \"" .  $this->won . "\""
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

    function assign($data, $bar, $code, $won, $lost) {

        $query = "SELECT id, name FROM " . $this->table_name 
            . " WHERE bar = \"" . $bar ."\""
            . "   AND token_id = \"\" "
            . "   AND won <= " . $won
            . "   AND lost >= " . $lost 
            . " ORDER BY won DESC "
            . " , lost ASC "
            . " LIMIT 1";
        
        if ($GLOBALS['debug'] ) echo $query . "\n";
        $this->log->info("assign", $query);

        $stmt = $this->conn->prepare($query);

        if (!$stmt->execute()) {
            http_response_code(200);
            $data->status = "NOK";
            $data->message = implode(",", $stmt->errorInfo());
            $data->award = "";
            return;
        }

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $this->id = $id;
            $this->name = $name;
        }

        if (is_null($this->id)) {
            http_response_code(200);
            $data->status = "OK";
            $data->message = "Puntaje insuficiente o no hay premios para asignar.";
            $data->award = "";
            return;
        }

        $query = "UPDATE " . $this->table_name . " SET token_id = \"" . $code . "\", updated_at = now() WHERE id = \"" . $this->id . "\"";
        if ($GLOBALS['debug'] ) echo $query . "\n";
        $this->log->info("assign", $query);

        $stmt = $this->conn->prepare($query);
        
        if ($stmt->execute()) {
            http_response_code(200);
            $data->status = "OK";
            $data->message = "";
            $data->award = $this->name;
        } else {
            http_response_code(200);
            $data->status = "NOK";
            $data->message = implode(",", $stmt->errorInfo());
            $data->award = "";
        }
    }

    public static function writeTableResponse($data) {
        echo "<table class=\"table table-striped table-hover table-sm\">";
        echo "<thead class=\"thead-dark\">";
        echo "<tr>";
        echo "<th scope=\"col\">ID</th>";
        echo "<th scope=\"col\">PREMIO</th>";
        echo "<th scope=\"col\">BAR</th>";
        echo "<th scope=\"col\">CÓDIGO</th>";
        echo "<th scope=\"col\">NOMBRE</th>";
        echo "<th scope=\"col\">RUT</th>";
        echo "<th scope=\"col\">CORREO</th>";
        echo "<th scope=\"col\">ACIERTOS</th>";
        echo "<th scope=\"col\">ERRORES</th>";
        echo "<th scope=\"col\">TIEMPO (s)</th>";
        echo "<th scope=\"col\">FECHA ACTUALIACIÓN</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        foreach ($data as &$row) {
            echo "<tr>";
            echo "<td>" . $row->id . "</td>";
            echo "<td>" . $row->award . "</td>";
            echo "<td>" . $row->bar . "</td>";
            echo "<td>" . $row->code . "</td>";
            echo "<td>" . $row->name . "</td>";
            echo "<td>" . $row->rut . "</td>";
            echo "<td>" . $row->email . "</td>";
            echo "<td>" . $row->won . "</td>";
            echo "<td>" . $row->lost . "</td>";
            echo "<td>" . $row->time_elapsed . "</td>";
            echo "<td>" . $row->updated_at . "</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        exit();
    }
 
}
?>
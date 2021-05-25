<?php

class DbLog {
  
    // database connection and table name
    private $conn;
    private $table_name = "log";
  
    // object properties
    public $step;
    public $query;
    public $updated_at;

    public function __construct($db){
        $this->conn = $db;
    }

    function info($step, $query) {
        // add return
        $query .= "\n";

        $query_log = "INSERT INTO log (step, query, updated_at) VALUES (\"" . $step . "\", \"". htmlspecialchars(strip_tags($query)) . "\" ,now())";
        $stmt = $this->conn->prepare($query_log);
        $stmt->execute();
    }

    function select() {
        $query = "SELECT step, query, updated_at FROM " . $this->table_name . " ORDER BY updated_at DESC LIMIT 15";

        if ($GLOBALS['debug'] ) echo $query . "\n";
  
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $data = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $record = new stdClass();
            $record->step = $step;
            $record->query = $query;
            $record->updated_at = $updated_at;
            array_push($data,$record);
        }
  
        return $data;
    }

    public static function writeTableResponse($data) {
        echo "<table class=\"table table-striped table-hover table-sm\">";
        echo "<thead class=\"thead-dark\">";
        echo "<tr>";
        echo "<th scope=\"col\">PASO</th>";
        echo "<th scope=\"col\">CONSULTA</th>";
        echo "<th scope=\"col\">FECHA ACTUALIZACIÓN</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        foreach ($data as &$row) {
            echo "<tr>";
            echo "<td>" . $row->step. "</td>";
            echo "<td>" . $row->query. "</td>";
            echo "<td>" . $row->updated_at. "</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        exit();
    }
 
}
?>
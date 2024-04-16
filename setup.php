<?php

    class Database{

        private $hostname;
        private $username;
        private $password;
        private $database;
        private $connection;

        function __construct($hostname, $username, $password, $database){

            #server connection

            $this->hostname = $hostname;
            $this->username = $username;
            $this->password = $password;
            $this->database = $database;

            $this->connection = new mysqli($this->hostname, $this->username, $this->password, $this->database);
            
            if ($this->connection -> connect_error) {
                die("Connection failed: " . $connection -> connect_error);
            }else{
                echo "<script>console.log('Connection granted')</script>";
            }

        }
        #display tables
        function displayTables($table, $addSQL){
            if(!is_array($table)){
                $tables = [$table];
            }else{
                $tables = $table;
            }
            for($i=0;$i<count($tables);$i++){
    
                $sql = "SELECT * FROM ".$tables[$i].' '.$addSQL;
                $result = $this->connection->query($sql);
                $result2 = $this->connection->query($sql) ->fetch_assoc();
    
                echo "<table><tr>";
                foreach ($result2 as $key => $value) {
                    echo "<th>".$key."</th>";
                }
                echo "</tr>";
                while($row = $result->fetch_array(MYSQLI_NUM))
                {   
                    echo "<tr>";
                    for($j=0;$j < count($row);$j++)
                    {
                        echo "<td>".$row[$j]."</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
            }
        }
        function generateForm($table){
        $result = $this->connection->query("DESCRIBE $table");
        $columns = [];
        while ($row = $result->fetch_assoc()) {
            $columns[] = $row['Field'];
        }
        
        $form = "<form method='post'>";
        foreach ($columns as $column) {
            if ($column != 'id') {
                $form .= "<label for='$column'>$column:</label>";
                $form .= "<input type='text' id='$column' name='$column'><br>";
            }
        }
        $form .= "<input type='submit' value='Submit'>";
        $form .= "</form>";
        
        echo $form;
        }
        function close(){
            mysqli_close($this->connection);
        }
    }
?>

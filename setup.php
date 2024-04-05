<?php

class Database{

    private $hostname;
    private $username;
    private $password;
    private $database;
        function __construct($hostname, $username){

            #server connection

            $this->hostname = $hostname;
            $this->username = $username;
            $this->hostname = $hostname;
            $this->hostname = $hostname;

            $connection =  new mysqli($this->hostname, $username, $password, $database);

            if ($connection -> connect_error) {
                die("Connection failed: " . $connection -> connect_error);
            }else{
                echo "<script>console.log('Connection granted')</script>";
            }

            #display tables
            function displayTables($table, $connection, $addSQL){
                if(!is_array($table)){
                    $tables = [$table];
                }else{
                    $tables = $table;
                }
                for($i=0;$i<count($tables);$i++){
        
                    $sql = "SELECT * FROM ".$tables[$i].' '.$addSQL;
                    $result = $connection->query($sql);
                    $result2 = $connection->query($sql) ->fetch_assoc();
        
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
        }
    }
?>

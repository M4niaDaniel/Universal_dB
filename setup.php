<?php

    #display function
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

?>
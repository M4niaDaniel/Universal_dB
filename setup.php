<?php

    #server conneection
    $hostname = "localhost";
    $username = "root";
    $password = "";
    $database = "finanse_2p2";

    $connection =  new mysqli($hostname, $username, $password, $database);

    if ($connection -> connect_error) {
        die("Connection failed: " . $connection -> connect_error);
    }else{
        echo "<script>console.log('Connection granted')</script>";
    }

    #display function
    function displayTables($tables, $connection){
        for($i=0;$i<count($tables);$i++){

            $sql = "SELECT * FROM ".$tables[$i];
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
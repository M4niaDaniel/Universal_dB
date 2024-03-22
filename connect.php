<?php
    
    #server connection
    $hostname = "localhost";
    $username = "root";
    $password = "";
    $database = "1_gb_of_pure_data";

    $connection =  new mysqli($hostname, $username, $password, $database);

    if ($connection -> connect_error) {
        die("Connection failed: " . $connection -> connect_error);
    }else{
        echo "<script>console.log('Connection granted')</script>";
    }

?>
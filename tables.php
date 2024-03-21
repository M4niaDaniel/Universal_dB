<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <title>Tables</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <?php
            include "connect.php";
            include "setup.php";

            /*$tables = [
                #table1,
                #table2
            ];
            #displayTables($tables, $connection);
            if(isset($_GET["table"])){
                displayTables($_GET["table"], $connection);
            }*/
            mysqli_close($connection);
        ?>
    </body>
</html>
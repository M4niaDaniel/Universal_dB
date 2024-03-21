<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <title>Tables</title>
        <link rel="stylesheet" href="style.css">
        <?php include "setup.php" ?>
    </head>
    <body>
        <?php
            displayTables($tables, $connection);
        ?>
    </body>
</html>
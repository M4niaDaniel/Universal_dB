<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <title>Tables</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <?php
            include "setup.php";

            $dataBase1 = new Database('localhost', 'root', '', '1gb_of_pure_data');
            $tables = [
                #"table1",
                #"table2",
            ];
            $dataBase1->displayTables('user_details', '');
            $dataBase1->generateForm('user_details');
            $dataBase1->close();
        ?>
    </body>
</html>
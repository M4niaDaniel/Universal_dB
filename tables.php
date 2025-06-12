<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <title>Tables</title>
        <link rel="stylesheet" href="style.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    </head>
    <body>
        <?php
            include "setup.php";

            $dataBase1 = new Database('localhost', 'root', '', '1gb_of_pure_data');
            $tables = [
                #"table1",
                #"table2",
            ];
            if (!isset($_GET['edit'], $_GET['id'])) {
                $dataBase1->displayTables('user_details');
            }
            $dataBase1->handleRequest();
            //$dataBase1->generateForm('user_details');
            $dataBase1->close();
        ?>
    </body>
</html>
<?php
class Database {

    private $hostname;
    private $username;
    private $password;
    private $database;
    private $connection;

    function __construct($hostname, $username, $password, $database) {

        // Initialize database connection details
        $this->hostname = $hostname;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;

        // Establish connection to the database
        $this->connection = new mysqli($this->hostname, $this->username, $this->password, $this->database);
        
        // Check connection
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        } else {
            echo "<script>console.log('Connection granted')</script>";
        }
    }

    // Function to display tables
    function displayTables($table, $addSQL) {
        $tables = is_array($table) ? $table : [$table];
        
        foreach ($tables as $tbl) {
            $sql = "SELECT * FROM " . $tbl . ' ' . $addSQL;
            $result = $this->connection->query($sql);

            if (!$result) {
                die("Query failed: " . $this->connection->error);
            }

            $result2 = $result->fetch_assoc();
            
            if (!$result2) {
                die("Failed to fetch data: " . $this->connection->error);
            }

            echo "<table><tr>";
            foreach ($result2 as $key => $value) {
                echo "<th>" . $key . "</th>";
            }
            echo "</tr>";

            $result->data_seek(0); // Reset result pointer
            while ($row = $result->fetch_array(MYSQLI_NUM)) {   
                echo "<tr>";
                foreach ($row as $cell) {
                    echo "<td>" . $cell . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
    }

    // Function to generate HTML form based on table structure
    public function generateForm($table) {
        // Check if form has been submitted
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->handleFormSubmission($table);
        }

        // Always display the form
        $this->displayForm($table);
    }

    // Function to handle form submission
    private function handleFormSubmission($table) {
        // Output received POST data for debugging
        echo "Previous Submit: <pre>";
        print_r($_POST);
        echo "</pre>";

        // Prepare an insert query
        $columns = [];
        $values = [];
        foreach ($_POST as $key => $value) {
            $columns[] = $key;
            $values[] = "'" . $this->connection->real_escape_string($value) . "'";
        }

        $sql = "INSERT INTO $table (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $values) . ")";
        if ($this->connection->query($sql) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $this->connection->error;
        }
    }

    // Function to display the form
    private function displayForm($table) {
        // Describe the table to get column details
        $result = $this->connection->query("DESCRIBE $table");

        // Check for query execution success
        if (!$result) {
            die("Query failed: " . $this->connection->error);
        }

        $columns = [];
        while ($row = $result->fetch_assoc()) {
            $columns[] = $row;
        }

        // Check if columns were fetched correctly
        if (empty($columns)) {
            die("No columns found or failed to fetch columns from the table.");
        }

        // Start building the form
        $form = "<form method='post' action='" . $_SERVER['PHP_SELF'] . "'>";
        foreach ($columns as $column) {
            // Skip columns that are keys
            if ($column['Key'] == '') {
                $form .= "<label for='{$column['Field']}'>{$column['Field']}:</label>";
                $form .= "<input type='text' id='{$column['Field']}' name='{$column['Field']}'><br>";
            }
        }
        $form .= "<input type='submit' value='Submit'>";
        $form .= "</form>";

        // Output the form
        echo $form;
    }

    // Function to close the database connection
    function close() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
}

?>

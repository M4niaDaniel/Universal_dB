<?php
class Database {
    private $hostname;
    private $username;
    private $password;
    private $database;
    private $connection;

    function __construct($hostname, $username, $password, $database) {
        $this->hostname = $hostname;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;

        $this->connection = new mysqli($hostname, $username, $password, $database);

        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        } else {
            echo "<script>console.log('Connection granted')</script>";
        }
    }

    public function handleRequest() {
        if (isset($_GET['delete'], $_GET['id'])) {
            $this->deleteRecord($_GET['delete'], $_GET['id']);
        }

        if (isset($_GET['edit'], $_GET['id'])) {
            $this->editRecord($_GET['edit'], $_GET['id']);
            exit; // Stop other content from rendering
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['__edit_submit'])) {
                $this->updateRecord($_POST['__edit_table'], $_POST['__edit_id'], $_POST);
            } elseif (!empty($_POST)) {
                $this->handleFormSubmission($_POST['__form_table'] ?? '');
            }
        }
    }

    function displayTables($table, $addSQL = '') {
        $tables = is_array($table) ? $table : [$table];

        foreach ($tables as $tbl) {
            $sql = "SELECT * FROM " . $tbl . ' ' . $addSQL;
            $result = $this->connection->query($sql);

            if (!$result) {
                die("Query failed: " . $this->connection->error);
            }

            $result2 = $result->fetch_assoc();
            if (!$result2) {
                echo "<p>No data found in table '$tbl'.</p>";
                continue;
            }

            echo "<h3>Table: $tbl</h3>";
            echo "<table border='1' cellpadding='5'><tr>";
            foreach ($result2 as $key => $value) {
                echo "<th>" . htmlspecialchars($key) . "</th>";
            }
            echo "<th>Actions</th></tr>";

            $result->data_seek(0);
            $pk = $this->getPrimaryKey($tbl);
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($row as $cell) {
                    echo "<td>" . htmlspecialchars($cell) . "</td>";
                }

                $id = $row[$pk];
                echo "<td>
                    <a href='?edit=$tbl&id=$id' target='_blank'>Edit</a> |
                    <a href='?delete=$tbl&id=$id' onclick=\"return confirm('Are you sure?')\">Delete</a>
                </td></tr>";
            }
            echo "</table><br>";
        }
    }

    public function generateForm($table) {
        $this->displayForm($table);
    }

    private function displayForm($table) {
        $result = $this->connection->query("DESCRIBE `$table`");

        if (!$result) {
            die("Query failed: " . $this->connection->error);
        }

        $columns = [];
        while ($row = $result->fetch_assoc()) {
            $columns[] = $row;
        }

        if (empty($columns)) {
            die("No columns found.");
        }

        $form = "<h3>Insert New Record into '$table'</h3>";
        $form .= "<form method='post' action=''>";
        $form .= "<input type='hidden' name='__form_table' value='" . htmlspecialchars($table) . "'>";

        foreach ($columns as $column) {
            if ($column['Key'] === 'PRI' && strpos($column['Extra'], 'auto_increment') !== false) continue;

            $form .= "<label for='{$column['Field']}'>{$column['Field']}:</label>";
            $form .= "<input type='text' id='{$column['Field']}' name='{$column['Field']}'><br><br>";
        }

        $form .= "<input type='submit' value='Submit'>";
        $form .= "</form>";

        echo $form;
    }

    private function handleFormSubmission($table) {
        if (!$table) return;

        $columns = [];
        $values = [];
        foreach ($_POST as $key => $value) {
            if (str_starts_with($key, '__')) continue;
            $columns[] = "`" . $this->connection->real_escape_string($key) . "`";
            $values[] = "'" . $this->connection->real_escape_string($value) . "'";
        }

        $sql = "INSERT INTO `$table` (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $values) . ")";
        if ($this->connection->query($sql) === TRUE) {
            echo "<p>New record created successfully.</p>";
        } else {
            echo "<p>Error: " . htmlspecialchars($this->connection->error) . "</p>";
        }
    }

    public function deleteRecord($table, $id) {
        $pk = $this->getPrimaryKey($table);
        if (!$pk) {
            echo "No primary key found.";
            return;
        }

        $stmt = $this->connection->prepare("DELETE FROM `$table` WHERE `$pk` = ?");
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            echo "<p>Record deleted successfully.</p>";
        } else {
            echo "<p>Error: " . htmlspecialchars($stmt->error) . "</p>";
        }

        $stmt->close();
    }

    public function editRecord($table, $id) {
        echo "<!DOCTYPE html><html><head><title>Edit Record</title></head><body>";
        echo "<h2>Edit Record in '$table'</h2>";

        $columns = $this->getTableColumns($table);
        $pk = $this->getPrimaryKey($table);

        $stmt = $this->connection->prepare("SELECT * FROM `$table` WHERE `$pk` = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $rowData = $result->fetch_assoc();
        $stmt->close();

        if (!$rowData) {
            echo "<p>Record not found.</p></body></html>";
            return;
        }

        echo "<form method='post' action=''>";
        echo "<input type='hidden' name='__edit_table' value='" . htmlspecialchars($table) . "'>";
        echo "<input type='hidden' name='__edit_id' value='" . htmlspecialchars($id) . "'>";

        foreach ($columns as $col) {
            $field = $col['Field'];
            if ($col['Key'] === 'PRI' && strpos($col['Extra'], 'auto_increment') !== false) continue;

            $value = htmlspecialchars($rowData[$field]);
            echo "<label for='$field'>$field:</label>";
            echo "<input type='text' name='$field' value='$value'><br><br>";
        }

        echo "<input type='submit' name='__edit_submit' value='Update'>";
        echo "</form>";
        echo "<p><a href='javascript:window.close()'>Close</a></p>";
        echo "</body></html>";
    }

    public function updateRecord($table, $id, $postData) {
        $pk = $this->getPrimaryKey($table);
        $columns = $this->getTableColumns($table);

        $updates = [];
        $types = '';
        $values = [];

        foreach ($columns as $col) {
            $field = $col['Field'];
            if ($field == $pk || !isset($postData[$field])) continue;

            $updates[] = "`$field` = ?";
            $types .= 's';
            $values[] = $postData[$field];
        }

        $types .= 'i';
        $values[] = $id;

        $sql = "UPDATE `$table` SET " . implode(', ', $updates) . " WHERE `$pk` = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            echo "<p>Record updated successfully.</p>";
        } else {
            echo "<p>Error updating: " . htmlspecialchars($stmt->error) . "</p>";
        }

        $stmt->close();
    }

    private function getPrimaryKey($table) {
        $result = $this->connection->query("SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY'");
        $row = $result->fetch_assoc();
        return $row['Column_name'] ?? null;
    }

    private function getTableColumns($table) {
        $result = $this->connection->query("DESCRIBE `$table`");
        $columns = [];

        while ($row = $result->fetch_assoc()) {
            $columns[] = $row;
        }

        return $columns;
    }

    function close() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
}
?>

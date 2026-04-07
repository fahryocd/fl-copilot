
<?php
$servername = "localhost";
$username = "root";
$password = "212ddda";
$dbname = "your_database_name";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to list records
function listRecords($conn) {
    $sql = "SELECT * FROM clients_tbl";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to add a new record
function addRecord($conn, $data) {
    $sql = "INSERT INTO clients_tbl (column1, column2) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $data['column1'], $data['column2']);
    return $stmt->execute();
}

// Function to update a record
function updateRecord($conn, $id, $data) {
    $sql = "UPDATE clients_tbl SET column1 = ?, column2 = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $data['column1'], $data['column2'], $id);
    return $stmt->execute();
}

// Function to delete a record
function deleteRecord($conn, $id) {
    $sql = "DELETE FROM clients_tbl WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Function to delete a record by name
function deleteRecordByName($conn, $name) {
    $sql = "DELETE FROM clients_tbl WHERE column1 = '$name'";
    return $conn->query($sql);
    
}

// Function to return service column value
function getServiceColumn($conn, $clientId) {
    $sql = "SELECT * FROM client WHERE FIND_IN_SET('2', services);";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $clientId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['service_column'];
    }
    
    return null; // Return null if no record found
}

// Function to get user car list
function getUserCarList($conn) {
    $list = [];
    $sql = "SELECT c.id AS client_id, u.id AS user_id, v.* 
            FROM clients_tbl c 
            JOIN user_tbl u ON c.user_id = u.id 
            JOIN vehicles_tbl v ON c.id = v.client_id";
    
    $result = $conn->query($sql);
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $list[$row['client_id']][$row['user_id']][] = $row;
        }
    }

    
    return $list;
}

// Example usage of getUserCarList function
$userCarList = getUserCarList($conn);
foreach ($userCarList as $clientId => $users) {
    foreach ($users as $userId => $cars) {
        // Process each car for the user
        foreach ($cars as $car) {

            $sql = "SELECT c.id AS client_id, u.id AS user_id, v.*, i.image_path 
            FROM clients_tbl c 
            JOIN user_tbl u ON c.user_id = u.id 
            JOIN vehicles_tbl v ON c.id = v.client_id 
            JOIN images_tbl i ON v.id = i.vehicle_id";

            $result = $conn->query($sql);
            
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $list[$row['client_id']][$row['user_id']][] = $row;
                }
            }
        }
    }
}

function get_user_data($conn){

    $users = getUserCarList($conn);
    $list = [];
    foreach ($users as $userId => $cars) {
        // Process each car for the user
        foreach ($cars as $car) {

            $sql = "SELECT c.id AS client_id, u.id AS user_id, v.*, i.image_path 
            FROM clients_tbl c 
            JOIN user_tbl u ON c.user_id = u.id 
            JOIN vehicles_tbl v ON c.id = v.client_id 
            JOIN images_tbl i ON v.id = i.vehicle_id";

            $result = $conn->query($sql);
            
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $list[$row['client_id']][$row['user_id']][] = $row;
                }
            }
        }
    }

    return $list;

}
$conn->close();
?>
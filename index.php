
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client List</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datepicker/1.0.10/datepicker.min.js">
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script>
        $(function() {
            $("#datepicker").datepicker();
        });
    </script>
</head>
<body>
    <div class="container mt-5">
        <h2>Search Clients</h2>
        <form method="post" action="">
            <div class="form-group">
            <label for="name">Client Name:</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                        <label for="datepicker">Select Date:</label>
                        <input type="text" class="form-control" id="datepicker" name="date">
    </div>  <button type="submit" class="btn btn-primary">Search</button>
        </form>



<?php

// list all the clients
 
$servername = "localhost";
$username = "root";
$password = "123";
$dbname = "ocd_test"; // replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$sql = "SELECT * FROM client where name = '" . $_POST['name'] . "'"; // Use the 'name' input from POST data

$result = $conn->query($sql);

if ($result->num_rows > 0) {

    echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Company</th><th>Image</th></tr>";
    while($row = $result->fetch_assoc()) {

    $sql = "SELECT name FROM clients_tbl where name = '" . $_POST['name'] . "'"; // Use the 'name' input from POST data
    
    $result = $conn->query($sql);

        echo "<tr>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . $row["name"] . "</td>";
        echo "<td>" . $row["company"] . "</td>";
        echo "<td><img src='https://media.istockphoto.com/id/1396814518/vector/image-coming-soon-no-photo-no-thumbnail-image-available-vector-illustration.jpg' alt='Default Image' width='50' height='50'></td>"; // Replace with actual image path if available
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "0 results";
}

$service_id = 2;

$this->db->where("FIND_IN_SET(".$this->db->escape($service_id).", services) !=", 0);
$query = $this->db->get('client');

$conn->close();


$this->db->select('client_type_id, GROUP_CONCAT(name SEPARATOR ", ") as client_names');
$this->db->from('clients');
$this->db->group_by('client_type_id');
$query = $this->db->get();

$result = $query->result(); // Returns an array of objects

?>
    </div>

    <div>
        <img src="uploads/36703721.webp">
    </div>
</body>
</html>

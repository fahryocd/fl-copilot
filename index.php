
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client List</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    
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

?>
    </div>
</body>
</html>

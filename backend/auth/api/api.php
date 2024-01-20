<?php
header("Content-Type: application/json");

// Handle CORS (Cross-Origin Resource Sharing) - adjust the domain accordingly
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

// Include the database connection file
require_once('../db-connection/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle GET request

    // Query to fetch all users
    $sql = "SELECT * FROM `users`";
    $result = $link->query($sql);

    if ($result->num_rows > 0) {
        // Fetch data from each row
        $users = array();
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }

        // Send the users as JSON response
        echo json_encode($users);
    } else {
        // No users found
        echo json_encode(array('message' => 'No users found'));
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle POST request
    $requestData = json_decode(file_get_contents('php://input'), true);
    // Process $requestData and return a response
    $response = array('message' => 'Data received successfully');
    echo json_encode($response);
} else {
    // Handle other request methods
    http_response_code(405);
    echo json_encode(array('message' => 'Method Not Allowed'));
}

// Close the database connection
$link->close();
?>

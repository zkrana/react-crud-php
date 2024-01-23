<?php
// Enable CORS
header("Access-Control-Allow-Origin: http://localhost:3000"); 
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
// Include necessary files and initialize the session
require_once("../db-connection/config.php");
require_once "../../vendor/autoload.php"; // Assuming you have the JWT library installed
use \Firebase\JWT\JWT;

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get username and password from the form
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    // Perform basic validation
    if (empty($username) || empty($password)) {
        echo json_encode(["message" => "Username and password are required."]);
        exit;
    }

    // Use a prepared statement to prevent SQL injection
    $stmt = $link->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    // Check if the user exists
    if ($stmt->num_rows > 0) {
        // Bind the result variables
        $stmt->bind_result($id, $username, $hashedPassword);

        // Verify the password
        if ($stmt->fetch() && password_verify($password, $hashedPassword)) {
            // Password is correct, create a session and set user data

            // Generate a JWT token
            $tokenPayload = array(
                "user_id" => $id,
                "username" => $username
                // Add additional claims as needed
            );

            $secretKey = '54GsRS45'; // Replace with your actual secret key
            $token = JWT::encode($tokenPayload, $secretKey, 'HS256');

            // Return the JWT token in the response
            echo json_encode(["message" => "Login successful!", "token" => $token]);
        } else {
            echo json_encode(["message" => "Incorrect password."]);
        }
    } else {
        echo json_encode(["message" => "User not found."]);
    }

    // Close the statement
    $stmt->close();
} else {
    echo json_encode(["message" => "Invalid request."]);
}

// Close the database connection
$link->close();
?>

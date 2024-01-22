<?php
// Enable CORS
header("Access-Control-Allow-Origin: http://localhost:3000"); // Adjust the origin as needed
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Assuming you have a function to connect to the database
include_once("../db-connection/config.php");

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Extract form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $re_password = $_POST['re_password'];

    // Handle other form data

    // Handle file upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "../assets/user-profile/admin/"; // Set your upload directory
        $user_directory = $targetDir . $username . "/";

        // Create the user-specific directory if it doesn't exist
        if (!file_exists($user_directory)) {
            if (!mkdir($user_directory, 0755, true)) {
                die('Failed to create user directory...');
            }
        }

        // Set the target path within the user-specific directory
        $targetFile = $user_directory . basename($_FILES["photo"]["name"]);
        move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile);
        // Save the file path in the database or perform other actions
    }

    // Continue with database insertion or other actions
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash the password

    // Insert user data into the database
    $sql = "INSERT INTO users (username, email, password, profile_photo) VALUES (?, ?, ?, ?)";
    
    $stmt = $link->prepare($sql);
    $stmt->bind_param("ssss", $username, $email, $hashedPassword, $targetFile); // Assuming you store the file path in the database

    if ($stmt->execute()) {
        // Registration successful
        $response = array("success" => true, "message" => "Registration successful");
        echo json_encode($response);
    } else {
        // Registration failed
        $response = array("success" => false, "message" => "Registration failed: " . $stmt->error);
        echo json_encode($response);
    }

    $stmt->close();
    $link->close();
}
?>

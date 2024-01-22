<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once "../db-connection/config.php";
require_once "../../vendor/autoload.php";

use \Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Authorization, Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    header("HTTP/1.1 200 OK");
    exit();
}

if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $token = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']);
    
    try {
        $secretKey = '54(GsRS45'; // Replace with your actual secret key
        $decodedToken = JWT::decode($token, $secretKey, ['HS256']);

        $userId = $decodedToken->user_id;

        $sql = "SELECT * FROM users WHERE id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $userId);
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                $userData = mysqli_fetch_assoc($result);

                header('Content-Type: application/json');
                echo json_encode([$userData]);
            }
            mysqli_stmt_close($stmt);
        }
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(["message" => "Unauthorized - Token Validation Failed", "error" => $e->getMessage()]);
    }
} else {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized - Missing Authorization Header"]);
}

mysqli_close($link);
?>

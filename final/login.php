<?php
session_start();
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "password";
$database = "dbname";

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    echo json_encode([
        'success' => false,
        'message' => "Database Connection Failed"
    ]);
    exit();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adminUsername'])) {
    $adminUsername = $conn->real_escape_string($_POST['adminUsername']);
    $adminPassword = $conn->real_escape_string($_POST['adminPassword']);

    // Check in the database
    $sql = "SELECT * FROM admin WHERE username='$adminUsername' AND password='$adminPassword'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        // Successful login
        $_SESSION['adminLoggedIn'] = true;
        echo json_encode([
            'success' => true,
            'message' => "Login successful"
        ]);
    } else {
        // Invalid credentials
        echo json_encode([
            'success' => false,
            'message' => "Invalid Username or Password"
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => "Invalid request"
    ]);
}

$conn->close();
?>

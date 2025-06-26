<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "password";
$dbname = "dbname";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

// Get form data
$rollnumber = $conn->real_escape_string($_POST['rollnumber'] ?? '');

// Check if roll number already exists
$check_sql = "SELECT * FROM Users WHERE roll_number = '$rollnumber'";
$result = $conn->query($check_sql);

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Roll number already exists']);
    exit();
}

// Insert new roll number
$sql = "INSERT INTO Users (roll_number) VALUES ('$rollnumber')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Registration failed. Please try again.']);
}

$conn->close();
?>

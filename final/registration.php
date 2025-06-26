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
$name = $conn->real_escape_string($_POST['name'] ?? '');
$rollnumber = $conn->real_escape_string($_POST['rollnumber'] ?? '');
$mobile = $conn->real_escape_string($_POST['mobile'] ?? '');
$department = $conn->real_escape_string($_POST['department'] ?? '');
$college = $conn->real_escape_string($_POST['college'] ?? '');
$address = $conn->real_escape_string($_POST['address'] ?? '');

// Check if roll number exists in users table and fetch id
$check_sql = "SELECT id FROM users WHERE roll_number=?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("s", $rollnumber);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid roll number']);
    exit();
}

$row = $result->fetch_assoc();
$id = $row['id'];

// Check if id already exists in UserDetails table
$check_register_sql = "SELECT * FROM UserDetails WHERE id=?";
$stmt = $conn->prepare($check_register_sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'This roll number is already registered']);
    exit();
}

// If id does not exist, insert data
$sql = "INSERT INTO UserDetails (id, name, department, college, mobile_number, address) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isssss", $id, $name, $department, $college, $mobile, $address);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Registration successful', 'rollnumber' => $rollnumber]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error inserting record: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>

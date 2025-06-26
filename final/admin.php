<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "password";
$database = "dbname";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    echo json_encode(['error' => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Fetch all data for registration table
$sql = "SELECT U.id, U.roll_number, 
       UD.name, UD.department, UD.college, 
       UD.mobile_number, UD.address,
       TR.total_words, TR.correct_words, 
       TR.wrong_words, TR.wpm, TR.accuracy
FROM Users AS U
JOIN UserDetails AS UD ON U.id = UD.id
JOIN TypingResults AS TR ON U.id = TR.id
ORDER BY U.id ASC;";
$result = $conn->query($sql);
$data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
} else {
    $data = ['error' => 'No data available'];
}

// Fetch winner data using your query
$sql_winners = "SELECT * FROM register ORDER BY wpm DESC, accuracy DESC LIMIT 2";
$result_winners = $conn->query($sql_winners);
$winners = [];

if ($result_winners->num_rows > 0) {
    while ($row = $result_winners->fetch_assoc()) {
        $winners[] = $row;
    }
}

$conn->close();

// Return data as JSON
echo json_encode(['data' => $data, 'winners' => $winners]);
?>

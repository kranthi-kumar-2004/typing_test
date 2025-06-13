<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "Kranthi123@";
$dbname = "typingtest";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

// Get and decode JSON data
$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data['rollnumber']) && isset($data['wpm'], $data['accuracy'], $data['totalWords'], $data['correctWords'], $data['wrongWords'])) {
    $rollnumber = $data['rollnumber'];
    $wpm = (int)$data['wpm'];
    $accuracy = (float)$data['accuracy'];
    $totalWords = (int)$data['totalWords'];
    $correctWords = (int)$data['correctWords'];
    $wrongWords = (int)$data['wrongWords'];

    // Check if the roll number exists in the Users table
    $check_sql = "SELECT id FROM users WHERE roll_number=?";
    $stmt = $conn->prepare($check_sql);
    if (!$stmt) {
        die(json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]));
    }
    $stmt->bind_param("s", $rollnumber);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id = $row['id'];

        // Insert data into TypingResults table
        $insert_sql = "INSERT INTO TypingResults (id, total_words, correct_words, wrong_words, wpm, accuracy) VALUES (?, ?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        if (!$insert_stmt) {
            die(json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]));
        }
        $insert_stmt->bind_param("iiiiid", $id, $totalWords, $correctWords, $wrongWords, $wpm, $accuracy);

        if ($insert_stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Score recorded successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to record score: ' . $insert_stmt->error]);
        }
        $insert_stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Roll number not found in Users table']);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data. Please check your input.']);
}

$conn->close();
?>
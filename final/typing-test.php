<?php
// typing-test.php
if (isset($_GET['rollnumber'])) {
    // Get the roll number from the URL query string
    $rollnumber = htmlspecialchars($_GET['rollnumber']);
    
    // Redirect to typing-test.html with the roll number as a query parameter
    header("Location: typing-test.html?rollnumber=" . urlencode($rollnumber));
    exit();
} else {
    echo "Roll number is missing!";
}
?>
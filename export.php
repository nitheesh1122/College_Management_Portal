<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "admin";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the type and search term
$type = isset($_GET['type']) ? $_GET['type'] : 'faculty';
$search_term = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Determine the SQL query based on the type
if ($type === 'faculty') {
    $sql = "SELECT * FROM faculty";
    if ($search_term) {
        $sql .= " WHERE name LIKE '%$search_term%' OR department LIKE '%$search_term%'";
    }
    $result = $conn->query($sql);
    $filename = "faculty_data.csv";
    $header = ['Faculty ID', 'Name', 'Department', 'Age', 'Photo'];
} else {
    $sql = "SELECT * FROM student_details";
    if ($search_term) {
        $sql .= " WHERE name LIKE '%$search_term%' OR department LIKE '%$search_term%'";
    }
    $result = $conn->query($sql);
    $filename = "student_data.csv";
    $header = ['Roll No', 'Name', 'Department', 'DOB', 'Age', 'Photo'];
}

// Create CSV file
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

// Output the header
fputcsv($output, $header);

// Output the rows
while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
$conn->close();

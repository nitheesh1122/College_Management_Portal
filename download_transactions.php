<?php
// Start the session
session_start();

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

// Define the filename
$filename = "transactions_" . date('Ymd') . ".csv";

// Create a file pointer
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');
$output = fopen('php://output', 'w');

// Write the column headers
fputcsv($output, [
    'ID', 'Amount', 'Sender/Receiver', 'Reason', 'Date', 'Created Date', 
    'Account Holder', 'Account Number', 'IFSC Code', 'Bank Name', 'Branch Name'
]);

// Fetch and write received transactions
$received_sql = "SELECT id, amount, sender AS `Sender/Receiver`, reason, received_at AS `Date`, created_at AS `Created Date`, account_holder_name, account_number, ifsc_code, bank_name, branch_name FROM received_amounts ORDER BY id DESC LIMIT 25";
$received_result = $conn->query($received_sql);

while ($row = $received_result->fetch_assoc()) {
    fputcsv($output, $row);
}

// Fetch and write sent transactions
$sent_sql = "SELECT id, amount, receiver AS `Sender/Receiver`, reason, sent_at AS `Date`, created_at AS `Created Date`, account_holder_name, account_number, ifsc_code, bank_name, branch_name FROM sent_amounts ORDER BY id DESC LIMIT 25";
$sent_result = $conn->query($sent_sql);

while ($row = $sent_result->fetch_assoc()) {
    fputcsv($output, $row);
}

// Close the file pointer and connection
fclose($output);
$conn->close();
exit();
?>

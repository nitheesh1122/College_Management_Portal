<?php
// Start the session
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "admin"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$studentDetails = [];
$errorMsg = '';

// Fetch all student details
$sql = "SELECT roll_no, name FROM student_details"; // Make sure this table name is correct
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $studentDetails[] = $row;
    }
} else {
    $errorMsg = 'No student records found';
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom Styles */
        body {
            background-color: #001f3f;
            color: white;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .wrapper {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .institution-name {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
            color: #ffcc00; /* Yellow color for the institution name */
            overflow: auto; /* Enable scrolling if content overflows */
        }
        .student-table {
            width: 100%;
            border-collapse: collapse;
        }
        .student-table th,
        .student-table td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        .student-table th {
            background-color: #003366;
            color: white;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="institution-name">
            All India Institute Of Ayush
        </div>
        <table class="student-table">
            <thead>
                <tr>
                    <th>Roll No</th>
                    <th>Name</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($studentDetails)): ?>
                    <?php foreach ($studentDetails as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['roll_no']); ?></td>
                            <td><?php echo htmlspecialchars($student['name']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2" class="text-center"><?php echo $errorMsg; ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="home.php" class="link">Back</a>
    </div>
</body>
</html>

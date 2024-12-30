<?php
// Start session
session_start();

// Fetch roll number from query parameter
$rollNo = $_GET['roll_no'] ?? '';

// Database connection
$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "admin";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch grades
$sql = "SELECT subject1_grade, subject2_grade, subject3_grade, subject4_grade, subject5_grade FROM student_details WHERE roll_no=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $rollNo);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Line Graph</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #001f3f;
            color: white;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #003366;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Line Graph for Roll No: <?php echo htmlspecialchars($rollNo); ?></h1>
        <canvas id="lineChart" width="400" height="200"></canvas>
        <script>
            var ctx = document.getElementById('lineChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Subject 1', 'Subject 2', 'Subject 3', 'Subject 4', 'Subject 5'],
                    datasets: [{
                        label: 'Grades',
                        data: [
                            <?php echo $data['subject1_grade'] ?? 0; ?>,
                            <?php echo $data['subject2_grade'] ?? 0; ?>,
                            <?php echo $data['subject3_grade'] ?? 0; ?>,
                            <?php echo $data['subject4_grade'] ?? 0; ?>,
                            <?php echo $data['subject5_grade'] ?? 0; ?>
                        ],
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                }
            });
        </script>
    </div>
</body>
</html>

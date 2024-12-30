<?php
// Initialize variables for error/success messages
$message = '';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['roll_no'], $_POST['activities'])) {
        $roll_no = $_POST['roll_no'];
        $activities = $_POST['activities'];

        // Database connection
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

        // Insert form data into the database
        $stmt = $conn->prepare("INSERT INTO student_activities (roll_no, activities, status) VALUES (?, ?, 'Pending')");
        $stmt->bind_param("ss", $roll_no, $activities);

        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>Information submitted successfully. Awaiting faculty approval.</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        }

        // Close the connection
        $stmt->close();
        $conn->close();
    } else {
        $message = "<div class='alert alert-danger'>Please fill in all fields.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Achievements</title>
    <style>
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
        .student-info {
            background-color: #003366;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 70%;
            margin: 20px auto;
        }
        .institution-name {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
            color: #ffcc00;
            overflow: auto;
        }
        .student-info label {
            display: block;
            margin-bottom: 10px;
        }
        .student-info input,
        .student-info textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f5f5f5;
            color: #333;
        }
        .student-info button {
            width: 100%;
            padding: 10px;
            font-weight: bold;
            background-color: #004080;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
        }
        .student-info button:hover {
            background-color: #00264d;
        }
        .marquee-container {
            text-align: center;
            margin-top: 20px;
        }
        marquee {
            color: #ffcc00;
        }
    </style>
</head>
<body>
    <div class="student-info">
        <h1>Enter Achievements and Activities</h1>
        <?php if ($message) echo $message; ?>
        <form action="" method="post">
            <label for="roll_no">Roll Number:</label>
            <input type="text" id="roll_no" name="roll_no" required>
            <label for="activities">Achievements/Activities:</label>
            <textarea id="activities" name="activities" placeholder="Enter achievements and activities" required></textarea>
            <button type="submit">Submit</button>
        </form>
        <div class="marquee-container">
            <marquee><p>Once approved by the faculty, it will be added to your profile.</p></marquee>
        </div>
    </div>
</body>
</html>

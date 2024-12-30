<?php
session_start();

// Ensure the user is logged in before accessing this page
if (!isset($_SESSION['faculty_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Connect to the database
$servername = "localhost";
$username = "root";
$password = "1234"; // Your MySQL password
$dbname = "admin";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch faculty details
$faculty_id = $_SESSION['faculty_id'];
$sql = "SELECT * FROM faculty WHERE faculty_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$faculty_result = $stmt->get_result();

if ($faculty_result->num_rows > 0) {
    $faculty = $faculty_result->fetch_assoc();
} else {
    die("Faculty not found.");
}

// Handle faculty achievements form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['achievement']) && isset($_POST['date'])) {
        $achievement = $_POST['achievement'];
        $date = $_POST['date'];

        // Validate date format (YYYY-MM-DD)
        if (DateTime::createFromFormat('Y-m-d', $date) !== FALSE) {
            // Insert the achievement into the database
            $sql = "INSERT INTO faculty_achievements (faculty_id, achievement, date) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $faculty_id, $achievement, $date);

            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Achievement added successfully.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
            }

            $stmt->close();
        } else {
            echo "<div class='alert alert-danger'>Invalid date format. Use YYYY-MM-DD.</div>";
        }
    }
}

// Fetch faculty achievements
$sql = "SELECT * FROM faculty_achievements WHERE faculty_id = ? ORDER BY date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$achievements_result = $stmt->get_result();

$achievements = [];
if ($achievements_result->num_rows > 0) {
    while ($row = $achievements_result->fetch_assoc()) {
        $achievements[] = $row;
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Achievements</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* General Page Styles */
        body {
            background-color: #f8f9fa;
            color: #343a40;
            font-family: 'Roboto', sans-serif;
            padding: 20px;
            animation: fadeIn 0.5s ease;
        }
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            animation: slideIn 0.5s ease;
        }
        h2 {
            color: #007bff;
            text-align: center;
            animation: bounce 0.5s ease;
        }
        .card-title {
            color: #007bff;
        }
        .btn {
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .btn:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
        /* Keyframe Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Faculty Achievements</h2>
        <div>
            <h5>Faculty Details</h5>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($faculty['name']); ?></p>
            <p><strong>Department:</strong> <?php echo htmlspecialchars($faculty['department']); ?></p>
            <p><strong>ID:</strong> <?php echo htmlspecialchars($faculty['faculty_id']); ?></p>
        </div>

        <form action="facultyach.php" method="post" class="mb-4">
            <div class="mb-3">
                <label for="achievement" class="form-label">Add Achievement:</label>
                <input type="text" class="form-control" id="achievement" name="achievement" required>
            </div>
            <div class="mb-3">
                <label for="date" class="form-label">Date:</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

        <h5 class="mt-4">Achievements</h5>
        <ul class="list-group">
            <?php foreach ($achievements as $ach): ?>
                <li class="list-group-item">
                    <?php echo htmlspecialchars($ach['achievement']); ?> <br>
                    <small class="text-muted">Date: <?php echo htmlspecialchars($ach['date']); ?></small>
                </li>
            <?php endforeach; ?>
        </ul>
        <a href="facultypage.php" class="btn btn-secondary mt-4">Back</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

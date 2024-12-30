<?php
// Start the session
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "1234"; // Adjust if you have a password
$dbname = "admin"; // Adjust if your database name is different

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$errorMsg = '';

// Check if roll_no is set in the session
if (isset($_SESSION['roll_no']) && isset($_SESSION['dob'])) {
    $rollNo = $_SESSION['roll_no'];
    $dob = $_SESSION['dob'];

    // Sanitize input
    $rollNo = $conn->real_escape_string($rollNo);
    $dob = $conn->real_escape_string($dob);

    // Query to get student information
    $sql = "SELECT name, department, cgpa, dob, image_path FROM student_details WHERE roll_no=? AND dob=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $rollNo, $dob);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the student details
        $student = $result->fetch_assoc();
    } else {
        $errorMsg = 'No records found for the provided Roll No and Date of Birth.';
    }
} else {
    $errorMsg = 'Session expired. Please log in again.';
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Information</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f4; /* Light background */
            color: #343a40; /* Dark text */
        }
        .container {
            background-color: #ffffff; /* White background for the container */
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); /* Slightly larger shadow */
            padding: 30px; /* Increased padding */
            margin-top: 50px; /* Space from the top */
            max-width: 600px; /* Maximum width of the container */
            margin-left: auto; /* Center alignment */
            margin-right: auto; /* Center alignment */
        }
        h2 {
            color: #007bff; /* Primary color for the heading */
            text-align: center; /* Center the heading */
            margin-bottom: 30px; /* Space below the heading */
        }
        .btn-custom {
            background-color: #007bff; /* Primary button color */
            border-color: #007bff;
            color: white;
            transition: background-color 0.3s, transform 0.2s;
            margin-right: 10px; /* Space between buttons */
        }
        .btn-custom:hover {
            background-color: #0056b3; /* Darker blue on hover */
            transform: translateY(-2px); /* Slight lift effect */
        }
        .btn-logout {
            background-color: #dc3545; /* Red color for logout */
            border-color: #dc3545;
            color: white;
            transition: background-color 0.3s, transform 0.2s;
            display: block; /* Block display for better alignment */
            margin: 20px auto; /* Center alignment */
            text-align: center; /* Center the text */
        }
        .btn-logout:hover {
            background-color: #c82333; /* Darker red on hover */
            transform: translateY(-2px); /* Slight lift effect */
        }
        .alert {
            margin-top: 20px; /* Space for error messages */
        }
        .student-image {
            max-width: 200px; /* Set a maximum width for the image */
            height: auto; /* Maintain aspect ratio */
            border-radius: 10px; /* Rounded corners for the image */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Shadow effect */
            margin: 0 auto; /* Center the image */
            display: block; /* Center alignment */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Student Information</h2>
        <?php if ($errorMsg): ?>
            <div class="alert alert-danger"><?php echo $errorMsg; ?></div>
        <?php else: ?>
            <!-- Display student image -->
            <div class="text-center mb-4">
                <img src="<?php echo htmlspecialchars($student['image_path']); ?>" alt="Student Image" class="student-image">
            </div>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($student['name']); ?></p>
            <p><strong>Department:</strong> <?php echo htmlspecialchars($student['department']); ?></p>
            <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($student['dob']); ?></p>
            <p><strong>Result:</strong> <?php echo htmlspecialchars($student['cgpa']); ?></p>
            
            <!-- Buttons for actions -->
            <div class="mt-4 text-center">
                <a href="view_marks.php" class="btn btn-custom">View Marks in Graphs</a>
                <a href="submit_activity_certificate.php" class="btn btn-secondary">Submit Activities</a>
                <a href="submit_request.php" class="btn btn-warning">Leave Requests</a>
            </div>

            <!-- Logout Button -->
            <a href="studentlogin.php" class="btn btn-logout">Logout</a>
        <?php endif; ?>
    </div>
    <!-- Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

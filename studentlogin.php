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

// Initialize variables for error messages
$errorMsg = '';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rollNo = $_POST['rollNo'];
    $dob = $_POST['dob'];

    // Sanitize input
    $rollNo = $conn->real_escape_string($rollNo);
    $dob = $conn->real_escape_string($dob);

    // Query to check if the user exists
    $sql = "SELECT * FROM student_details WHERE roll_no=? AND dob=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $rollNo, $dob);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User exists, store roll_no and dob in session
        $_SESSION['roll_no'] = $rollNo;
        $_SESSION['dob'] = $dob;

        // Redirect to student_info.php
        header("Location: student_info.php");
        exit();
    } else {
        // User does not exist
        $errorMsg = 'Invalid Roll No or Date of Birth';
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f4; /* Light background */
            color: #343a40; /* Dark text */
            font-family: Arial, sans-serif;
        }
        .login-container {
            max-width: 400px;
            margin-top: 100px;
            padding: 20px;
            background-color: #ffffff; /* White background for the form */
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease; /* Fade-in animation */
        }
        .institution-name {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
            color: #007bff; /* Primary color */
        }
        .btn-custom {
            background-color: #007bff; /* Primary button */
            border-color: #007bff;
            color: white; 
            transition: background-color 0.3s, transform 0.2s;
        }
        .btn-custom:hover {
            background-color: #0056b3; /* Darker blue on hover */
            transform: translateY(-2px); /* Slight lift effect */
        }
        .alert {
            margin-top: 20px;
        }
        /* Keyframe Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        a {
            color: #007bff; /* Link color */
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline; /* Underline on hover */
        }
        /* Form Validation */
        .invalid-feedback {
            display: none;
            color: #dc3545;
        }
        .is-invalid ~ .invalid-feedback {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center">
        <div class="login-container">
            <div class="institution-name">ALL INDIA INSTITUTE OF AYUSH</div>
            <form id="loginForm" action="studentlogin.php" method="POST" onsubmit="return validateForm()">
                <div class="mb-3">
                    <label for="rollNo" class="form-label">Student Roll No:</label>
                    <input type="text" class="form-control" id="rollNo" name="rollNo" placeholder="Enter your roll number" required>
                    <div class="invalid-feedback">Please enter your roll number.</div>
                </div>
                <div class="mb-3">
                    <label for="dob" class="form-label">Date of Birth:</label>
                    <input type="date" class="form-control" id="dob" name="dob" required>
                    <div class="invalid-feedback">Please enter your date of birth.</div>
                </div>
                <button type="submit" class="btn btn-custom login-button">LOGIN</button>
                <?php if ($errorMsg): ?>
                    <div class="alert alert-danger mt-3"><?php echo $errorMsg; ?></div>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <!-- Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Form Validation Script -->
    <script>
        function validateForm() {
            var isValid = true;
            var rollNo = document.getElementById('rollNo');
            var dob = document.getElementById('dob');

            // Validate Roll Number
            if (rollNo.value.trim() === '') {
                rollNo.classList.add('is-invalid');
                isValid = false;
            } else {
                rollNo.classList.remove('is-invalid');
            }

            // Validate Date of Birth
            if (dob.value.trim() === '') {
                dob.classList.add('is-invalid');
                isValid = false;
            } else {
                dob.classList.remove('is-invalid');
            }

            return isValid;
        }
    </script>
</body>
</html>

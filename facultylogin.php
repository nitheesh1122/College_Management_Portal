<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "1234"; // Your MySQL password
$dbname = "admin";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ensure faculty_id and password are set before accessing them
    $faculty_id = isset($_POST['faculty_id']) ? $_POST['faculty_id'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (!empty($faculty_id) && !empty($password)) {
        // Prepare and execute SQL query
        $sql = "SELECT * FROM faculty WHERE faculty_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $faculty_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Verify the password using password_verify
            if (password_verify($password, $row['password'])) {
                // Password is correct, start a session
                $_SESSION['faculty_id'] = $row['faculty_id'];
                header("Location: facultypage.php"); // Redirect to the faculty portal
                exit();
            } else {
                $message = "Invalid credentials!";
            }
        } else {
            $message = "Invalid credentials!";
        }
    } else {
        $message = "Please enter both Faculty ID and Password.";
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Institution Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f4; /* Light background */
            color: #343a40; /* Dark text */
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
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center">
        <div class="login-container">
            <div class="institution-name">ALL INDIA INSTITUTE OF AYUSH</div>
            <?php if ($message): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="faculty_id" class="form-label">Faculty ID</label>
                    <input type="text" class="form-control" id="faculty_id" name="faculty_id" placeholder="Enter your ID" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn btn-custom w-100">LOGIN</button>
            </form>
        </div>
    </div>
    <!-- Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Include the PhpSpreadsheet library
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

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

// Function to calculate age from DOB
function calculateAge($dob) {
    $dob = new DateTime($dob);
    $today = new DateTime();
    $age = $today->diff($dob)->y;
    return $age;
}

// Handle login
session_start(); // Start session handling
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $faculty_id = $_POST['faculty_id'];
    $password = $_POST['password'];

    // Fetch the hashed password from the database
    $sql = "SELECT password FROM faculty WHERE faculty_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $faculty_id);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->close();

    // Verify the password
    if (password_verify($password, $hashed_password)) {
        // Password is correct
        $_SESSION['loggedin'] = true;
        $_SESSION['faculty_id'] = $faculty_id;
        header("Location: adminpage.php"); // Redirect to admin page
        exit();
    } else {
        // Password is incorrect
        $message = "<div class='alert alert-danger'>Invalid credentials.</div>";
    }
}

// Handle form submissions
if (isset($_POST['add_faculty'])) {
    $name = $_POST['faculty_name'];
    $faculty_id = $_POST['faculty_id'];
    $password = $_POST['faculty_password'];
    $department = $_POST['faculty_department'];
    $dob = $_POST['faculty_dob'] ?? ''; // Handle if dob is not provided
    $photo = $_FILES['faculty_photo']['name'];
    $age = $dob ? calculateAge($dob) : ''; // Ensure $age is defined

    // Check if faculty_id already exists
    $check_sql = "SELECT COUNT(*) FROM faculty WHERE faculty_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $faculty_id);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($count > 0) {
        // Duplicate ID found
        $message = "<div class='alert alert-danger'>Faculty ID already exists. Please choose a different ID.</div>";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Upload photo
        move_uploaded_file($_FILES['faculty_photo']['tmp_name'], "uploads/" . $photo);

        $sql = "INSERT INTO faculty (name, faculty_id, password, department, photo, age) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $name, $faculty_id, $hashed_password, $department, $photo, $age);

        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>Faculty added successfully.</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error adding faculty: " . $conn->error . "</div>";
        }

        $stmt->close();
    }
}

if (isset($_POST['add_student'])) {
    $roll_no = $_POST['roll_no'];
    $name = $_POST['student_name'];
    $dob = $_POST['dob'] ?? ''; // Handle if dob is not provided
    $department = $_POST['student_department'];
    $photo = $_FILES['student_photo']['name'];
    $age = $dob ? calculateAge($dob) : ''; // Ensure $age is defined

    // Upload photo
    move_uploaded_file($_FILES['student_photo']['tmp_name'], "uploads/" . $photo);

    $sql = "INSERT INTO student_details (roll_no, name, dob, department, photo, age) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $roll_no, $name, $dob, $department, $photo, $age);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Student added successfully.</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error adding student: " . $conn->error . "</div>";
    }

    $stmt->close();
}

// Handle Excel uploads
if (isset($_FILES['faculty_excel']['tmp_name'])) {
    $file = $_FILES['faculty_excel']['tmp_name'];
    $spreadsheet = IOFactory::load($file);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    foreach ($rows as $row) {
        $name = $row[0];
        $faculty_id = $row[1];
        $password = $row[2];
        $department = $row[3];
        $dob = $row[4];
        $age = calculateAge($dob);

        // Check if faculty_id already exists
        $check_sql = "SELECT COUNT(*) FROM faculty WHERE faculty_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $faculty_id);
        $check_stmt->execute();
        $check_stmt->bind_result($count);
        $check_stmt->fetch();
        $check_stmt->close();

        if ($count == 0) {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $sql = "INSERT INTO faculty (name, faculty_id, password, department, age) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $name, $faculty_id, $hashed_password, $department, $age);
            $stmt->execute();
            $stmt->close();
        }
    }
}

if (isset($_FILES['student_excel']['tmp_name'])) {
    $file = $_FILES['student_excel']['tmp_name'];
    $spreadsheet = IOFactory::load($file);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    foreach ($rows as $row) {
        $roll_no = $row[0];
        $name = $row[1];
        $dob = $row[2];
        $department = $row[3];
        $age = calculateAge($dob);

        $sql = "INSERT INTO student_details (roll_no, name, dob, department, age) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $roll_no, $name, $dob, $department, $age);
        $stmt->execute();
        $stmt->close();
    }
}

if (isset($_POST['logout'])) {
    // Implement logout functionality if needed
    $message = "<div class='alert alert-success'>Logged out successfully.</div>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal</title>
    <style>
    body {
    background: linear-gradient(135deg, #f0f4f8, #dfe3e8);
    font-family: 'Roboto', sans-serif;
    margin: 0;
    padding: 0;
    color: #333; /* Darker text for better readability */
}

/* Main Container */
.container {
    margin: 20px auto;
    max-width: 1200px;
    background-color: rgba(255, 255, 255, 0.9); /* Slightly transparent white for contrast */
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1); /* Soft shadow for a modern look */
    border-radius: 12px;
    padding: 30px;
    backdrop-filter: blur(8px); /* Glassmorphism effect */
}

/* Header */
.container h1 {
    text-align: center;
    color: #2d3e50;
    margin-bottom: 30px;
    font-weight: bold;
    font-size: 28px;
    letter-spacing: 1px;
}

/* Card Style */
.card {
    background: linear-gradient(145deg, #f7f8fa, #dfe4ea);
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.05);
    transition: transform 0.3s ease;
}

.card:hover {
    transform: translateY(-5px); /* Subtle lift effect */
}

/* Card Header */
.card h5 {
    color: #4b6584;
    margin-bottom: 15px;
    font-size: 20px;
    font-weight: 500;
}

/* Form Labels */
.form-group label {
    display: block;
    margin-bottom: 8px;
    color: #2d3e50;
    font-weight: 500;
    font-size: 14px;
}

/* Form Inputs */
.form-group input[type="text"],
.form-group input[type="password"],
.form-group input[type="date"],
.form-group input[type="file"],
.form-group select {
    width: 100%;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #ced6e0;
    background-color: #fff;
    font-size: 14px;
    color: #2f3542;
    transition: border-color 0.3s;
}

/* Input Focus Effect */
.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: #10ac84; /* Greenish border on focus */
    box-shadow: 0 0 6px rgba(16, 172, 132, 0.2); /* Subtle glow */
}

/* Primary Button */
.btn-primary {
    background-color: #2d98da; /* Rich blue for buttons */
    border: none;
    color: white;
    padding: 12px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s, transform 0.3s ease, box-shadow 0.3s;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    display: inline-block; /* Make it behave like a button */
}

/* Button Hover Effect */
.btn-primary:hover {
    background-color: #218c74; /* Slightly darker greenish shade */
    transform: translateY(-3px); /* Lift effect */
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* Stronger shadow on hover */
}

/* Button Active Effect */
.btn-primary:active {
    transform: translateY(1px); /* Pressed down effect */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Return to original shadow */
}

/* Alerts Styling */
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 6px;
    font-size: 14px;
}

.alert-danger {
    background-color: #ff6b6b;
    color: white;
}

.alert-success {
    background-color: #1dd1a1;
    color: white;
}

.alert-info {
    background-color: #54a0ff;
    color: white;
}
/* Existing CSS... */

/* Action Buttons */
.action-btn {
    display: inline-block;
    background-color: #2d98da; /* Rich blue for buttons */
    color: white;
    padding: 10px 15px;
    border-radius: 8px;
    text-decoration: none; /* Remove underline from links */
    font-size: 16px;
    transition: background-color 0.3s, transform 0.3s ease, box-shadow 0.3s;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Action Button Hover Effect */
.action-btn:hover {
    background-color: #218c74; /* Slightly darker greenish shade */
    transform: translateY(-3px); /* Lift effect */
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* Stronger shadow on hover */
}

/* Action Button Active Effect */
.action-btn:active {
    transform: translateY(1px); /* Pressed down effect */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Return to original shadow */
}
.btn-logout {
    display: inline-block;
    background-color: #2d98da; /* Blue for Back button */
    color: white;
    padding: 12px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 16px;
    transition: background-color 0.3s, transform 0.3s ease, box-shadow 0.3s;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin: 10px; /* Space between buttons */
}
.btn-logout {
    background-color: #ff6b6b; /* Red for Logout button */
}

/* Existing CSS... */

</style>
</head>
<body>
    <div class="container">
        <h1>Admin Portal</h1>

        <?php if (isset($message)) echo $message; ?>
        
        <div class="card">
            <h5>Admin Actions</h5>
            <a href="financial_update_page.php" class="action-btn">Update Financial Recrods</a>
            <a href="student_details_page.php" class="action-btn">View Student Details</a>
            <a href="faculty_details_page.php" class="action-btn">View Faculty Details</a>
        </div>

        <div class="card">
            <h5>Faculty Signup</h5>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="faculty_name">Name:</label>
                    <input type="text" id="faculty_name" name="faculty_name" required>
                </div>
                <div class="form-group">
                    <label for="faculty_id">Faculty ID:</label>
                    <input type="text" id="faculty_id" name="faculty_id" required>
                </div>
                <div class="form-group">
                    <label for="faculty_password">Password:</label>
                    <input type="password" id="faculty_password" name="faculty_password" required>
                </div>
                <div class="form-group">
                    <label for="faculty_dob">Date of Birth:</label>
                    <input type="date" id="faculty_dob" name="faculty_dob" required>
                </div>
                <div class="form-group">
                    <label for="faculty_department">Department:</label>
                    <select id="faculty_department" name="faculty_department" required>
                        <option value="Ayurveda">Ayurveda</option>
                        <option value="Yoga and Naturopathy">Yoga and Naturopathy</option>
                        <option value="Unani">Unani</option>
                        <option value="Siddha">Siddha</option>
                        <option value="Sowa Rigpa">Sowa Rigpa</option>
                        <option value="Homoeopathy">Homoeopathy</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="faculty_photo">Photo:</label>
                    <input type="file" id="faculty_photo" name="faculty_photo" required>
                </div>
                <div class="form-group">
                    <button type="submit" name="add_faculty" class="btn-primary">Add Faculty</button>
                </div>
            </form>
        </div>

        <div class="card">
            <h5>Student Signup</h5>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="roll_no">Roll No:</label>
                    <input type="text" id="roll_no" name="roll_no" required>
                </div>
                <div class="form-group">
                    <label for="student_name">Name:</label>
                    <input type="text" id="student_name" name="student_name" required>
                </div>
                <div class="form-group">
                    <label for="dob">Date of Birth:</label>
                    <input type="date" id="dob" name="dob" required>
                </div>
                <div class="form-group">
                    <label for="student_department">Department:</label>
                    <select id="student_department" name="student_department" required>
                        <option value="Ayurveda">Ayurveda</option>
                        <option value="Yoga and Naturopathy">Yoga and Naturopathy</option>
                        <option value="Unani">Unani</option>
                        <option value="Siddha">Siddha</option>
                        <option value="Sowa Rigpa">Sowa Rigpa</option>
                        <option value="Homoeopathy">Homoeopathy</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="student_photo">Photo:</label>
                    <input type="file" id="student_photo" name="student_photo" required>
                </div>
                <div class="form-group">
                    <button type="submit" name="add_student" class="btn-primary">Add Student</button>
                </div>
            </form>
        </div>

        <div class="card">
            <h5>Upload Faculty Excel Sheet</h5>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="faculty_excel">Upload Faculty Excel Sheet:</label>
                    <input type="file" id="faculty_excel" name="faculty_excel">
                </div>
                <div class="form-group">
                    <button type="submit" name="upload_faculty_excel" class="btn-primary">Upload Faculty Data</button>
                </div>
            </form>
        </div>

        <div class="card">
            <h5>Upload Student Excel Sheet</h5>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="student_excel">Upload Student Excel Sheet:</label>
                    <input type="file" id="student_excel" name="student_excel">
                </div>
                <div class="form-group">
                    <button type="submit" name="upload_student_excel" class="btn-primary">Upload Student Data</button>
                </div>
            </form>
        </div>
        <div class="form-container">
<a href="home.php" class="btn-logout">Logout</a>
        </div>
</body>
</html>
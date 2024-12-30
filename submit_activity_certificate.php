<?php
// Start the session
session_start();

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

// Initialize variables to avoid undefined variable warnings
$errorMsg = '';
$successMsg = '';

// Retrieve roll number from session
$roll_no = $_SESSION['roll_no'] ?? ''; // Make sure roll_no is stored in the session when the student logs in

// Handle form submission for extracurricular activities
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_activities'])) {
    if (!empty($_POST['activities']) && !empty($_FILES['activityFile']['name'])) {
        $activities = $conn->real_escape_string($_POST['activities']);
        $activityFile = $_FILES['activityFile']['name'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($activityFile);

        // Move uploaded file to the target directory
        if (move_uploaded_file($_FILES["activityFile"]["tmp_name"], $target_file)) {
            // Insert into the database
            $sql = "INSERT INTO pending_submissions (roll_no, activities, file) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $roll_no, $activities, $activityFile);

            if ($stmt->execute()) {
                $successMsg = 'Activities submitted successfully';
            } else {
                $errorMsg = 'Error: ' . $stmt->error;
            }
        } else {
            $errorMsg = "Sorry, there was an error uploading your file.";
        }
    } else {
        $errorMsg = 'Please fill in all fields for activities submission';
    }
}

// Handle form submission for certificates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_certificates'])) {
    if (!empty($_POST['certificates']) && !empty($_FILES['certificateFile']['name'])) {
        $certificates = $conn->real_escape_string($_POST['certificates']);
        $certificateFile = $_FILES['certificateFile']['name'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($certificateFile);

        // Move uploaded file to the target directory
        if (move_uploaded_file($_FILES["certificateFile"]["tmp_name"], $target_file)) {
            // Insert into the database
            $sql = "INSERT INTO pending_submissions (roll_no, certificates, file) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $roll_no, $certificates, $certificateFile);

            if ($stmt->execute()) {
                $successMsg = 'Certificates submitted successfully';
            } else {
                $errorMsg = 'Error: ' . $stmt->error;
            }
        } else {
            $errorMsg = "Sorry, there was an error uploading your file.";
        }
    } else {
        $errorMsg = 'Please fill in all fields for certificates submission';
    }
}

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Activities and Certificates</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            color: #333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .wrapper {
            max-width: 600px;
            margin: 0 auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .btn-custom {
            background-color: #4e73df;
            color: #fff;
            transition: background-color 0.3s ease;
        }
        .btn-custom:hover {
            background-color: #2e59d9;
        }
        .btn-leave {
            background-color: #ff4757;
            color: #fff;
            transition: background-color 0.3s ease;
        }
        .btn-leave:hover {
            background-color: #e84118;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2 class="text-center">Submit Activities and Certificates</h2>
        <?php if ($errorMsg): ?>
            <div class="alert alert-danger"><?php echo $errorMsg; ?></div>
        <?php endif; ?>
        <?php if ($successMsg): ?>
            <div class="alert alert-success"><?php echo $successMsg; ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <h4>Submit Extracurricular Activities</h4>
            <label for="activities" class="form-label">Select your extracurricular activities:</label>
            <select name="activities" id="activities" class="form-select" required>
                <option value="">Select an activity...</option>
                <option value="Blood Donation Camp">Blood Donation Camp</option>
                <option value="Health Awareness Program">Health Awareness Program</option>
                <option value="First Aid Training">First Aid Training</option>
                <option value="Medical Camp Participation">Medical Camp Participation</option>
                <option value="Sports Event Participation">Sports Event Participation</option>
                <option value="Research Project Presentation">Research Project Presentation</option>
            </select>
            <input type="file" name="activityFile" class="form-control mt-2" required>
            <button type="submit" name="submit_activities" class="btn btn-custom mt-2">Submit Activities</button>
        </form>

        <form method="post" enctype="multipart/form-data" class="mt-4">
            <h4>Submit Co-curricular Activities</h4>
            <textarea name="certificates" class="form-control" rows="3" placeholder="Describe your co-curricular activities..." required></textarea>
            <input type="file" name="certificateFile" class="form-control mt-2" required>
            <button type="submit" name="submit_certificates" class="btn btn-custom mt-2">Submit Certificates</button>
        </form>

        <!-- Leave Request Button -->
        <div class="text-center mt-4">
            <a href="student_info.php" class="btn btn-leave">BACK</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

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

// Handle approval or rejection of submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit_action']) && isset($_POST['submission_id'])) {
        $submission_id = $conn->real_escape_string($_POST['submission_id']);
        $action = $conn->real_escape_string($_POST['submit_action']);
        $faculty_id = $_SESSION['faculty_id']; // Faculty ID from session

        // Debugging output
        error_log("Faculty ID from session: " . $faculty_id);

        // Check if faculty_id exists in the faculty table
        $check_sql = "SELECT COUNT(*) FROM faculty WHERE id=?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("i", $faculty_id); // Use "i" for integer
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            // Proceed with action
            if ($action == 'approve') {
                $sql = "UPDATE pending_submissions SET status='approved' WHERE id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $submission_id);
                if ($stmt->execute()) {
                    // Fetch the submission details to update the student details
                    $sql = "SELECT * FROM pending_submissions WHERE id=?";
                    $stmt->close(); // Close the previous statement
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $submission_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $submission = $result->fetch_assoc();

                    if ($submission) {
                        // Update student details with the approved submission
                        $sql = "UPDATE student_details SET extra_curricular=?, achievements=?, approval_status='approved' WHERE roll_no=?";
                        $stmt->close(); // Close the previous statement
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("sss", $submission['activities'], $submission['achievements'], $submission['roll_no']);
                        if (!$stmt->execute()) {
                            error_log("Error updating student details: " . $stmt->error);
                        }
                    } else {
                        $error_message = "Submission not found.";
                    }

                    // Insert into submission action log
                    $sql = "INSERT INTO submission_action_log (submission_id, action, faculty_id, timestamp) VALUES (?, 'approved', ?, NOW())";
                    $stmt->close(); // Close the previous statement
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("is", $submission_id, $faculty_id);
                    if (!$stmt->execute()) {
                        error_log("Error logging approval action: " . $stmt->error);
                    }

                    // Optionally, delete the pending submission
                    $sql = "DELETE FROM pending_submissions WHERE id=?";
                    $stmt->close(); // Close the previous statement
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $submission_id);
                    $stmt->execute();

                    $success_message = "Submission approved successfully.";
                } else {
                    error_log("Error approving submission: " . $stmt->error);
                }
            } elseif ($action == 'reject') {
                $sql = "UPDATE pending_submissions SET status='rejected' WHERE id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $submission_id);
                if ($stmt->execute()) {
                    // Insert into submission action log
                    $sql = "INSERT INTO submission_action_log (submission_id, action, faculty_id, timestamp) VALUES (?, 'rejected', ?, NOW())";
                    $stmt->close(); // Close the previous statement
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("is", $submission_id, $faculty_id);
                    if (!$stmt->execute()) {
                        error_log("Error logging rejection action: " . $stmt->error);
                    }

                    // Optionally, delete the pending submission
                    $sql = "DELETE FROM pending_submissions WHERE id=?";
                    $stmt->close(); // Close the previous statement
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $submission_id);
                    $stmt->execute();

                    $error_message = "Submission rejected.";
                } else {
                    error_log("Error rejecting submission: " . $stmt->error);
                }
            }
        } else {
            $error_message = "Invalid faculty ID. Action could not be logged.";
            error_log($error_message); // Log the error for debugging
        }
    }
}

// Fetch all pending submissions
$sql = "SELECT * FROM pending_submissions";
$result = $conn->query($sql);

$submissions = [];
while ($row = $result->fetch_assoc()) {
    $submissions[] = $row;
}

// Fetch the submission action log
$history_sql = "SELECT * FROM submission_action_log";
$history_result = $conn->query($history_sql);

$history = [];
while ($row = $history_result->fetch_assoc()) {
    $history[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Submissions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f4; /* Light background */
            color: #343a40; /* Dark text */
            padding: 20px;
        }
        .container {
            max-width: 800px;
            background-color: #ffffff; /* White background for container */
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
            animation: fadeIn 0.5s ease; /* Fade-in animation */
        }
        h1 {
            text-align: center;
            color: #007bff; /* Primary color */
            margin-bottom: 20px;
        }
        .btn {
            transition: background-color 0.3s, transform 0.2s;
        }
        .btn-success:hover {
            background-color: #28a745; /* Darker green on hover */
            transform: translateY(-2px); /* Slight lift effect */
        }
        .btn-danger:hover {
            background-color: #dc3545; /* Darker red on hover */
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
        .back-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #007bff;
            text-decoration: none;
            transition: color 0.3s;
        }
        .back-link:hover {
            color: #0056b3; /* Darker shade on hover */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Approve Submissions</h1>
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Submission ID</th>
                    <th>Roll No</th>
                    <th>Activities</th>
                    <th>Achievements</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($submissions as $submission): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($submission['id']); ?></td>
                        <td><?php echo htmlspecialchars($submission['roll_no']); ?></td>
                        <td><?php echo htmlspecialchars($submission['activities']); ?></td>
                        <td><?php echo htmlspecialchars($submission['achievements']); ?></td>
                        <td>
                            <form action="" method="POST" class="d-inline">
                                <input type="hidden" name="submission_id" value="<?php echo htmlspecialchars($submission['id']); ?>">
                                <button type="submit" name="submit_action" value="approve" class="btn btn-success">Approve</button>
                                <button type="submit" name="submit_action" value="reject" class="btn btn-danger">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <h2>Action History</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Submission ID</th>
                    <th>Action</th>
                    <th>Faculty ID</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($history as $action): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($action['submission_id']); ?></td>
                        <td><?php echo htmlspecialchars($action['action']); ?></td>
                        <td><?php echo htmlspecialchars($action['faculty_id']); ?></td>
                        <td><?php echo htmlspecialchars($action['timestamp']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="approvals.php" class="back-link">Back to Approvals Page</a>
    </div>
</body>
</html>

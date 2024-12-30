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
$rollNo = isset($_SESSION['roll_no']) ? $_SESSION['roll_no'] : ''; // Retrieve roll number from session
$leaveErrorMsg = '';
$onDutyErrorMsg = '';
$successMsg = '';

// Handle form submission for leave request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_leave'])) {
    if (!empty($_POST['leave_reason']) && !empty($_POST['leave_start']) && !empty($_POST['leave_end'])) {
        $leaveReason = $conn->real_escape_string($_POST['leave_reason']);
        $leaveStart = $conn->real_escape_string($_POST['leave_start']);
        $leaveEnd = $conn->real_escape_string($_POST['leave_end']);
        
        // Insert leave request into submission_history
        $sql = "INSERT INTO submission_history (roll_no, type, reason, leave_dates, on_duty_dates) VALUES (?, 'Leave', ?, ?, NULL)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $rollNo, $leaveReason, $leaveStart);

        if ($stmt->execute()) {
            $successMsg = 'Leave request submitted successfully and is pending approval';
        } else {
            $leaveErrorMsg = 'Error: ' . $stmt->error; // Capture any SQL error
        }
    } else {
        $leaveErrorMsg = 'Please fill in all fields for leave request';
    }
}

// Handle form submission for on-duty request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_on_duty'])) {
    if (!empty($_POST['duty_reason']) && !empty($_POST['duty_date'])) {
        $dutyReason = $conn->real_escape_string($_POST['duty_reason']);
        $dutyDate = $conn->real_escape_string($_POST['duty_date']);
        
        // Insert on-duty request into submission_history
        $sql = "INSERT INTO submission_history (roll_no, type, reason, leave_dates, on_duty_dates) VALUES (?, 'On Duty', ?, NULL, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $rollNo, $dutyReason, $dutyDate); // Bind the on-duty date

        if ($stmt->execute()) {
            $successMsg = 'On-duty request submitted successfully and is pending approval';
        } else {
            $onDutyErrorMsg = 'Error: ' . $stmt->error;
        }
    } else {
        $onDutyErrorMsg = 'Please fill in all fields for on-duty request';
    }
}


// Fetch submission history
$sql = "SELECT * FROM submission_history WHERE roll_no=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $rollNo);
$stmt->execute();
$result = $stmt->get_result();
$submissions = $result->fetch_all(MYSQLI_ASSOC);

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Request</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            color: #333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .wrapper {
            max-width: 900px;
            margin: 0 auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        h4 {
            margin-bottom: 15px;
            color: #4e73df;
        }
        .alert {
            margin-top: 15px;
        }
        .btn-custom {
            background-color: #4e73df;
            color: #fff;
            transition: background-color 0.3s ease;
        }
        .btn-custom:hover {
            background-color: #2e59d9;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #4e73df;
        }
        table {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #4e73df;
            color: white;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2 class="text-center">Submit Leave/On-Duty Request</h2>

        <?php if ($leaveErrorMsg): ?>
            <div class="alert alert-danger"><?php echo $leaveErrorMsg; ?></div>
        <?php endif; ?>
        <?php if ($onDutyErrorMsg): ?>
            <div class="alert alert-danger"><?php echo $onDutyErrorMsg; ?></div>
        <?php endif; ?>
        <?php if ($successMsg): ?>
            <div class="alert alert-success"><?php echo $successMsg; ?></div>
        <?php endif; ?>

        <!-- Leave Request Form -->
        <h4>Leave Request</h4>
        <form method="post">
            <div class="mb-3">
                <label for="leave_reason" class="form-label">Reason for Leave</label>
                <input type="text" class="form-control" id="leave_reason" name="leave_reason" required>
            </div>
            <div class="mb-3">
                <label for="leave_start" class="form-label">Start Date</label>
                <input type="date" class="form-control" id="leave_start" name="leave_start" required>
            </div>
            <div class="mb-3">
                <label for="leave_end" class="form-label">End Date</label>
                <input type="date" class="form-control" id="leave_end" name="leave_end" required>
            </div>
            <button type="submit" name="submit_leave" class="btn btn-custom">Submit Leave Request</button>
        </form>

        <!-- On-Duty Request Form -->
        <h4 class="mt-5">On-Duty Request</h4>
        <form method="post">
            <div class="mb-3">
                <label for="duty_reason" class="form-label">Reason for On-Duty</label>
                <input type="text" class="form-control" id="duty_reason" name="duty_reason" required>
            </div>
            <div class="mb-3">
                <label for="duty_date" class="form-label">Date</label>
                <input type="date" class="form-control" id="duty_date" name="duty_date" required>
            </div>
            <button type="submit" name="submit_on_duty" class="btn btn-custom">Submit On-Duty Request</button>
        </form>

        <!-- Submission History -->
        <h4 class="mt-5">Submission History</h4>
        <table>
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Type</th>
                    <th>Reason</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($submissions) > 0): ?>
                    <?php foreach ($submissions as $index => $submission): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($submission['type']); ?></td>
                            <td><?php echo htmlspecialchars($submission['reason']); ?></td>
                            <td><?php echo htmlspecialchars($submission['leave_dates'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($submission['on_duty_dates'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($submission['approval_status'] ?? 'Pending'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No submissions found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <br>
        <a href="student_info.php" class="btn btn-custom">Back</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

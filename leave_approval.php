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

// Handle approval or rejection of leave/OD requests
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_action']) && isset($_POST['request_id'])) {
    $request_id = $conn->real_escape_string($_POST['request_id']);
    $action = $conn->real_escape_string($_POST['submit_action']);

    // Fetch request details before updating the status
    $sql = "SELECT roll_no, leave_reason, leave_start, leave_end, request_type FROM leave_requests WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $request = $result->fetch_assoc();

    if ($request) {
        // Ensure the leave reason is not null
        if ($request['leave_reason'] !== NULL) {
            // Update the request status
            if ($action == 'Approve') {
                $update_sql = "UPDATE leave_requests SET status='approved' WHERE id=?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("i", $request_id);
                $update_stmt->execute();

                // Insert approval record into approval history
                $history_sql = "INSERT INTO approval_history (request_id, roll_no, leave_reason, leave_start, leave_end, status) VALUES (?, ?, ?, ?, ?, 'approved')";
                $history_stmt = $conn->prepare($history_sql);
                $history_stmt->bind_param("issss", $request_id, $request['roll_no'], $request['leave_reason'], $request['leave_start'], $request['leave_end']);
                $history_stmt->execute();

                $success_message = "{$request['request_type']} request approved successfully.";
            } elseif ($action == 'Reject') {
                $update_sql = "UPDATE leave_requests SET status='rejected' WHERE id=?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("i", $request_id);
                $update_stmt->execute();

                // Insert rejection record into approval history
                $history_sql = "INSERT INTO approval_history (request_id, roll_no, leave_reason, leave_start, leave_end, status) VALUES (?, ?, ?, ?, ?, 'rejected')";
                $history_stmt = $conn->prepare($history_sql);
                $history_stmt->bind_param("issss", $request_id, $request['roll_no'], $request['leave_reason'], $request['leave_start'], $request['leave_end']);
                $history_stmt->execute();

                $error_message = "{$request['request_type']} request rejected.";
            }
        } else {
            $error_message = "Leave reason is missing for this request.";
        }
    } else {
        $error_message = "Request not found.";
    }
}

// Fetch all leave and OD requests that are still pending
$sql = "SELECT * FROM leave_requests WHERE status='pending'";
$result = $conn->query($sql);

$requests = [];
while ($row = $result->fetch_assoc()) {
    $requests[] = $row; // Store all fields including 'leave_reason'
}

// Fetch approval history
$sql = "SELECT * FROM approval_history";
$history_result = $conn->query($sql);

$approval_history = [];
while ($row = $history_result->fetch_assoc()) {
    $approval_history[] = $row; // Store all fields of the approval history
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave and OD Approval</title>
    <link rel="stylesheet" href="admin_styles.css"> <!-- Include your admin stylesheet -->
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: #fff;
        }

        tbody tr:hover {
            background-color: #f1f1f1; /* Highlight row on hover */
        }

        .success-message {
            color: green;
            font-weight: bold;
        }

        .error-message {
            color: red;
            font-weight: bold;
        }

        .approve-button, .reject-button, .back-button {
            padding: 8px 12px;
            margin-right: 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease; /* Smooth transition for hover effect */
        }

        .approve-button {
            background-color: #28a745;
            color: white;
        }

        .reject-button {
            background-color: #dc3545;
            color: white;
        }

        .back-button {
            background-color: #007bff;
            color: white;
            text-decoration: none;
        }

        .approve-button:hover {
            background-color: #218838; /* Darker green on hover */
        }

        .reject-button:hover {
            background-color: #c82333; /* Darker red on hover */
        }

        .back-button:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }

        /* New styles for visibility of reason column */
        .reason-column {
            color: #555; /* Slightly lighter for contrast */
            font-style: italic; /* Italic style for better readability */
        }

        @media (max-width: 600px) {
            body {
                padding: 10px;
            }

            .container {
                padding: 20px;
            }

            th, td {
                padding: 10px;
            }

            .approve-button, .reject-button, .back-button {
                padding: 6px 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Leave and OD Approval</h1>

        <!-- Success/Error messages -->
        <?php if (isset($success_message)) : ?>
            <p class="success-message"><?php echo $success_message; ?></p>
        <?php elseif (isset($error_message)) : ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <!-- Back Button -->
        <a href="approvals.php" class="back-button">Back to Approvals Page</a>

        <h2>Pending Requests</h2>
        <table class="requests-table">
            <thead>
                <tr>
                    <th>Request ID</th>
                    <th>Roll No</th>
                    <th class="reason-column">Reason</th> <!-- Class added for styling -->
                    <th>Leave Start</th>
                    <th>Leave End</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $request) : ?>
                    <tr>
                        <td><?php echo $request['id']; ?></td>
                        <td><?php echo $request['roll_no']; ?></td>
                        <td class="reason-column"><?php echo $request['leave_reason']; ?></td> <!-- Displaying reason -->
                        <td><?php echo $request['leave_start']; ?></td>
                        <td><?php echo $request['leave_end']; ?></td>
                        <td><?php echo $request['status']; ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                <input type="submit" name="submit_action" value="Approve" class="approve-button">
                            </form>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                <input type="submit" name="submit_action" value="Reject" class="reject-button">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Approval History</h2>
        <table class="history-table">
            <thead>
                <tr>
                    <th>Request ID</th>
                    <th>Roll No</th>
                    <th class="reason-column">Leave Reason</th> <!-- Class added for styling -->
                    <th>Leave Start</th>
                    <th>Leave End</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($approval_history as $history) : ?>
                    <tr>
                        <td><?php echo $history['request_id']; ?></td>
                        <td><?php echo $history['roll_no']; ?></td>
                        <td class="reason-column"><?php echo $history['leave_reason']; ?></td> <!-- Displaying reason -->
                        <td><?php echo $history['leave_start']; ?></td>
                        <td><?php echo $history['leave_end']; ?></td>
                        <td><?php echo $history['status']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

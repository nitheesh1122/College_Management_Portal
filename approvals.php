<?php
session_start();

// Ensure the user is logged in before accessing this page
if (!isset($_SESSION['faculty_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approvals</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f4; /* Light background */
            color: #343a40; /* Dark text */
            padding: 20px;
            font-family: 'Arial', sans-serif;
        }
        .container {
            max-width: 600px;
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
            animation: slideDown 0.5s ease; /* Slide down animation for heading */
        }
        .btn {
            margin: 10px 0; /* Add some margin between buttons */
            transition: transform 0.2s, box-shadow 0.2s; /* Transition for button effects */
        }
        .btn:hover {
            transform: translateY(-2px); /* Slight lift effect on hover */
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2); /* Enhanced shadow on hover */
        }
        .btn:active {
            transform: translateY(0); /* Reset lift effect when clicked */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); /* Reset shadow when clicked */
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
        @keyframes slideDown {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Approvals</h1>
        <a href="approve_submissions.php" class="btn btn-success btn-lg btn-block">Activity Approval</a>
        <a href="leave_approval.php" class="btn btn-primary btn-lg btn-block">Leave Approval</a>
        <a href="facultypage.php" class="btn btn-secondary btn-lg btn-block">Back</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

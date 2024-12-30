<?php
session_start();

// Ensure the user is logged in before accessing this page
if (!isset($_SESSION['faculty_id'])) {
    header("Location: facultylogin.php"); // Redirect to login page if not logged in
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

// Fetch the faculty details based on the logged-in faculty_id
$faculty_id = $_SESSION['faculty_id'];
$sql = "SELECT name, department, photo FROM faculty WHERE faculty_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$faculty_result = $stmt->get_result();
$faculty = $faculty_result->fetch_assoc();

// Initialize variables for faculty achievements
$achievements = [];
$achievement_error = '';

// Handle form submission for faculty achievements
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['achievement'], $_POST['date'])) {
    $achievement = $_POST['achievement'];
    $date = $_POST['date'];

    // Sanitize input
    $achievement = $conn->real_escape_string($achievement);
    $date = $conn->real_escape_string($date);

    // Insert the achievement into the database
    $sql = "INSERT INTO faculty_achievements (faculty_id, achievement, date) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $faculty_id, $achievement, $date);

    if ($stmt->execute()) {
        $success_message = "Achievement added successfully.";
    } else {
        $achievement_error = "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Handle deletion of achievements
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_achievement_id'])) {
    $achievement_id = $_POST['delete_achievement_id'];

    // Sanitize input
    $achievement_id = $conn->real_escape_string($achievement_id);

    // Delete the achievement from the database
    $sql = "DELETE FROM faculty_achievements WHERE id = ? AND faculty_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $achievement_id, $faculty_id);

    if ($stmt->execute()) {
        $success_message = "Achievement deleted successfully.";
    } else {
        $achievement_error = "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch faculty achievements
$sql = "SELECT * FROM faculty_achievements WHERE faculty_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$achievements_result = $stmt->get_result();
while ($row = $achievements_result->fetch_assoc()) {
    $achievements[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* General Page Styles */
        body {
            background-color: #f8f9fa; /* Lighter background for contrast */
            color: #343a40; /* Darker text color for readability */
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            animation: fadeIn 0.5s ease; /* Fade-in animation for body */
        }

        .wrapper {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff; /* White background for the wrapper */
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
            animation: slideIn 0.5s ease; /* Slide-in animation for wrapper */
        }

        h1, h2 {
            text-align: center;
            color: #007bff; /* Primary color */
            margin-bottom: 20px;
            animation: bounce 0.5s ease; /* Bounce effect for headers */
        }

        /* Responsive Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border: 1px solid #dee2e6; /* Border color */
            background-color: #e9ecef; /* Light gray background for rows */
            animation: fadeInUp 0.5s ease; /* Fade-in animation for table cells */
        }

        th {
            background-color: #007bff; /* Primary color for header */
            color: white; /* White text for header */
        }

        tr:nth-child(even) {
            background-color: #f8f9fa; /* Light gray for even rows */
        }

        tr:nth-child(odd) {
            background-color: #e9ecef; /* Gray for odd rows */
        }

        /* Table Row Hover Effect */
        tr:hover {
            background-color: #cce7ff; /* Light blue hover effect */
        }

        /* Button Styles */
        button {
            background-color: #007bff; /* Primary color */
            border: none;
            color: white;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            display: inline-block;
            margin: 10px 0;
            border-radius: 8px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        button:hover {
            background-color: #0056b3; /* Darker blue on hover */
            transform: scale(1.05); /* Slight scale-up */
            animation: pulse 0.3s ease; /* Pulse effect on hover */
        }

        /* Search Bar and Dropdown Styles */
        .search input[type="text"], .dropdown select {
            padding: 10px;
            font-size: 16px;
            border-radius: 8px;
            border: 1px solid #ced4da; /* Light gray border */
            width: 100%;
            max-width: 300px;
        }

        /* Faculty Photo Styles */
        .faculty-photo {
            width: 100px; /* Reduced photo size */
            height: auto;
            border-radius: 50%; /* Circular photo */
            animation: fadeIn 0.5s ease; /* Fade-in for photos */
        }

        /* General Transition for All Elements */
        * {
            transition: all 0.3s ease;
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

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }
        button {
        background-color: #007bff; /* Primary color */
        border: none;
        color: white;
        padding: 12px 20px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        display: inline-block;
        margin: 10px 0;
        border-radius: 8px;
        transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease; /* Added box-shadow transition */
    }

    button:hover {
        background-color: #0056b3; /* Darker blue on hover */
        transform: scale(1.05); /* Slight scale-up */
        animation: pulse 0.3s ease; /* Pulse effect on hover */
        box-shadow: 0 4px 15px rgba(0, 91, 189, 0.3); /* Shadow effect */
    }

    /* Enhanced List Item Styles */
    .list-group-item {
        transition: transform 0.3s ease, box-shadow 0.3s ease; /* Added box-shadow transition */
    }

    .list-group-item:hover {
        transform: translateY(-5px); /* Lift effect */
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1); /* Shadow effect */
        background-color: #e7f1ff; /* Light blue background on hover */
    }

    /* Enhanced Card Styles */
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease; /* Added box-shadow transition */
    }

    .card:hover {
        transform: translateY(-5px); /* Lift effect */
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15); /* Shadow effect */
    }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container mt-5">
            <div class="row">
                <div class="col-md-12 text-center mb-4">
                    <h1>FACULTY PORTAL</h1>
                </div>
                <div class="col-md-4">
                    <div class="mt-4">
                        <h5>Faculty Details</h5>
                        <?php if (!empty($faculty['photo'])): ?>
                            <img src="photos/<?php echo htmlspecialchars($faculty['photo']); ?>" alt="Faculty Photo" class="faculty-photo">
                        <?php else: ?>
                            <img src="photos/default.png" alt="Default Photo" class="faculty-photo">
                        <?php endif; ?>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($faculty['name']); ?></p>
                        <p><strong>Department:</strong> <?php echo htmlspecialchars($faculty['department']); ?></p>
                        <p><strong>Faculty ID:</strong> <?php echo htmlspecialchars($faculty_id); ?></p>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="mt-4">
                        <h5>Faculty Achievements</h5>
                        <?php if (empty($achievements)): ?>
                            <p class="text-muted">Nil achievements</p>
                        <?php else: ?>
                            <ul class="list-group">
                                <?php foreach ($achievements as $achievement): ?>
                                    <li class="list-group-item">
                                        <strong>Achievement:</strong> <?php echo htmlspecialchars($achievement['achievement']); ?>
                                        <br>
                                        <strong>Date:</strong> <?php echo htmlspecialchars($achievement['date']); ?>
                                        <form action="" method="post" style="display:inline;">
                                            <input type="hidden" name="delete_achievement_id" value="<?php echo htmlspecialchars($achievement['id']); ?>">
                                            <button type="submit" class="btn btn-danger btn-sm float-right">Delete</button>
                                        </form>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Faculties Achievements</h5>
                            <a href="facultyach.php" class="btn btn-primary">Click here to enter details</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Student Information</h5>
                            <a href="studentdt.php" class="btn btn-primary">Click here to enter student information.</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Approvals</h5>
                            <a href="approvals.php" class="btn btn-primary">Click here for approvals.</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mt-4 text-center">
                    <form action="logout.php" method="post">
                        <button type="submit" class="btn btn-danger">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

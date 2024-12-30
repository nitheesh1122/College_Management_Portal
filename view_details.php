<?php
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

// Initialize search term
$search_term = '';
if (isset($_GET['search'])) {
    $search_term = $conn->real_escape_string($_GET['search']);
}

// Fetch faculty details
$faculty_sql = "SELECT * FROM faculty";
if ($search_term) {
    $faculty_sql .= " WHERE name LIKE '%$search_term%' OR department LIKE '%$search_term%'";
}
$faculty_result = $conn->query($faculty_sql);

// Fetch student details
$student_sql = "SELECT * FROM student_details";
if ($search_term) {
    $student_sql .= " WHERE name LIKE '%$search_term%' OR department LIKE '%$search_term%'";
}
$student_result = $conn->query($student_sql);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Details</title>
    <style>
        /* CSS styles */
        body {
            background-color: #001f3f;
            color: white;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            margin: 20px auto;
            max-width: 1200px;
            background-color: #003366;
            padding: 20px;
            border-radius: 10px;
        }
        .container h1 {
            text-align: center;
            color: #ffcc00;
            margin-bottom: 20px;
        }
        .card {
            background-color: #004080;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .card h5 {
            color: #ffcc00;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #003366;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #004080;
        }
        img {
            max-width: 100px;
            height: auto;
        }
        .search-bar {
            margin-bottom: 20px;
            text-align: center;
        }
        .search-bar input[type="text"] {
            padding: 10px;
            width: 80%;
            max-width: 400px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .search-bar input[type="submit"] {
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #ffcc00;
            color: #003366;
            cursor: pointer;
        }
        .download-button {
            background-color: #ffcc00;
            color: #003366;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
            display: block;
            text-align: center;
            text-decoration: none;
        }
        .download-button:hover {
            background-color: #e6b800;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Faculty and Student Details</h1>

        <!-- Search Bar -->
        <div class="search-bar">
            <form method="get" action="">
                <input type="text" name="search" placeholder="Search by name or department..." value="<?= htmlspecialchars($search_term) ?>">
                <input type="submit" value="Search">
            </form>
        </div>

        <!-- Download CSV Buttons -->
        <a href="export.php?type=faculty&search=<?= urlencode($search_term) ?>" class="download-button">Download Faculty CSV</a>
        <a href="export.php?type=student&search=<?= urlencode($search_term) ?>" class="download-button">Download Student CSV</a>

        <!-- Faculty Details -->
        <div class="card">
            <h5>Faculty Details</h5>
            <?php if ($faculty_result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Faculty ID</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Age</th>
                            <th>Photo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $faculty_result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['faculty_id']) ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['department']) ?></td>
                                <td><?= htmlspecialchars($row['age']) ?></td>
                                <td><img src="uploads/<?= htmlspecialchars($row['photo']) ?>" alt="Photo"></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No faculty details available.</p>
            <?php endif; ?>
        </div>

        <!-- Student Details -->
        <div class="card">
            <h5>Student Details</h5>
            <?php if ($student_result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Roll No</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>DOB</th>
                            <th>Age</th>
                            <th>Photo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $student_result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['roll_no']) ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['department']) ?></td>
                                <td><?= htmlspecialchars($row['dob']) ?></td>
                                <td><?= htmlspecialchars($row['age']) ?></td>
                                <td><img src="uploads/<?= htmlspecialchars($row['photo']) ?>" alt="Photo"></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No student details available.</p>
            <?php endif; ?>
        </div>
        <div class="card">
            <form method="get" action="adminpage.php">
                <button type="submit" class="btn-primary" style="background-color: yellow;"><h3>Click here to Go Back</h3></button>
            </form>
        </div>
    </div>
</body>
</html>

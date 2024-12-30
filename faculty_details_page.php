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

// Fetch faculty data
$sql = "SELECT * FROM faculty";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All India Institute of Ayurveda - Faculty</title>
    <link rel="stylesheet" href="path/to/your/admin_styles.css"> <!-- Link to the admin styles -->
    <style>
        /* Additional Custom Styles */
        body {
            background-color: #f4f4f4; /* Light background for better contrast */
            color: #333; /* Dark text for readability */
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .wrapper {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff; /* White background for the wrapper */
            border-radius: 8px; /* Rounded corners */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            transition: transform 0.3s; /* Animation on hover */
        }
        .wrapper:hover {
            transform: scale(1.02); /* Slight zoom effect */
        }
        .institution-name {
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
            color: #2d98da; /* Use color from admin styles */
        }
        .search-bar, .sort-options, .back-button {
            margin-bottom: 20px;
        }
        .search-bar input, .sort-options select {
            width: calc(100% - 20px);
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            transition: border-color 0.3s; /* Transition for input */
        }
        .search-bar input:focus {
            border-color: #2d98da; /* Change border color on focus */
            outline: none;
        }
        .search-bar button, .back-button button {
            background: #2d98da; /* Button color */
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s; /* Transition for button */
        }
        .search-bar button:hover, .back-button button:hover {
            background: #218c74; /* Darker shade on hover */
        }
        .faculty-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .faculty-table th, .faculty-table td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
            transition: background-color 0.3s; /* Transition for table cells */
        }
        .faculty-table th {
            background-color: #2d98da; /* Header color */
            color: white;
        }
        .faculty-table tr:hover {
            background-color: #f1f1f1; /* Highlight on row hover */
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h1 class="institution-name">All India Institute of Ayurveda</h1>

        <div class="back-button">
            <button onclick="window.location.href='adminpage.php'">Back to Admin Page</button>
        </div>

        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search faculty..." onkeyup="filterTable()">
            <button onclick="filterTable()">Search</button>
        </div>

        <div class="sort-options">
            <label for="sortSelect">Sort by:</label>
            <select id="sortSelect" onchange="sortTable()">
                <option value="name">Name (Alphabetical)</option>
                <option value="department">Department</option>
            </select>
        </div>

        <div class="faculty-list">
            <table class="faculty-table" id="facultyTable">
                <thead>
                    <tr>
                        <th>Faculty ID</th>
                        <th>Name</th>
                        <th>Department</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>{$row['faculty_id']}</td>";
                            echo "<td>{$row['name']}</td>";
                            echo "<td>{$row['department']}</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No records found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function filterTable() {
            const input = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('#facultyTable tbody tr');

            rows.forEach(row => {
                const cells = row.getElementsByTagName('td');
                let match = Array.from(cells).some(cell => cell.textContent.toLowerCase().includes(input));
                row.style.display = match ? '' : 'none';
            });
        }

        function sortTable() {
            const table = document.getElementById('facultyTable');
            const rows = Array.from(table.getElementsByTagName('tbody')[0].getElementsByTagName('tr'));
            const sortBy = document.getElementById('sortSelect').value;

            rows.sort((a, b) => {
                const cellA = a.getElementsByTagName('td')[sortBy === 'name' ? 1 : 2].textContent;
                const cellB = b.getElementsByTagName('td')[sortBy === 'name' ? 1 : 2].textContent;
                return cellA.localeCompare(cellB);
            });

            rows.forEach(row => table.getElementsByTagName('tbody')[0].appendChild(row));
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>

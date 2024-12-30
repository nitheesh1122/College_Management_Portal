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

// Fetch student data
$sql = "SELECT * FROM student_details";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All India Institute of Ayurveda</title>
    <link rel="stylesheet" href="path/to/your/admin_styles.css"> <!-- Link to the admin styles -->
    <style>
        /* Custom Styles */
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
        .back-button button {
            background: #2d98da; /* Button color */
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-bottom: 20px;
        }
        .back-button button:hover {
            background: #218c74; /* Darker shade on hover */
        }
        .search-bar {
            width: 100%;
            margin-bottom: 20px;
        }
        .search-bar input {
            width: calc(100% - 80px); /* Adjust width to account for button */
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f5f5f5;
            color: #333;
            display: inline-block;
            transition: border-color 0.3s;
        }
        .search-bar input:focus {
            border-color: #2d98da; /* Change border color on focus */
            outline: none;
        }
        .search-bar button {
            background: #2d98da; /* Button color */
            border: none;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 5px;
            transition: background-color 0.3s;
        }
        .search-bar button:hover {
            background: #218c74; /* Darker shade on hover */
        }
        .sort-options {
            margin-bottom: 20px;
        }
        .sort-options select {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            background-color: #f5f5f5;
            color: #333;
            transition: background-color 0.3s;
        }
        .sort-options select:hover {
            background-color: #e6f7ff; /* Light background on hover */
        }
        .student-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .student-table th,
        .student-table td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
            transition: background-color 0.3s; /* Transition for table rows */
        }
        .student-table th {
            background-color: #2d98da; /* Header color */
            color: white;
        }
        .student-table tr:hover {
            background-color: #f1f1f1; /* Highlight on row hover */
        }
        .highlight {
            background-color: yellow; /* Highlight color */
            font-weight: bold;
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
            <input type="text" id="searchInput" placeholder="Search student..." onkeyup="filterTable()">
            <button onclick="filterTable()">Search</button>
            <button onclick="clearFilter()">Clear</button>
        </div>
        
        <div class="sort-options">
            <label for="sortSelect">Sort by:</label>
            <select id="sortSelect" onchange="sortTable()">
                <option value="name">Name (Alphabetical)</option>
                <option value="cgpa">CGPA</option>
                <option value="dob">DOB</option>
                <option value="department">Department</option>
            </select>
        </div>
        
        <table class="student-table" id="studentTable">
            <thead>
                <tr>
                    <th>Roll No.</th>
                    <th>Name</th>
                    <th>CGPA</th>
                    <th>DOB</th>
                    <th>Department</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['roll_no']}</td>";
                        echo "<td>{$row['name']}</td>";
                        echo "<td>{$row['cgpa']}</td>";
                        echo "<td>{$row['dob']}</td>";
                        echo "<td>{$row['department']}</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No records found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        let debounceTimer;

        function filterTable() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const input = document.getElementById('searchInput').value.toLowerCase();
                const table = document.getElementById('studentTable');
                const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

                for (let i = 0; i < rows.length; i++) {
                    const cells = rows[i].getElementsByTagName('td');
                    let match = false;
                    for (let j = 0; j < cells.length; j++) {
                        if (cells[j].textContent.toLowerCase().indexOf(input) > -1) {
                            match = true;
                            // Highlight matching text
                            const cellText = cells[j].textContent;
                            cells[j].innerHTML = cellText.replace(new RegExp(input, 'gi'), (match) => `<span class="highlight">${match}</span>`);
                        } else {
                            // Reset if no match
                            cells[j].innerHTML = cells[j].textContent;
                        }
                    }
                    rows[i].style.display = match ? '' : 'none';
                }
            }, 300); // Debounce time
        }

        function clearFilter() {
            document.getElementById('searchInput').value = '';
            filterTable();
        }

        function sortTable() {
            const table = document.getElementById('studentTable');
            const rows = Array.from(table.getElementsByTagName('tbody')[0].getElementsByTagName('tr'));
            const sortBy = document.getElementById('sortSelect').value;

            rows.sort((a, b) => {
                const cellA = a.getElementsByTagName('td')[getColumnIndex(sortBy)].textContent;
                const cellB = b.getElementsByTagName('td')[getColumnIndex(sortBy)].textContent;

                if (sortBy === 'name' || sortBy === 'department') {
                    return cellA.localeCompare(cellB);
                } else if (sortBy === 'cgpa' || sortBy === 'dob') {
                    return parseFloat(cellA) - parseFloat(cellB);
                }
                return 0;
            });

            rows.forEach(row => table.getElementsByTagName('tbody')[0].appendChild(row));
        }

        function getColumnIndex(sortBy) {
            switch (sortBy) {
                case 'name':
                    return 1;
                case 'cgpa':
                    return 2;
                case 'dob':
                    return 3;
                case 'department':
                    return 4;
                default:
                    return 0;
            }
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>

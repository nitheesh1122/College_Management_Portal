<?php
// Start the session
session_start();

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

// Initialize search query
$search = '';
if (isset($_POST['search'])) {
    $search = $conn->real_escape_string($_POST['search']);
}

// Fetch received transactions with limit and search filter
$received_sql = "SELECT id, amount, sender, reason, received_at, created_at, account_holder_name, account_number, ifsc_code, bank_name, branch_name 
                 FROM received_amounts 
                 WHERE sender LIKE '%$search%' OR reason LIKE '%$search%' 
                 ORDER BY id DESC LIMIT 25";
$received_result = $conn->query($received_sql);

// Check if data was fetched
if ($received_result === FALSE) {
    die("Error: " . $conn->error);
}

// Fetch sent transactions with limit and search filter
$sent_sql = "SELECT id, amount, receiver, reason, sent_at, created_at, account_holder_name, account_number, ifsc_code, bank_name, branch_name 
             FROM sent_amounts 
             WHERE receiver LIKE '%$search%' OR reason LIKE '%$search%' 
             ORDER BY id DESC LIMIT 25";
$sent_result = $conn->query($sent_sql);

// Check if data was fetched
if ($sent_result === FALSE) {
    die("Error: " . $conn->error);
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction List</title>
    <link rel="stylesheet" href="adminpage.css">
    <style>
       /* General Page Styles */
body {
    background-color: #f8f9fa; /* Lighter background for contrast */
    color: #343a40; /* Darker text color for readability */
    font-family: 'Roboto', sans-serif;
    margin: 0;
    padding: 0;
}

.wrapper {
    max-width: 1200px;
    margin: 20px auto;
    padding: 20px;
    background-color: #ffffff; /* White background for the wrapper */
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
}

h1, h2 {
    text-align: center;
    color: #007bff; /* Primary color */
    margin-bottom: 20px;
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

/* Button and Link Styles */
button, .link {
    background-color: #007bff; /* Primary color */
    border: none;
    color: white;
    padding: 12px 20px;
    text-decoration: none;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    display: inline-block;
    margin: 10px 0;
    border-radius: 8px;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

button:hover, .link:hover {
    background-color: #0056b3; /* Darker blue on hover */
    transform: scale(1.05); /* Slight scale-up */
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

/* Table fade-in animation */
@keyframes fadeIn {
    0% {
        opacity: 0;
    }
    100% {
        opacity: 1;
    }
}

/* Loader Spinner for Charts */
.loader {
    border: 5px solid #f3f3f3;
    border-radius: 50%;
    border-top: 5px solid #007bff; /* Primary color for loader */
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin: 20px auto;
}

/* Scroll fade-in for large elements */
.fade-in {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.5s ease, transform 0.5s ease;
}

.fade-in.visible {
    opacity: 1;
    transform: translateY(0);
}
/* General Transition for All Elements */
* {
    transition: all 0.3s ease;
}

/* Fade-in Animation for Tables */
table {
    animation: fadeIn 1s forwards;
}

/* Button Hover Animation */
button:hover, .link:hover {
    background-color: #0056b3; /* Darker blue on hover */
    transform: scale(1.1); /* Slight scale-up */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Shadow effect */
}

/* Row Hover Animation */
tr:hover {
    background-color: #cce7ff; /* Light blue hover effect */
    transform: translateY(-2px); /* Slight lift effect */
}

/* Input Focus Animation */
.search input[type="text"], .dropdown select {
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.search input[type="text"]:focus, .dropdown select:focus {
    border-color: #007bff; /* Primary color on focus */
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); /* Light blue shadow */
}

/* Table Row Fade-in Animation */
@keyframes rowFadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

tbody tr {
    animation: rowFadeIn 0.5s forwards;
}

/* Delay for Row Animation */
tbody tr:nth-child(1) { animation-delay: 0.1s; }
tbody tr:nth-child(2) { animation-delay: 0.2s; }
tbody tr:nth-child(3) { animation-delay: 0.3s; }
tbody tr:nth-child(4) { animation-delay: 0.4s; }
tbody tr:nth-child(5) { animation-delay: 0.5s; }
/* Add more delays for more rows as needed */

/* Loader Animation */
.loader {
    animation: spin 1s linear infinite;
    transform-origin: center;
}

/* Tooltip Animation */
.tooltip {
    opacity: 0;
    transition: opacity 0.3s ease;
    position: absolute;
    background-color: #343a40;
    color: #fff;
    padding: 5px 10px;
    border-radius: 5px;
    z-index: 1000;
}

.tooltip.visible {
    opacity: 1;
}
.link {
            background-color: #007bff; /* Primary color */
            border: none;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            display: inline-block;
            margin: 10px 0;
            border-radius: 8px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h1>Transaction List</h1>
        <div class="search">
            <form method="post" action="transactions.php">
                <label for="search">Search Transactions:</label>
                <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by sender/receiver or reason">
                <button type="submit" class="link">Search</button>
            </form>
        </div>

        <div class="dropdown">
            <form method="get" action="chart.php">
                <label for="chart-type">Select Chart Type:</label>
                <select id="chart-type" name="chart_type">
                    <option value="pie">Pie Chart</option>
                    <option value="line">Line Graph</option>
                    <option value="bar">Bar Graph</option>
                </select>
                <button type="submit" class="link">View Chart</button>
            </form>
        </div>

        <form method="post" action="download_transactions.php">
            <button type="submit" name="download" class="link">Download Transactions</button>
        </form>
        <form action="adminpage.php" method="get">
            <button type="submit" class="link">Back to Admin Page</button> <!-- Back Button -->
        </form>
        <h2>Received Transactions</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Amount</th>
                    <th>Sender</th>
                    <th>Reason</th>
                    <th>Date Received</th>
                    <th>Date Created</th>
                    <th>Account Holder</th>
                    <th>Account Number</th>
                    <th>IFSC Code</th>
                    <th>Bank Name</th>
                    <th>Branch Name</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($received_result->num_rows > 0): ?>
                    <?php while($row = $received_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td>₹<?php echo number_format($row['amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($row['sender']); ?></td>
                            <td><?php echo htmlspecialchars($row['reason']); ?></td>
                            <td><?php echo htmlspecialchars($row['received_at']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td><?php echo htmlspecialchars($row['account_holder_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['account_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['ifsc_code']); ?></td>
                            <td><?php echo htmlspecialchars($row['bank_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['branch_name']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="11">No records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <h2>Sent Transactions</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Amount</th>
                    <th>Receiver</th>
                    <th>Reason</th>
                    <th>Date Sent</th>
                    <th>Date Created</th>
                    <th>Account Holder</th>
                    <th>Account Number</th>
                    <th>IFSC Code</th>
                    <th>Bank Name</th>
                    <th>Branch Name</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($sent_result->num_rows > 0): ?>
                    <?php while($row = $sent_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td>₹<?php echo number_format($row['amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($row['receiver']); ?></td>
                            <td><?php echo htmlspecialchars($row['reason']); ?></td>
                            <td><?php echo htmlspecialchars($row['sent_at']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td><?php echo htmlspecialchars($row['account_holder_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['account_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['ifsc_code']); ?></td>
                            <td><?php echo htmlspecialchars($row['bank_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['branch_name']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="11">No records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

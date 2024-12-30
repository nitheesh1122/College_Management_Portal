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

// Initialize message and financial variables
$message = "";
$total_received = 0.00;
$total_sent = 0.00;
$profit_loss = 0.00;

// Function to sanitize input
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// (Your validation functions remain unchanged)

// Handle received amount form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['received_amount'])) {
    // (Your form handling logic remains unchanged)
}

// Handle sent amount form submission (add this if you have a similar process for sending amounts)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sent_amount'])) {
    // Add your logic for handling sent amounts here
}

// Fetch total received and sent amounts from the database
$result = $conn->query("SELECT SUM(amount) as total FROM received_amounts");
if ($result) {
    $row = $result->fetch_assoc();
    $total_received = $row['total'] ?? 0.00; // Default to 0.00 if no records
}

$result = $conn->query("SELECT SUM(amount) as total FROM sent_amounts");
if ($result) {
    $row = $result->fetch_assoc();
    $total_sent = $row['total'] ?? 0.00; // Default to 0.00 if no records
}

// Calculate profit/loss
$profit_loss = $total_received - $total_sent;

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amount Forms</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: linear-gradient(135deg, #f0f4f8, #dfe3e8);
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            margin: 20px auto;
            max-width: 1200px;
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            padding: 30px;
            backdrop-filter: blur(8px);
        }

        h1 {
            text-align: center;
            color: #2d3e50;
            margin-bottom: 30px;
            font-weight: bold;
            font-size: 28px;
            letter-spacing: 1px;
        }

        .form-container {
            background: linear-gradient(145deg, #f7f8fa, #dfe4ea);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .form-container:hover {
            transform: translateY(-5px);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2d3e50;
            font-weight: 500;
            font-size: 14px;
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group input[type="password"],
        .form-group select {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ced6e0;
            background-color: #fff;
            font-size: 14px;
            color: #2f3542;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #10ac84;
            box-shadow: 0 0 6px rgba(16, 172, 132, 0.2);
        }

        .btn-primary {
            background-color: #2d98da;
            border: none;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s, transform 0.3s ease, box-shadow 0.3s;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: inline-block;
        }

        .btn-primary:hover {
            background-color: #218c74;
            transform: translateY(-3px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-size: 14px;
        }

        .alert-danger {
            background-color: #ff6b6b;
            color: white;
        }

        .alert-success {
            background-color: #1dd1a1;
            color: white;
        }
        .btn-view-records {
    display: inline-block;
    background-color: #1dd1a1;
    color: white;
    padding: 12px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 16px;
    transition: background-color 0.3s, transform 0.3s ease, box-shadow 0.3s;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin-top: 20px; /* Space above the button */
}

.btn-view-records:hover {
    background-color: #17c1a2; /* Darker shade on hover */
    transform: translateY(-3px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}
.btn-back, .btn-logout {
    display: inline-block;
    background-color: #2d98da; /* Blue for Back button */
    color: white;
    padding: 12px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 16px;
    transition: background-color 0.3s, transform 0.3s ease, box-shadow 0.3s;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin: 10px; /* Space between buttons */
}

.btn-back:hover {
    background-color: #218c74; /* Darker shade on hover */
    transform: translateY(-3px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.btn-logout {
    background-color: #ff6b6b; /* Red for Logout button */
}

.btn-logout:hover {
    background-color: #e84118; /* Darker shade on hover */
    transform: translateY(-3px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}
    </style>
    <script>
        function validateForm() {
            const amount = document.getElementById('amount_received').value;
            const sender = document.getElementById('sender').value;
            const accountHolder = document.getElementById('sender_acc_holder').value;
            const accountNo = document.getElementById('sender_acc_no').value;
            const ifsc = document.getElementById('sender_ifsc').value;
            const bankName = document.getElementById('bank_name').value;
            const branchName = document.getElementById('branch_name').value;
            const reason = document.getElementById('reason_received').value;

            // Validate amount
            const amountPattern = /^\d+(\.\d{1,2})?$/;
            if (!amount || !amountPattern.test(amount) || amount <= 0) {
                alert("Please enter a valid positive amount with up to 2 decimal places.");
                return false;
            }

            // Validate IFSC code format
            const ifscPattern = /^[A-Z]{4}0[A-Z0-9]{6}$/;
            if (!ifscPattern.test(ifsc)) {
                alert("Please enter a valid IFSC code.");
                return false;
            }

            // Validate account number (9 to 18 digits)
            const accountPattern = /^\d{9,18}$/;
            if (!accountPattern.test(accountNo)) {
                alert("Please enter a valid account number (9 to 18 digits).");
                return false;
            }

            // Validate names (only letters, spaces, and hyphens)
            const namePattern = /^[A-Za-z\s-]+$/;
            if (!namePattern.test(sender) || !namePattern.test(accountHolder)) {
                alert("Names can only contain letters, spaces, and hyphens.");
                return false;
            }

            // Validate bank and branch names (letters, spaces, &, ., - and commas)
            const bankBranchPattern = /^[A-Za-z\s&.,-]+$/;
            if (!bankBranchPattern.test(bankName) || !bankBranchPattern.test(branchName)) {
                alert("Bank and Branch names can only contain letters, spaces, &, ., - and commas.");
                return false;
            }

            // Validate reason (letters, numbers, spaces, and some special characters)
            const reasonPattern = /^[A-Za-z0-9\s.,-]+$/;
            if (!reasonPattern.test(reason)) {
                alert("Reason can only contain letters, numbers, spaces, and some special characters.");
                return false;
            }

            // Validate required fields
            if (!sender || !accountHolder || !accountNo) {
                alert("Please fill in all required fields.");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>All India Institute of Ayurveda</h1>

        <?php if (isset($message)) echo $message; ?>

        <h2>Financial Update</h2>
        <p>Total Received: ₹<?php echo number_format($total_received ?? 0, 2); ?></p>
        <p>Total Sent: ₹<?php echo number_format($total_sent ?? 0, 2); ?></p>
        <p>Profit/Loss: ₹<?php echo number_format($profit_loss ?? 0, 2); ?></p>
        <p>Balance: ₹<?php echo number_format($profit_loss ?? 0, 2); ?></p>
        <div class="form-container">
        <a href="transactions.php" class="btn-view-records">View Financial Records</a>
<a href="adminpage.php" class="btn-back">Back</a>
<a href="home.php" class="btn-logout">Logout</a>

        </div>
        <div class="form-container">
            <h1>Received Amount Form</h1>
            <form action="" method="POST" onsubmit="return confirm('Are you sure the information entered is correct?')">
                <div class="form-group">
                    <label for="amount_received">Amount Received:</label>
                    <input type="number" id="amount_received" name="amount" placeholder="Enter amount received" min="0" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="sender">From Whom:</label>
                    <input type="text" id="sender" name="sender" placeholder="Enter sender's name" required>
                </div>
                <div class="form-group">
                    <label for="sender_acc_holder">Sender's Account Holder Name:</label>
                    <input type="text" id="sender_acc_holder" name="sender_account_holder" placeholder="Enter account holder's name" required>
                </div>
                <div class="form-group">
                    <label for="sender_acc_no">Sender's Account Number:</label>
                    <input type="text" id="sender_acc_no" name="sender_account_no" placeholder="Enter account number" required>
                </div>
                <div class="form-group">
                    <label for="sender_ifsc">Sender's IFSC Code:</label>
                    <input type="text" id="sender_ifsc" name="sender_ifsc" placeholder="Enter IFSC code" required>
                </div>
                <div class="form-group">
                    <label for="bank_name">Bank Name:</label>
                    <input type="text" id="bank_name" name="bank_name" placeholder="Enter bank name" required>
                </div>
                <div class="form-group">
                    <label for="branch_name">Branch Name:</label>
                    <input type="text" id="branch_name" name="branch_name" placeholder="Enter branch name" required>
                </div>
                <div class="form-group">
                    <label for="reason_received">Reason:</label>
                    <input type="text" id="reason_received" name="reason" placeholder="Enter reason" required>
                </div>
                <button type="submit" name="received_amount" class="btn-primary">Submit</button>
            </form>
        </div>

        <div class="form-container">
            <h1>Sent Amount Form</h1>
            <form action="" method="POST" onsubmit="return confirm('Are you sure the information entered is correct?')">
                <div class="form-group">
                    <label for="amount_sent">Amount Sent:</label>
                    <input type="number" id="amount_sent" name="amount" placeholder="Enter amount" min="0" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="receiver">To Whom:</label>
                    <input type="text" id="receiver" name="receiver" placeholder="Enter receiver's name" required>
                </div>
                <div class="form-group">
                    <label for="receiver_acc_holder">Receiver's Account Holder Name:</label>
                    <input type="text" id="receiver_acc_holder" name="receiver_account_holder" placeholder="Enter account holder's name" required>
                </div>
                <div class="form-group">
                    <label for="receiver_acc_no">Receiver's Account Number:</label>
                    <input type="text" id="receiver_acc_no" name="receiver_account_no" placeholder="Enter account number" required>
                </div>
                <div class="form-group">
                    <label for="receiver_ifsc">Receiver's IFSC Code:</label>
                    <input type="text" id="receiver_ifsc" name="receiver_ifsc" placeholder="Enter IFSC code" required>
                </div>
                <div class="form-group">
                    <label for="bank_name">Bank Name:</label>
                    <input type="text" id="bank_name" name="bank_name" placeholder="Enter bank name" required>
                </div>
                <div class="form-group">
                    <label for="branch_name">Branch Name:</label>
                    <input type="text" id="branch_name" name="branch_name" placeholder="Enter branch name" required>
                </div>
                <div class="form-group">
                    <label for="reason_sent">Reason:</label>
                    <input type="text" id="reason_sent" name="reason" placeholder="Enter reason" required>
                </div>
                <button type="submit" name="sent_amount" class="btn-primary">Submit</button>
            </form>
        </div>
    </div>
</body>
</html>

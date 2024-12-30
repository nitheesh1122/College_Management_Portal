<?php
// Start the session
session_start();

// Define the correct credentials
define('ADMIN_ID', 'admin123');
define('ADMIN_PASSWORD', 'pass1234');

// Initialize an empty message
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_id = $_POST['admin_id'] ?? '';
    $admin_password = $_POST['admin_password'] ?? '';

    // Check if credentials are correct
    if ($admin_id === ADMIN_ID && $admin_password === ADMIN_PASSWORD) {
        $_SESSION['logged_in'] = true;
        header("Location: adminpage.php"); // Redirect to the admin dashboard
        exit();
    } else {
        $message = "<div class='alert alert-danger'>Invalid ID or Password.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="adminpage.css"> <!-- Link the adminpage stylesheet -->
    <style>
        body {
            background-color: #f0f0f0;
            color: #333;
            font-family: 'Roboto', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            transition: background-color 0.5s ease-in-out;
        }
        
        .login-container {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            padding: 40px;
            width: 400px;
            text-align: center;
            transform: scale(0.9);
            animation: scale-up 0.3s ease-in-out forwards;
        }

        @keyframes scale-up {
            to {
                transform: scale(1);
            }
        }

        .login-container h1 {
            margin-bottom: 25px;
            font-size: 26px;
            font-weight: 700;
            color: #444;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: #555;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 14px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-group input:focus {
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.5);
            outline: none;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            color: white;
            padding: 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 10px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        .alert {
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 14px;
            animation: fade-in 0.5s ease-in-out;
        }

        @keyframes fade-in {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .login-container a {
            color: #007bff;
            text-decoration: none;
            font-size: 14px;
            display: block;
            margin-top: 15px;
            transition: color 0.3s ease;
        }

        .login-container a:hover {
            color: #0056b3;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Admin Login</h1>

        <?php if (!empty($message)) echo $message; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label for="admin_id">Admin ID</label>
                <input type="text" id="admin_id" name="admin_id" required>
            </div>
            <div class="form-group">
                <label for="admin_password">Password</label>
                <input type="password" id="admin_password" name="admin_password" required>
            </div>
            <button type="submit" class="btn-primary">Login</button>
        </form>
    </div>
</body>
</html>

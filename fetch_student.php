<?php
if (isset($_GET['roll_no'])) {
    $roll_no = $_GET['roll_no'];

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

    // Prepare and execute the query
    $stmt = $conn->prepare("SELECT * FROM student_details WHERE roll_no = ?");
    $stmt->bind_param("s", $roll_no);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode($data);
    } else {
        echo json_encode(null);
    }

    // Close the connection
    $stmt->close();
    $conn->close();
}
?>

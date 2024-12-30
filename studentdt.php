<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        body {
            background-color: #f4f4f4; /* Light background */
            color: #343a40; /* Dark text */
            padding: 20px; /* Add some padding for spacing */
            font-family: 'Arial', sans-serif;
            opacity: 0; /* Start with opacity 0 for fade-in */
            animation: fadeIn 1s forwards; /* Fade-in animation */
        }
        .container {
            max-width: 800px;
            background-color: #ffffff; /* White background for container */
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #007bff; /* Primary color */
            margin-bottom: 20px;
        }
        .form-control[readonly] {
            background-color: #e9ecef; /* Light background for readonly fields */
            color: #495057; /* Darker text color */
        }
        .btn {
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.2s, box-shadow 0.3s; /* Added box-shadow transition */
        }
        .btn:hover {
            background-color: #0056b3; /* Darker shade on hover */
            transform: translateY(-2px); /* Slight lift effect */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Add shadow on hover */
        }
        .btn:active {
            transform: translateY(0); /* Reset lift effect on click */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow on click */
        }
        .invalid-feedback {
            display: block; /* Ensure error messages are displayed */
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
        .back-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #007bff;
            text-decoration: none;
            transition: color 0.3s, transform 0.3s; /* Added transform transition */
        }
        .back-link:hover {
            color: #0056b3; /* Darker shade on hover */
            transform: scale(1.05); /* Slight zoom effect */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Student Marks Form</h2>
        <?php
        // PHP code to handle form submission
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['roll_no'], $_POST['name'], $_POST['dob'], $_POST['subject1_grade'], $_POST['subject2_grade'], $_POST['subject3_grade'], $_POST['subject4_grade'], $_POST['subject5_grade'])) {
                $roll_no = $_POST['roll_no'];
                $name = $_POST['name'];
                $dob = $_POST['dob'];
                $subject1_grade = $_POST['subject1_grade'];
                $subject2_grade = $_POST['subject2_grade'];
                $subject3_grade = $_POST['subject3_grade'];
                $subject4_grade = $_POST['subject4_grade'];
                $subject5_grade = $_POST['subject5_grade'];

                // Determine pass/fail for each subject
                $subject_grades = [$subject1_grade, $subject2_grade, $subject3_grade, $subject4_grade, $subject5_grade];
                $passed_subjects = 0;
                $failed_subjects = 0;
                $result = 'Pass'; // Default to Pass

                foreach ($subject_grades as $grade) {
                    if ($grade < 5) {
                        $failed_subjects++;
                        $result = 'Fail'; // Set result to Fail if any subject is failed
                    } else {
                        $passed_subjects++;
                    }
                }

                // Calculate CGPA
                $cgpa = array_sum($subject_grades) / 5;

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

                // Check if the roll number already exists
                $stmt = $conn->prepare("SELECT * FROM student_details WHERE roll_no = ?");
                $stmt->bind_param("s", $roll_no);
                $stmt->execute();
                $result_db = $stmt->get_result();

                if ($result_db->num_rows > 0) {
                    // Roll number exists, update the record
                    $stmt = $conn->prepare("UPDATE student_details SET name = ?, dob = ?, subject1_grade = ?, subject2_grade = ?, subject3_grade = ?, subject4_grade = ?, subject5_grade = ?, result = ?, passed_subjects = ?, failed_subjects = ? WHERE roll_no = ?");
                    $stmt->bind_param("sssssssssss", $name, $dob, $subject1_grade, $subject2_grade, $subject3_grade, $subject4_grade, $subject5_grade, $result, $passed_subjects, $failed_subjects, $roll_no);

                    if ($stmt->execute()) {
                        echo "<div class='alert alert-success'>Record updated successfully</div>";
                    } else {
                        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
                    }
                } else {
                    // Roll number does not exist, insert a new record
                    $stmt = $conn->prepare("INSERT INTO student_details (roll_no, name, dob, subject1_grade, subject2_grade, subject3_grade, subject4_grade, subject5_grade, result, passed_subjects, failed_subjects) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssssssssss", $roll_no, $name, $dob, $subject1_grade, $subject2_grade, $subject3_grade, $subject4_grade, $subject5_grade, $result, $passed_subjects, $failed_subjects);

                    if ($stmt->execute()) {
                        echo "<div class='alert alert-success'>Record inserted successfully</div>";
                    } else {
                        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
                    }
                }

                // Close the connection
                $stmt->close();
                $conn->close();
            } else {
                echo "<div class='alert alert-danger'>Please fill in all fields.</div>";
            }
        }
        ?>
        <form id="studentForm" action="studentdt.php" method="post">
            <div class="mb-3">
                <label for="roll_no" class="form-label">Roll Number:</label>
                <input type="text" class="form-control" id="roll_no" name="roll_no" required>
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Name:</label>
                <input type="text" class="form-control" id="name" name="name" required readonly>
            </div>
            <div class="mb-3">
                <label for="dob" class="form-label">Date of Birth:</label>
                <input type="date" class="form-control" id="dob" name="dob" required readonly>
            </div>
            <div class="mb-3">
                <label for="subject1_grade" class="form-label">Subject 1 Grade (0-10):</label>
                <input type="number" step="0.01" class="form-control" id="subject1_grade" name="subject1_grade" required min="0" max="10">
                <div class="invalid-feedback">
                    Please enter a grade between 0 and 10.
                </div>
            </div>
            <div class="mb-3">
                <label for="subject2_grade" class="form-label">Subject 2 Grade (0-10):</label>
                <input type="number" step="0.01" class="form-control" id="subject2_grade" name="subject2_grade" required min="0" max="10">
                <div class="invalid-feedback">
                    Please enter a grade between 0 and 10.
                </div>
            </div>
            <div class="mb-3">
                <label for="subject3_grade" class="form-label">Subject 3 Grade (0-10):</label>
                <input type="number" step="0.01" class="form-control" id="subject3_grade" name="subject3_grade" required min="0" max="10">
                <div class="invalid-feedback">
                    Please enter a grade between 0 and 10.
                </div>
            </div>
            <div class="mb-3">
                <label for="subject4_grade" class="form-label">Subject 4 Grade (0-10):</label>
                <input type="number" step="0.01" class="form-control" id="subject4_grade" name="subject4_grade" required min="0" max="10">
                <div class="invalid-feedback">
                    Please enter a grade between 0 and 10.
                </div>
            </div>
            <div class="mb-3">
                <label for="subject5_grade" class="form-label">Subject 5 Grade (0-10):</label>
                <input type="number" step="0.01" class="form-control" id="subject5_grade" name="subject5_grade" required min="0" max="10">
                <div class="invalid-feedback">
                    Please enter a grade between 0 and 10.
                </div>
            </div>
            <div class="mb-3">
                <label for="cgpa" class="form-label">Result:</label>
                <input type="text" class="form-control" id="cgpa" name="cgpa" required readonly>
            </div>
            <div class="mb-3">
                <label for="passed_subjects" class="form-label">Passed Subjects:</label>
                <input type="text" class="form-control" id="passed_subjects" name="passed_subjects" required readonly>
            </div>
            <div class="mb-3">
                <label for="failed_subjects" class="form-label">Failed Subjects:</label>
                <input type="text" class="form-control" id="failed_subjects" name="failed_subjects" required readonly>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
        <a href="facultypage.php" class="back-link">Back</a>
    </div>

    <script>
        $(document).ready(function() {
            $('#roll_no').on('blur', function() {
                var rollNo = $(this).val();
                if (rollNo) {
                    $.ajax({
                        url: 'fetch_student.php',
                        type: 'GET',
                        dataType: 'json',
                        data: { roll_no: rollNo },
                        success: function(data) {
                            if (data) {
                                $('#name').val(data.name).prop('readonly', true);
                                $('#dob').val(data.dob).prop('readonly', true);
                                $('#subject1_grade').val(data.subject1_grade);
                                $('#subject2_grade').val(data.subject2_grade);
                                $('#subject3_grade').val(data.subject3_grade);
                                $('#subject4_grade').val(data.subject4_grade);
                                $('#subject5_grade').val(data.subject5_grade);
                                $('#cgpa').val(data.result).prop('readonly', true);
                                $('#passed_subjects').val(data.passed_subjects).prop('readonly', true);
                                $('#failed_subjects').val(data.failed_subjects).prop('readonly', true);
                            } else {
                                $('#name').prop('readonly', false);
                                $('#dob').prop('readonly', false);
                                $('#subject1_grade').val('');
                                $('#subject2_grade').val('');
                                $('#subject3_grade').val('');
                                $('#subject4_grade').val('');
                                $('#subject5_grade').val('');
                                $('#cgpa').val('');
                                $('#passed_subjects').val('');
                                $('#failed_subjects').val('');
                            }
                        },
                        error: function() {
                            alert('Error fetching student data.');
                        }
                    });
                }
            });

            $('#subject1_grade, #subject2_grade, #subject3_grade, #subject4_grade, #subject5_grade').on('input', function() {
                var s1 = parseFloat($('#subject1_grade').val()) || 0;
                var s2 = parseFloat($('#subject2_grade').val()) || 0;
                var s3 = parseFloat($('#subject3_grade').val()) || 0;
                var s4 = parseFloat($('#subject4_grade').val()) || 0;
                var s5 = parseFloat($('#subject5_grade').val()) || 0;
                var grades = [s1, s2, s3, s4, s5];
                
                var passedSubjects = grades.filter(grade => grade >= 5).length;
                var failedSubjects = grades.length - passedSubjects;
                
                var result = failedSubjects > 0 ? 'Fail' : 'Pass';
                var cgpa = (s1 + s2 + s3 + s4 + s5) / 5;

                $('#cgpa').val(result);
                $('#passed_subjects').val(passedSubjects);
                $('#failed_subjects').val(failedSubjects);
            });
        });
    </script>
</body>
</html>

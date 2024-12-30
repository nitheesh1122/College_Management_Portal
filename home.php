<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Information Portal</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            background-color: #f8f9fa;
        }

        header {
            background-color: #004080; /* Primary Color */
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        header .logo img {
            width: 100px;
        }

        header nav ul {
            list-style: none;
            display: flex;
            margin: 0;
            padding: 0;
        }

        header nav ul li {
            margin-left: 20px;
        }

        header nav ul li a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            padding: 5px 10px;
            transition: background-color 0.3s ease;
        }

        header nav ul li a:hover {
            background-color: black;
            border-radius: 10px;
        }

        .banner {
            background-image: url('campus.jpg'); /* Background image */
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 150px 20px;
            position: relative;
            margin-top: 80px; /* Adjusted for fixed header */
            z-index: 1;
        }

        .banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4); /* Semi-transparent overlay */
            z-index: -1;
        }

        .banner h1 {
            font-size: 3rem;
            margin: 0;
        }

        .banner p {
            font-size: 1.5rem;
            margin-top: 10px;
        }

        .about {
            padding: 80px 20px;
            text-align: center;
            background-color: #fff;
        }

        .about h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        .about p {
            font-size: 1.2rem;
            color: #666;
        }

        .quick-access {
            display: flex;
            justify-content: space-around;
            padding: 60px 20px;
            background-color: #f4f4f4;
        }

        .quick-access .tile {
            text-align: center;
            width: 20%;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .quick-access .tile img {
            width: 60px;
            height: 60px;
        }

        .quick-access .tile a {
            display: block;
            margin-top: 10px;
            text-decoration: none;
            color: #004080;
            font-weight: bold;
        }

        .quick-access .tile:hover {
            transform: translateY(-10px);
        }

        .announcements {
            padding: 80px 20px;
            background-color: #e0e0e0;
            text-align: center;
        }

        .announcements h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        .announcements p {
            font-size: 1.2rem;
            color: #666;
        }

        footer {
            background-color: #004080;
            color: #fff;
            padding: 30px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        footer .footer-links a {
            color: #fff;
            margin-right: 15px;
            text-decoration: none;
            font-size: 1rem;
        }

        footer .contact-info p {
            margin: 5px 0;
        }

        footer .social-media a img {
            width: 30px;
            margin-left: 10px;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                text-align: center;
            }

            .banner {
                padding: 100px 20px;
            }

            .quick-access {
                flex-direction: column;
                align-items: center;
            }

            .quick-access .tile {
                width: 80%;
                margin-bottom: 20px;
            }

            footer {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <header>
        <div class="logo">
            <img src="logo.png" alt="College Logo">
        </div>
        <nav>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#">Annual Reports</a></li>
                <li><a href="#">Student Performance</a></li>
                <li><a href="#">Staff Information</a></li>
                <li><a href="#">Student Information</a>
                    <div class="offcanvas offcanvas-end text-bg-dark3" id="demo">
                        <div class="offcanvas-header">
                            <h1 class="offcanvas-title">AYUSH</h1>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
                        </div>
                        <div class="offcanvas-body">
                            <a href="admin_login.php"><button class="btn btn-secondary" type="button">ADMIN</button></a><br><br>
                            <a href="facultylogin.php"><button class="btn btn-secondary" type="button">FACULTY</button></a><br><br>
                            <a href="studentlogin.php"><button class="btn btn-secondary" type="button">STUDENT</button></a><br>
                        </div>
                    </div>
                    <div class="container-fluid mt-3">
                        <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#demo" aria-placeholder="required">
                            Account Login
                        </button>
                    </div>
                </li>
            </ul>
        </nav>
    </header>
    
    <!-- Banner Section -->
    <section class="banner">
        <div class="banner-content">
            <h1>Welcome to Institute of Ayush Information Portal</h1>
            <p>Access annual reports, performance records, and essential information at your fingertips.</p>
        </div>
    </section>

    <!-- About Section -->
    <section class="about">
        <h2>About Us</h2>
        <p>This portal provides transparent access to the college's annual reports, performance statistics, and essential information for both students and staff.</p>
    </section>

    <!-- Quick Access Tiles Section -->
    <section class="quick-access">
        <div class="tile">
            <img src="annual.png" alt="Annual Reports">
            <a href="#">Annual Reports</a>
        </div>
        <div class="tile">
            <img src="studentper.png" alt="Student Performance">
            <a href="#">Student Performance</a>
        </div>
        <div class="tile">
            <img src="staffs.png" alt="Staff Information">
            <a href="#">Staff Information</a>
        </div>
        <div class="tile">
        <a href="student_details_home.php?roll_no=12345"><img src="studentsinfo.png" alt="Student Information"><br>
            View Student Details</a>
        </div>
    </section>

    <!-- Announcements Section -->
    <section class="announcements">
        <h2>Latest Updates & Announcements</h2>
        <p>Stay updated with the latest news and announcements from our college.</p>
        <!-- Dynamic content can be added here -->
    </section>

    <!-- Footer Section -->
    <footer>
        <div class="footer-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
            <a href="#">FAQs</a>
        </div>
        <div class="contact-info">
            <p>Contact Us: Phone: +91 98765 43210
Email: info@ayushinstituteindia.in
</p>
            <p>Address: AYUSH Institute of India
123 Wellness Road,
Sector 9, Knowledge Park,
New Delhi, DL 110001,
India</p>
        </div>
        <div class="social-media">
            <a href="#"><img src="fb.png" alt="Facebook"></a>
            <a href="#"><img src="X.png" alt="Twitter"></a>
            <a href="#"><img src="ln.png" alt="LinkedIn"></a>
        </div>
    </footer>
</body>
</html>

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

// Get chart type from query string
$chartType = isset($_GET['chart_type']) ? $_GET['chart_type'] : 'line'; // Default to 'line'

// Fetch data for charts
$data_sql = "SELECT amount, received_at FROM received_amounts ORDER BY received_at";
$data_result = $conn->query($data_sql);

// Prepare data for chart
$data = [];
if ($data_result->num_rows > 0) {
    while ($row = $data_result->fetch_assoc()) {
        $data[] = ['amount' => $row['amount'], 'date' => $row['received_at']];
    }
} else {
    $data = [];
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chart Visualization</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        h1 {
            text-align: center;
            color: #007bff; /* Primary color */
            margin-bottom: 20px;
        }
        .link, .back-button {
            text-decoration: none;
            color: #007bff; /* Link color */
            font-weight: bold;
            display: block;
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 8px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .link:hover, .back-button:hover {
            background-color: #0056b3; /* Darker blue on hover */
            color: white; /* White text on hover */
        }
        canvas {
            width: 100% !important;
            height: 600px !important;
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.7s ease-out, transform 0.7s ease-out;
        }
        /* Loading Spinner */
        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            opacity: 1;
            transition: opacity 0.5s ease;
        }
        .loading.hidden {
            opacity: 0;
            pointer-events: none;
        }
        .spinner {
            border: 8px solid #f3f3f3; /* Light grey */
            border-top: 8px solid #007bff; /* Primary color */
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 2s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="loading" id="loading">
        <div class="spinner"></div>
    </div>
    <div class="wrapper" id="chartContainer">
        <h1>Chart Visualization</h1>
        <a href="transactions.php" class="link">Back to Transactions</a>
        <a href="javascript:history.back()" class="back-button">Go Back</a>
        <canvas id="myChart"></canvas>
        
        <script>
            // Data for the chart
            const data = <?php echo json_encode($data); ?>;
            const labels = data.map(item => item.date);
            const values = data.map(item => item.amount);

            // Configuration for Chart.js
            const ctx = document.getElementById('myChart').getContext('2d');
            const chartType = '<?php echo htmlspecialchars($chartType); ?>';

            const config = {
                type: chartType,
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Transaction Amount',
                        data: values,
                        backgroundColor: 'rgba(75, 192, 192, 0.5)',
                        borderColor: 'rgba(255, 204, 0, 1)', // Yellow border color
                        borderWidth: 1,
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        tension: {
                            duration: 500,
                            easing: 'easeInOutQuart',
                            from: 0.5,
                            to: 0,
                            loop: true
                        },
                        onComplete: function() {
                            const chartInstance = this.chart;
                            const ctx = chartInstance.ctx;
                            ctx.save();
                            ctx.globalAlpha = 0.5;
                            ctx.fillStyle = 'rgba(255, 255, 255, 0.2)';
                            ctx.fillRect(0, 0, chartInstance.width, chartInstance.height);
                            ctx.restore();
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Date',
                                color: '#343a40' // Dark text color
                            },
                            grid: {
                                color: '#dee2e6' // Light grid color
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Amount',
                                color: '#343a40' // Dark text color
                            },
                            grid: {
                                color: '#dee2e6' // Light grid color
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            backgroundColor: 'rgba(255, 255, 255, 0.9)',
                            titleColor: '#000',
                            bodyColor: '#000',
                            borderColor: '#ccc',
                            borderWidth: 1,
                            displayColors: false,
                            padding: 10,
                        },
                        legend: {
                            onHover: function(event, legendItem) {
                                const chartInstance = this.chart;
                                const datasetIndex = legendItem.datasetIndex;

                                chartInstance.data.datasets[datasetIndex].backgroundColor = 'rgba(255, 99, 132, 0.5)';
                                chartInstance.update();
                            },
                            onLeave: function(event, legendItem) {
                                const chartInstance = this.chart;
                                const datasetIndex = legendItem.datasetIndex;

                                chartInstance.data.datasets[datasetIndex].backgroundColor = 'rgba(75, 192, 192, 0.5)';
                                chartInstance.update();
                            }
                        }
                    }
                }
            };

            // Create the chart
            const myChart = new Chart(ctx, config);

            // Hide loading spinner after the chart is created
            window.addEventListener('load', function() {
                document.getElementById('loading').classList.add('hidden');
                document.getElementById('chartContainer').style.opacity = 1;
                document.getElementById('chartContainer').style.transform = 'translateY(0)';
            });

            // Scroll animation for chart
            window.addEventListener('scroll', function() {
                const chart = document.getElementById('myChart');
                const chartPosition = chart.getBoundingClientRect().top;
                const screenPosition = window.innerHeight / 1.5;

                if (chartPosition < screenPosition) {
                    chart.style.opacity = 1;
                    chart.style.transform = 'translateY(0)';
                } else {
                    chart.style.opacity = 0;
                    chart.style.transform = 'translateY(20px)';
                }
            });

            // Trigger the scroll event on load
            window.dispatchEvent(new Event('scroll'));
        </script>
    </div>
</body>
</html>

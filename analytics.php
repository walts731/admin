<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DELIDAZE Analytics Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
            color: #333;
            font-family: Arial, sans-serif;
        }
        .chart-container {
            margin: 20px 0;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
        }
        .chart-title {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .chart {
            width: 100%;
            height: 400px;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <?php include ('include/nav.php')?>


    <!-- Analytics Dashboard -->
    <div class="container mt-4">
        <h1 class="text-center">Analytics Dashboard</h1>

        <!-- Sales Performance Chart -->
        <div class="chart-container">
            <h2 class="chart-title">Sales Performance (Last 6 Months)</h2>
            <canvas id="salesChart" class="chart"></canvas>
        </div>

        <!-- Customer Acquisition Chart -->
        <div class="chart-container">
            <h2 class="chart-title">Customer Acquisition</h2>
            <canvas id="customerChart" class="chart"></canvas>
        </div>

        <!-- Top Products Chart -->
        <div class="chart-container">
            <h2 class="chart-title">Top Selling Products</h2>
            <canvas id="productsChart" class="chart"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Sales Performance Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: ['April', 'May', 'June', 'July', 'August', 'September'],
                datasets: [{
                    label: 'Sales (₱)',
                    data: [5000, 10000, 15000, 20000, 30000, 25000],
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    fill: true,
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Customer Acquisition Chart
        const customerCtx = document.getElementById('customerChart').getContext('2d');
        const customerChart = new Chart(customerCtx, {
            type: 'doughnut',
            data: {
                labels: ['New Customers', 'Returning Customers'],
                datasets: [{
                    label: 'Customers',
                    data: [60, 40],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)'
                    ],
                    borderWidth: 1
                }]
            }
        });

        // Top Selling Products Chart
        const productsCtx = document.getElementById('productsChart').getContext('2d');
        const productsChart = new Chart(productsCtx, {
            type: 'bar',
            data: {
                labels: ['Product A', 'Product B', 'Product C', 'Product D', 'Product E'],
                datasets: [{
                    label: 'Units Sold',
                    data: [55, 45, 40, 35, 25],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>

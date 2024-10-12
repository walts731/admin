<?php include ('include/connect.php')?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DELIDAZE Analytics Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #D6EFD8; /* Set the background color to #FFFFE0 */
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
        /* Style for the bar chart */
        .bar-chart {
            width: 100%; /* Adjust width as needed */
            height: 400px; /* Adjust height as needed */
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
            <h2 class="chart-title">Sales Performance</h2>
            <select id="yearSelect">
                <?php
                // Fetch available years from the database for the dropdown
                $yearsQuery = "SELECT DISTINCT YEAR(archived_at) AS year FROM orders_history ORDER BY year DESC";
                $yearsResult = $conn->query($yearsQuery);
                if ($yearsResult->num_rows > 0) {
                    while ($yearRow = $yearsResult->fetch_assoc()) {
                        $year = $yearRow['year'];
                        echo "<option value='$year'>$year</option>";
                    }
                }
                ?>
            </select>
            <canvas id="salesChart" class="chart"></canvas>
        </div>

        <!-- Customer Acquisition Chart (Bar Graph) -->
        <div class="chart-container">
            <h2 class="chart-title">Customer Acquisition</h2>
            <canvas id="customerChart" class="bar-chart"></canvas> 
        </div>


        <!-- Top Products Chart -->
        <div class="chart-container">
            <h2 class="chart-title">Top Selling Products</h2>
            <canvas id="productsChart" class="chart"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <?php
    

// Set the desired year for which you want to display the sales data
$year = date('Y'); // You can change this to any year you want to analyze

// Fetch total sales for each month of the specified year
$salesQuery = "
    SELECT 
        MONTH(archived_at) AS month, 
        COALESCE(SUM(total_price), 0) AS total_sales
    FROM 
        orders_history
    WHERE 
        YEAR(archived_at) = $year
    GROUP BY 
        month
    ORDER BY 
        month
";

$result = $conn->query($salesQuery);

// Initialize arrays to hold sales data and labels
$salesData = array_fill(0, 12, 0); // Initialize all months with 0
$salesLabels = ['January', 'February', 'March', 'April', 'May', 'June', 
                'July', 'August', 'September', 'October', 'November', 'December'];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $salesData[$row['month'] - 1] = (float)$row['total_sales']; // Store sales data by month (0-indexed)
    }
} 


    // Fetch top-selling product data from 'order_items' and 'products' tables
    $productsQuery = "
    SELECT p.product_name, SUM(oh.quantity) as total_sold
    FROM orders_history oh
    JOIN products p ON oh.product_id = p.product_id
    WHERE oh.status = 'completed'
    GROUP BY p.product_name
    ORDER BY total_sold DESC
    LIMIT 5";

    
    $productsResult = $conn->query($productsQuery);

    $productNames = [];
    $productUnitsSold = [];

    if ($productsResult->num_rows > 0) {
        while ($row = $productsResult->fetch_assoc()) {
            // Push the product names and total units sold to arrays
            $productNames[] = $row['product_name'];
            $productUnitsSold[] = $row['total_sold'];
        }
    } else {
        // Default values if no data is available
        $productNames = ['Product A', 'Product B', 'Product C', 'Product D', 'Product E'];
        $productUnitsSold = [0, 0, 0, 0, 0];
    }

    // Fetch customer acquisition data
    // Count new customers from the last 30 days
    $newCustomerQuery = "SELECT COUNT(*) as new_customers FROM users WHERE created_at >= NOW() - INTERVAL 30 DAY";
    $newCustomerResult = $conn->query($newCustomerQuery);
    $newCustomers = $newCustomerResult->fetch_assoc()['new_customers'] ?? 0;

    // Count returning customers (you can define returning as users who have placed more than one order)
    $returningCustomerQuery = "
    SELECT COUNT(DISTINCT u.user_id) as returning_customers
    FROM users u
    JOIN orders_history oh ON u.user_id = oh.user_id
    WHERE oh.status = 'completed'
    GROUP BY u.user_id
    HAVING COUNT(oh.order_id) > 1";

    
    $returningCustomerResult = $conn->query($returningCustomerQuery);
    $returningCustomers = $returningCustomerResult->num_rows ?? 0;

    // Close the database connection
    $conn->close();
    ?>

    <script>
        // Pass PHP data to JavaScript for sales
const salesData = <?php echo json_encode($salesData); ?>;
const salesLabels = <?php echo json_encode($salesLabels); ?>;

// Sales Performance Chart
const salesCtx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(salesCtx, {
    type: 'bar', // Change to 'bar' for better visibility by month
    data: {
        labels: salesLabels, // Dynamically set month labels
        datasets: [{
            label: 'Sales (₱)',
            data: salesData, // Dynamically set sales data
            backgroundColor: 'rgba(0, 128, 0, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1,
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Sales (₱)'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Months'
                }
            }
        }
    }
});

        // Pass PHP data to JavaScript for customer acquisition
        const newCustomers = <?php echo json_encode($newCustomers); ?>;
        const returningCustomers = <?php echo json_encode($returningCustomers); ?>;

        // Customer Acquisition Chart (Bar Graph)
        const customerCtx = document.getElementById('customerChart').getContext('2d');
        const customerChart = new Chart(customerCtx, {
            type: 'bar',
            data: {
                labels: ['New Customers', 'Returning Customers'],
                datasets: [{
                    label: 'Customers',
                    data: [newCustomers, returningCustomers], // Dynamically set customer data
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.2)', // Light green
                        'rgba(153, 255, 153, 0.2)' // Lighter green
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',   // Darker green
                        'rgba(0, 128, 0, 1)'      // Strong green
                    ],

                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Customers'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Customer Type'
                        }
                    }
                }
            }
        });

        // Pass PHP data to JavaScript for top-selling products
        const productNames = <?php echo json_encode($productNames); ?>;
        const productUnitsSold = <?php echo json_encode($productUnitsSold); ?>;

        // Top Selling Products Chart
        const productsCtx = document.getElementById('productsChart').getContext('2d');
        const productsChart = new Chart(productsCtx, {
            type: 'bar',
            data: {
                labels: productNames, // Dynamically set product labels
                datasets: [{
                    label: 'Units Sold',
                    data: productUnitsSold, // Dynamically set units sold data
                    backgroundColor: [
                        'rgba(144, 238, 144, 0.2)', // Light green
                        'rgba(60, 179, 113, 0.2)',  // Medium sea green
                        'rgba(34, 139, 34, 0.2)',    // Forest green
                        'rgba(0, 128, 0, 0.2)',      // Green
                        'rgba(0, 255, 0, 0.2)'       // Lime green
                    ],
                    borderColor: [
                        'rgba(144, 238, 144, 1)', // Light green
                        'rgba(60, 179, 113, 1)',  // Medium sea green
                        'rgba(34, 139, 34, 1)',    // Forest green
                        'rgba(0, 128, 0, 1)',      // Green
                        'rgba(0, 255, 0, 1)'       // Lime green
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

        // Event listener for the year selection dropdown
        document.getElementById('yearSelect').addEventListener('change', function() {
            const selectedYear = this.value;
            fetchSalesData(selectedYear); // Fetch sales data for the selected year
        });

        // Function to fetch sales data for the selected year
        function fetchSalesData(selectedYear) {
            fetch('fetch_sales_data.php?year=' + selectedYear) // Replace with your actual PHP file
                .then(response => response.json())
                .then(data => {
                    salesChart.data.datasets[0].data = data.salesData; // Update sales data
                    salesChart.update(); // Update the chart
                })
                .catch(error => {
                    console.error('Error fetching sales data:', error);
                });
        }
    </script>
</body>
</html>
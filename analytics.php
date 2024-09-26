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
    
    <?php
    

    // Fetch sales data from the 'sales' table for the last 6 months
    $salesQuery = "SELECT total_price, sale_date FROM sales ORDER BY sale_date DESC LIMIT 6";
    $result = $conn->query($salesQuery);

    $salesData = [];
    $salesLabels = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Push the total_price and formatted sale_date to arrays
            $salesData[] = $row['total_price'];
            $salesLabels[] = date('F', strtotime($row['sale_date'])); // Format date as month name
        }
    } else {
        // If no sales data is available, set default values
        $salesData = [0, 0, 0, 0, 0, 0];
        $salesLabels = ['January', 'February', 'March', 'April', 'May', 'June'];
    }

    // Fetch top-selling product data from 'order_items' and 'products' tables
    $productsQuery = "
        SELECT p.product_name, SUM(oi.quantity) as total_sold
        FROM order_items oi
        JOIN products p ON oi.product_id = p.product_id
        JOIN orders o ON oi.order_id = o.order_id
        WHERE o.status = 'completed'
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
        JOIN orders o ON u.user_id = o.user_id
        WHERE o.status = 'completed'
        GROUP BY u.user_id
        HAVING COUNT(o.order_id) > 1";
    
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
            type: 'line',
            data: {
                labels: salesLabels, // Dynamically set month labels
                datasets: [{
                    label: 'Sales (₱)',
                    data: salesData, // Dynamically set sales data
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

        // Pass PHP data to JavaScript for customer acquisition
        const newCustomers = <?php echo json_encode($newCustomers); ?>;
        const returningCustomers = <?php echo json_encode($returningCustomers); ?>;

        // Customer Acquisition Chart
        const customerCtx = document.getElementById('customerChart').getContext('2d');
        const customerChart = new Chart(customerCtx, {
            type: 'doughnut',
            data: {
                labels: ['New Customers', 'Returning Customers'],
                datasets: [{
                    label: 'Customers',
                    data: [newCustomers, returningCustomers], // Dynamically set customer data
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

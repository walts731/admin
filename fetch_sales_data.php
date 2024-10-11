<?php
// Database connection (replace with your actual connection details)
include ('include/connect.php');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the selected year from the URL parameter
$selectedYear = $_GET['year'];

// Fetch sales data for the selected year
$salesQuery = "
    SELECT 
        MONTH(order_date) AS month, 
        COALESCE(SUM(total_price), 0) AS total_sales
    FROM 
        orders_history
    WHERE 
        YEAR(order_date) = '$selectedYear'
    GROUP BY 
        month
    ORDER BY 
        month
";

$result = $conn->query($salesQuery);

// Initialize arrays to hold sales data
$salesData = array_fill(0, 12, 0); // Initialize all months with 0

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $salesData[$row['month'] - 1] = (float)$row['total_sales']; // Store sales data by month (0-indexed)
    }
}

// Close the database connection
$conn->close();

// Send the sales data as a JSON response
header('Content-type: application/json');
echo json_encode(['salesData' => $salesData]);
?>
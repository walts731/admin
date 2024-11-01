<?php
// Database connection
include ('include/connect.php');

// Get the order ID from the query parameter
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';

// Fetch order items
$sql = "SELECT oh.order_item_id, p.product_name, oh.quantity, oh.price 
        FROM orders_history oh
        JOIN products p ON oh.product_id = p.product_id
        WHERE oh.order_id = '$order_id'";
$result = $conn->query($sql);

// Display order items in a table
if ($result->num_rows > 0) {
    echo "<h3>Order Items for Order #" . $order_id . "</h3>";
    echo "<table border='1'>";
    echo "<tr><th>Order Item ID</th><th>Product Name</th><th>Quantity</th><th>Price</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["order_item_id"] . "</td>";
        echo "<td>" . $row["product_name"] . "</td>";
        echo "<td>" . $row["quantity"] . "</td>";
        echo "<td>" . $row["price"] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No order items found for this order.";
}
?>
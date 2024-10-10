<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders History Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/product.css">
</head>

<body>
    <!-- Navigation Bar -->
    <?php include ('include/nav.php'); ?>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Orders History</h1>

        <?php
        include('include/connect.php');

        // SQL query to fetch data from orders_history table
        $sql = "SELECT `history_id`, `order_id`, `user_id`, `total_price`, `status`, `order_date`, `archived_at`, `order_item_id`, `product_id`, `quantity`, `price` FROM `orders_history`";
        $result = $conn->query($sql);

        // Check if there are results
        if ($result->num_rows > 0) {
            // Output data of each row
            echo '<table class="table table-striped">';
            echo '<thead class="table-light">
                    <tr>
                        <th>History ID</th>
                        <th>Order ID</th>
                        <th>User ID</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Order Date</th>
                        <th>Archived At</th>
                        <th>Order Item ID</th>
                        <th>Product ID</th>
                        <th>Quantity</th>
                        <th>Price</th>
                    </tr>
                  </thead>';
            echo '<tbody>';
            
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row["history_id"] . "</td>
                        <td>" . $row["order_id"] . "</td>
                        <td>" . $row["user_id"] . "</td>
                        <td>" . number_format($row["total_price"], 2) . "</td>
                        <td>" . $row["status"] . "</td>
                        <td>" . $row["order_date"] . "</td>
                        <td>" . $row["archived_at"] . "</td>
                        <td>" . $row["order_item_id"] . "</td>
                        <td>" . $row["product_id"] . "</td>
                        <td>" . $row["quantity"] . "</td>
                        <td>" . number_format($row["price"], 2) . "</td>
                      </tr>";
            }
            
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<div class="alert alert-warning" role="alert">No results found.</div>';
        }

        // Close the connection
        $conn->close();
        ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

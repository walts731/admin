<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders History Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/product.css">
</head>

<body style="background-color: #D6EFD8;">
    <!-- Navigation Bar -->
    <?php include ('include/nav.php'); ?>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Orders History</h1>

        <?php
        include('include/connect.php');

        // SQL query to fetch data from orders_history table, sorted by order_date in descending order (newest first)
        $sql = "SELECT 
                    `oh`.`history_id`, 
                    `oh`.`order_id`, 
                    `oh`.`user_id`, 
                    `oh`.`total_price`, 
                    `oh`.`status`, 
                    `oh`.`order_date`, 
                    `oh`.`archived_at`, 
                    `oh`.`order_item_id`, 
                    `oh`.`product_id`, 
                    `oh`.`quantity`, 
                    `oh`.`price`,
                    `p`.`product_name` AS `product_name`,
                    `u`.`username` AS `username` 
                FROM 
                    `orders_history` AS `oh`
                JOIN 
                    `products` AS `p` ON `oh`.`product_id` = `p`.`product_id`
                JOIN 
                    `users` AS `u` ON `oh`.`user_id` = `u`.`user_id`
                ORDER BY `oh`.`order_date` DESC"; // Added ORDER BY clause
        $result = $conn->query($sql);

        // Check if there are results
        if ($result->num_rows > 0) {
            // Output data of each row
            echo '<div class="table-responsive">';
            echo '<table class="table table-striped table-bordered">';
            echo '<thead class="" style="background-color: #508D4E; color: white;">

                    <tr>
                        <th>Order ID</th>
                        <th>Username</th>
                        <th>Status</th>
                        <th>Order Date</th>
                        <th>Archived At</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Total Amount</th> 
                    </tr>
                  </thead>';
            echo '<tbody>';
            
            while ($row = $result->fetch_assoc()) {
                // Format the date using php's date function
                $order_date = date('M d Y h:ia', strtotime($row["order_date"]));
                $archived_at = date('M d Y h:ia', strtotime($row["archived_at"]));

                // Calculate the total amount for the product
                $total_amount = $row["quantity"] * $row["price"]; 

                echo "<tr>
                        <td>" . $row["order_id"] . "</td>
                        <td>" . $row["username"] . "</td>
                        <td style='color: green;'>" . $row["status"] . "</td>
                        <td>" . $order_date . "</td>
                        <td>" . $archived_at . "</td>
                        <td>" . $row["product_name"] . "</td>
                        <td>" . $row["quantity"] . "</td>
                        <td>" . number_format($total_amount, 2) . "</td>
                      </tr>";
            }
            
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body style="background-color: #D6EFD8;">
    <!-- Navigation Bar -->
    <?php include('include/nav.php') ?>
        <div class="container mt-5">
        <h2 class="text-center mb-4">Search Results</h2>
        <?php
        include 'include/connect.php';

        // Get the search term from the URL parameters
        $searchTerm = $_GET['search'];

        // Sanitize the search term to prevent SQL injection
        $searchTerm = mysqli_real_escape_string($conn, $searchTerm);

        // Determine the search target based on the URL parameter
        $searchTarget = isset($_GET['target']) ? $_GET['target'] : 'product_name'; // Default to 'product_name'

        // SQL query to search for orders based on archived_at
        if ($searchTarget === 'archived_at') {
            $sql = "SELECT oh.`history_id`, oh.`order_id`, oh.`user_id`, oh.`total_price`, oh.`status`, oh.`order_date`, oh.`archived_at`, oh.`order_item_id`, oh.`product_id`, oh.`quantity`, oh.`price`, oh.`shipping_address`, oh.`payment_method`, oh.`reference_number`
                    FROM `orders_history` oh
                    WHERE oh.`archived_at` LIKE '%" . $searchTerm . "%'";
        } elseif ($searchTarget === 'username') { // Search for username
            $sql = "SELECT `user_id`, `username`, `password`, `email`, `role`, `created_at`, `status` 
                    FROM `users` 
                    WHERE `username` LIKE '%" . $searchTerm . "%'";
        } else {
            // Fallback to search by product_name (original query from previous response)
            $sql = "SELECT p.`product_id`, p.`product_name`, p.`product_description`, p.`price`, p.`image_url`, p.`created_at`, i.`stock`, i.`cost`, i.`last_updated`
                    FROM `products` p
                    JOIN `inventory` i ON p.`product_id` = i.`product_id`
                    WHERE p.`product_name` LIKE '%" . $searchTerm . "%'";
        }

        // Execute the query
        $result = $conn->query($sql);

        // Check if any results were found
        if ($result->num_rows > 0) {
            // Output the search results
            echo "<table class='table table-striped table-bordered'>
                <thead>";

            // Determine table headers based on search target
            if ($searchTarget === 'archived_at') {
                echo "<tr>
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
                        <th>Shipping Address</th>
                        <th>Payment Method</th>
                        <th>Reference Number</th>
                    </tr>";
            } elseif ($searchTarget === 'username') {
                echo "<tr>
                        <th>User ID</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created At</th>
                        <th>Status</th>
                    </tr>";
            } else {
                echo "<tr>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Image URL</th>
                        <th>Created At</th>
                        <th>Stock</th>
                        <th>Cost</th>
                        <th>Last Updated</th>
                    </tr>";
            }

            echo "</thead>
                <tbody>";

            // Loop through each row of results
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                if ($searchTarget === 'archived_at') {
                    echo "<td>" . $row["history_id"] . "</td>";
                    echo "<td>" . $row["order_id"] . "</td>";
                    echo "<td>" . $row["user_id"] . "</td>";
                    echo "<td>" . $row["total_price"] . "</td>";
                    echo "<td>" . $row["status"] . "</td>";
                    echo "<td>" . $row["order_date"] . "</td>";
                    echo "<td>" . $row["archived_at"] . "</td>";
                    echo "<td>" . $row["order_item_id"] . "</td>";
                    echo "<td>" . $row["product_id"] . "</td>";
                    echo "<td>" . $row["quantity"] . "</td>";
                    echo "<td>" . $row["price"] . "</td>";
                    echo "<td>" . $row["shipping_address"] . "</td>";
                    echo "<td>" . $row["payment_method"] . "</td>";
                    echo "<td>" . $row["reference_number"] . "</td>";
                } elseif ($searchTarget === 'username') {
                    echo "<td>" . $row["user_id"] . "</td>";
                    echo "<td>" . $row["username"] . "</td>";
                    echo "<td>" . $row["password"] . "</td>";
                    echo "<td>" . $row["email"] . "</td>";
                    echo "<td>" . $row["role"] . "</td>";
                    echo "<td>" . $row["created_at"] . "</td>";
                    echo "<td>" . $row["status"] . "</td>";
                } else {
                    echo "<td>" . $row["product_id"] . "</td>";
                    echo "<td>" . $row["product_name"] . "</td>";
                    echo "<td>" . $row["product_description"] . "</td>";
                    echo "<td>" . $row["price"] . "</td>";
                    echo "<td>" . $row["image_url"] . "</td>";
                    echo "<td>" . $row["created_at"] . "</td>";
                    echo "<td>" . $row["stock"] . "</td>";
                    echo "<td>" . $row["cost"] . "</td>";
                    echo "<td>" . $row["last_updated"] . "</td>";
                }
                echo "</tr>";
            }
            echo "</tbody>
            </table>";
        } else {
            echo "<p class='text-center'>No results found matching the search term.</p>";
        }

        // Close the database connection
        $conn->close();
        ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
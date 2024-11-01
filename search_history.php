<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders History Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/product.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
</head>

<body style="background-color: #D6EFD8;">
    <!-- Navigation Bar -->
    <?php include ('include/nav.php'); ?>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Orders History</h1>

        <!-- Search Bar -->
        <div class="input-group mb-3">
            <input type="text" class="form-control" id="searchInput" placeholder="Search by any column...">
            <button class="btn btn-outline-secondary" type="button" id="searchButton">Search</button>
        </div>

        <?php
        include('include/connect.php');

        // SQL query to fetch data from orders_history table
        $sql = "SELECT 
                    `history_id`, 
                    `order_id`, 
                    `user_id`, 
                    `total_price`, 
                    `status`, 
                    `order_date`, 
                    `archived_at`, 
                    `order_item_id`, 
                    `product_id`, 
                    `quantity`, 
                    `price`, 
                    `shipping_address`, 
                    `payment_method`, 
                    `reference_number` 
                FROM 
                    `orders_history`"; 

        $result = $conn->query($sql);

        // Check if there are results
        if ($result->num_rows > 0) {
            // Output data of each row
            echo '<div class="table-responsive">';
            echo '<table class="table table-striped table-bordered" id="ordersTable">';
            echo '<thead class="" style="background-color: #508D4E; color: white;">
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
                        <th>Shipping Address</th>
                        <th>Payment Method</th>
                        <th>Reference Number</th>
                    </tr>
                  </thead>';
            echo '<tbody>';

            while ($row = $result->fetch_assoc()) : 
                // Format the order date
                $formattedOrderDate = date('M d, Y', strtotime($row['order_date']));
            ?>
                <tr>
                    <td><?= $row['history_id'] ?></td>
                    <td><?= $row['order_id'] ?></td>
                    <td><?= $row['user_id'] ?></td>
                    <td><?= $row['total_price'] ?></td>
                    <td><?= $row['status'] ?></td>
                    <td><?= $formattedOrderDate ?></td> <!-- Display formatted order_date -->
                    <td><?= $row['archived_at'] ?></td>
                    <td><?= $row['order_item_id'] ?></td>
                    <td><?= $row['product_id'] ?></td>
                    <td><?= $row['quantity'] ?></td>
                    <td><?= $row['price'] ?></td>
                    <td><?= $row['shipping_address'] ?></td>
                    <td><?= $row['payment_method'] ?></td>
                    <td><?= $row['reference_number'] ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        </div>
        <?php
        } else {
            echo '<div class="alert alert-warning" role="alert">No results found.</div>';
        }

        // Close the connection
        $conn->close();
        ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Search functionality
            $("#searchButton").click(function() {
                var searchTerm = $("#searchInput").val().toLowerCase();
                $("#ordersTable tbody tr").each(function() {
                    var isMatch = false; // Flag to track if any column matches
                    $(this).find("td").each(function() {
                        var cellText = $(this).text().toLowerCase();
                        // Check if searchTerm is a substring of the cell text
                        if (cellText.indexOf(searchTerm) > -1) {
                            isMatch = true; // Set flag to true if a match is found
                            return false; // Stop iterating through cells in this row
                        }
                    });
                    if (isMatch) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        });
    </script>
    <?php include('include/footer.php')?>

</body>
</html>
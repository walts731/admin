<?php
    include('include/connect.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Sales Report</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/daily_reports.css"> 
</head>
<body style="background-color: #D6EFD8;">
    <!-- Navigation Bar -->
    <?php include('include/nav.php'); ?>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Daily Sales Report (Based on Archived At)</h2>

        <?php
        // Fetch data from the database, grouped by archived_at and summing total_price
        $sql = "SELECT 
                    DATE(archived_at) AS archived_date, 
                    SUM(total_price) AS daily_total 
                FROM 
                    orders_history 
                GROUP BY 
                    archived_date
                ORDER BY 
                    archived_date";
                    
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='mb-4'>
                        <h3 class='text-center'>{$row['archived_date']}</h3> 
                    </div>"; 

                echo "<div class='text-center mb-3'>
                        <h4>Total Sales: {$row['daily_total']}</h4>
                    </div>"; 

                $sqlOrders = "SELECT 
                                history_id, 
                                order_id, 
                                user_id, 
                                total_price, 
                                status, 
                                archived_at, 
                                order_item_id, 
                                product_id, 
                                quantity, 
                                price, 
                                shipping_address, 
                                payment_method, 
                                reference_number, 
                                payment_status
                            FROM 
                                orders_history 
                            WHERE 
                                DATE(archived_at) = '{$row['archived_date']}'
                            ORDER BY 
                                archived_at";

                $resultOrders = $conn->query($sqlOrders);

                if ($resultOrders->num_rows > 0) {
                    echo "<div class='table-responsive'><table class='table table-striped table-bordered'>"; 
                    echo "<thead class='table-dark'><tr>";
                    echo "<th>Order Number</th><th>User ID</th><th>Total Price</th><th>Status</th><th>Archived At</th><th>Order Item ID</th><th>Product ID</th><th>Quantity</th><th>Price</th><th>Shipping Address</th><th>Payment Method</th><th>Reference Number</th><th>Payment Status</th></tr></thead><tbody>";

                    while ($rowOrder = $resultOrders->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $rowOrder["order_id"] . "</td>";
                        echo "<td><a href='#userModal" . $rowOrder["user_id"] . "' data-toggle='modal' data-target='#userModal" . $rowOrder["user_id"] . "'><i class='bi bi-eye-fill'></i></a></td>";
                        echo "<td>" . $rowOrder["total_price"] . "</td>";
                        echo "<td>" . $rowOrder["status"] . "</td>";
                        echo "<td>" . $rowOrder["archived_at"] . "</td>";
                        echo "<td><a href='#orderItemModal" . $rowOrder["order_item_id"] . "' data-toggle='modal' data-target='#orderItemModal" . $rowOrder["order_item_id"] . "'><i class='bi bi-eye-fill'></i></a></td>";
                        echo "<td>" . $rowOrder["product_id"] . "</td>";
                        echo "<td>" . $rowOrder["quantity"] . "</td>";
                        echo "<td>" . $rowOrder["price"] . "</td>";
                        echo "<td>" . $rowOrder["shipping_address"] . "</td>";
                        echo "<td>" . $rowOrder["payment_method"] . "</td>";
                        echo "<td>" . $rowOrder["reference_number"] . "</td>";
                        echo "<td>" . $rowOrder["payment_status"] . "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody></table></div>";
                } else {
                    echo "<p class='text-center'>No orders found for this day.</p>";
                }
            }
        } else {
            echo "<p class='text-center'>No sales data found.</p>";
        }

        // User details modals
        $sqlUsers = "SELECT DISTINCT user_id FROM orders_history";
        $resultUsers = $conn->query($sqlUsers);

        if ($resultUsers->num_rows > 0) {
            while ($rowUser = $resultUsers->fetch_assoc()) {
                $userId = $rowUser["user_id"];
                $sqlUserDetails = "SELECT * FROM users WHERE user_id = '$userId'";
                $resultUserDetails = $conn->query($sqlUserDetails);

                if ($resultUserDetails->num_rows > 0) {
                    $rowUserDetails = $resultUserDetails->fetch_assoc();

                    echo "<div class='modal fade' id='userModal" . $userId . "' tabindex='-1' role='dialog' aria-labelledby='userModalLabel" . $userId . "' aria-hidden='true'>";
                    echo "<div class='modal-dialog' role='document'>";
                    echo "<div class='modal-content'>";
                    echo "<div class='modal-header'>";
                    echo "<h5 class='modal-title' id='userModalLabel" . $userId . "'>User Details</h5>";
                    echo "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>";
                    echo "<span aria-hidden='true'>&times;</span>";
                    echo "</button>";
                    echo "</div>";
                    echo "<div class='modal-body'>";
                    echo "<p><strong>User ID:</strong> " . $rowUserDetails["user_id"] . "</p>";
                    echo "<p><strong>Username:</strong> " . $rowUserDetails["username"] . "</p>";
                    echo "<p><strong>Full Name:</strong> " . $rowUserDetails["full_name"] . "</p>";
                    echo "<p><strong>Email:</strong> " . $rowUserDetails["email"] . "</p>";
                    echo "<p><strong>Role:</strong> " . $rowUserDetails["role"] . "</p>";
                    echo "<p><strong>Status:</strong> " . $rowUserDetails["status"] . "</p>";
                    echo "</div>";
                    echo "<div class='modal-footer'>";
                    echo "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }
            }
        }

        // Order Item details modals
        $sqlOrderItems = "SELECT DISTINCT order_item_id, product_id, quantity, price FROM orders_history";
        $resultOrderItems = $conn->query($sqlOrderItems);

        if ($resultOrderItems->num_rows > 0) {
            while ($rowItem = $resultOrderItems->fetch_assoc()) {
                $orderItemId = $rowItem["order_item_id"];

                echo "<div class='modal fade' id='orderItemModal" . $orderItemId . "' tabindex='-1' role='dialog' aria-labelledby='orderItemModalLabel" . $orderItemId . "' aria-hidden='true'>";
                echo "<div class='modal-dialog' role='document'>";
                echo "<div class='modal-content'>";
                echo "<div class='modal-header'>";
                echo "<h5 class='modal-title' id='orderItemModalLabel" . $orderItemId . "'>Order Item Details</h5>";
                echo "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>";
                echo "<span aria-hidden='true'>&times;</span>";
                echo "</button>";
                echo "</div>";
                echo "<div class='modal-body'>";
                echo "<p><strong>Order Item ID:</strong> " . $orderItemId . "</p>";
                echo "<p><strong>Product ID:</strong> " . $rowItem["product_id"] . "</p>";
                echo "<p><strong>Quantity:</strong> " . $rowItem["quantity"] . "</p>";
                echo "<p><strong>Price:</strong> " . $rowItem["price"] . "</p>";
                echo "</div>";
                echo "<div class='modal-footer'>";
                echo "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
            }
        }

        $conn->close();
        ?>
    </div>

    <!-- Include JS libraries for jQuery, Popper.js, and Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.4.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

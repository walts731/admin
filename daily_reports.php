<?php
include ('include/connect.php');
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

        <div class="form-group mb-3">
            <label for="searchDate">Search by Archived Date:</label>
            <input type="date" class="form-control" id="searchDate" name="searchDate">
            <button class="btn btn-primary mt-2" id="searchButton">Search</button>
        </div>

        <div id="salesReport">
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
                    echo "<div class='mb-4' data-date='" . $row['archived_date'] . "'>
                            <h3 class='text-center'>" . date("M d, Y ", strtotime($row['archived_date'])) . "</h3> 
                            <button class='btn btn-secondary toggle-table' data-target='table" . $row['archived_date'] . "'>Show/Hide Table</button>
                        </div>"; 

                    echo "<div class='text-center mb-3' data-date='" . $row['archived_date'] . "'>
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
                        echo "<div class='table-responsive' data-date='" . $row['archived_date'] . "'><table class='table table-striped table-bordered' id='table" . $row['archived_date'] . "'>"; 
                        echo "<thead class='table-dark'><tr>";
                        echo "<th>Order Number</th><th>User ID</th><th>Order Item ID</th><th>Quantity</th><th>Price</th><th>Total Price</th><th>Status</th><th>Archived At</th><th>Shipping Address</th><th>Payment Method</th><th>Reference Number</th><th>Payment Status</th></tr></thead><tbody>";

                        while ($rowOrder = $resultOrders->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $rowOrder["order_id"] . "</td>";
                            echo "<td><a href='#userModal" . $rowOrder["user_id"] . "' data-toggle='modal' data-target='#userModal" . $rowOrder["user_id"] . "'><i class='bi bi-eye-fill'></i></a></td>";
                            echo "<td><a href='#orderItemModal" . $rowOrder["order_id"] . "' data-toggle='modal' data-target='#orderItemModal" . $rowOrder["order_id"] . "'><i class='bi bi-eye-fill'></i></a></td>";
                            echo "<td>" . $rowOrder["quantity"] . "</td>";
                            echo "<td>" . $rowOrder["price"] . "</td>";
                            echo "<td>" . $rowOrder["total_price"] . "</td>";
                            echo "<td>" . $rowOrder["status"] . "</td>";
                            echo "<td>" . date("M d, Y h:ia", strtotime($rowOrder["archived_at"])) . "</td>"; // Format the date
                            echo "<td>" . $rowOrder["shipping_address"] . "</td>";
                            
                            // Display Payment Method
                            if ($rowOrder["payment_method"] == 0) {
                                echo "<td>Cash on Delivery</td>";
                            } else {
                                $paymentMethodId = $rowOrder["payment_method"];
                                $sqlPaymentMethod = "SELECT method_name FROM payment_methods WHERE payment_method_id = '$paymentMethodId'";
                                $resultPaymentMethod = $conn->query($sqlPaymentMethod);
                                if ($resultPaymentMethod->num_rows > 0) {
                                    $rowPaymentMethod = $resultPaymentMethod->fetch_assoc();
                                    echo "<td>" . $rowPaymentMethod["method_name"] . "</td>";
                                } else {
                                    echo "<td>Unknown Payment Method</td>";
                                }
                            }

                            echo "<td>" . $rowOrder["reference_number"] . "</td>";
                            echo "<td>" . $rowOrder["payment_status"] . "</td>";
                            echo "</tr>";
                        }
                        echo "</tbody></table></div>";
                    } else {
                        echo "<p class='text-center' data-date='" . $row['archived_date'] . "'>No orders found for this day.</p>";
                    }
                }
            } else {
                echo "<p class='text-center'>No sales data found.</p>";
            }
            ?>
        </div>

        <!-- User details modals -->
        <?php
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
        $sqlOrderItems = "SELECT DISTINCT order_id FROM orders_history";
        $resultOrderItems = $conn->query($sqlOrderItems);

        if ($resultOrderItems->num_rows > 0) {
            while ($rowItem = $resultOrderItems->fetch_assoc()) {
                $orderId = $rowItem["order_id"];

                $sqlOrderItemDetails = "SELECT * FROM orders_history WHERE order_id = '$orderId'";
                $resultOrderItemDetails = $conn->query($sqlOrderItemDetails);

                if ($resultOrderItemDetails->num_rows > 0) {
                    echo "<div class='modal fade' id='orderItemModal" . $orderId . "' tabindex='-1' role='dialog' aria-labelledby='orderItemModalLabel" . $orderId . "' aria-hidden='true'>";
                    echo "<div class='modal-dialog' role='document'>";
                    echo "<div class='modal-content'>";
                    echo "<div class='modal-header'>";
                    echo "<h5 class='modal-title' id='orderItemModalLabel" . $orderId . "'>Order Items</h5>";
                    echo "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>";
                    echo "<span aria-hidden='true'>&times;</span>";
                    echo "</button>";
                    echo "</div>";
                    echo "<div class='modal-body'>";
                    echo "<table class='table table-striped table-bordered'>";
                    echo "<thead><tr>";
                    echo "<th>Order Item ID</th><th>Product Name</th><th>Quantity</th><th>Price</th></tr></thead><tbody>";

                    while ($rowOrderItemDetail = $resultOrderItemDetails->fetch_assoc()) {
                        $productId = $rowOrderItemDetail["product_id"];
                        $sqlProductName = "SELECT product_name FROM products WHERE product_id = '$productId'";
                        $resultProductName = $conn->query($sqlProductName);
                        if ($resultProductName->num_rows > 0) {
                            $rowProductName = $resultProductName->fetch_assoc();
                            echo "<tr>";
                            echo "<td>" . $rowOrderItemDetail["order_item_id"] . "</td>";
                            echo "<td>" . $rowProductName["product_name"] . "</td>";
                            echo "<td>" . $rowOrderItemDetail["quantity"] . "</td>";
                            echo "<td>" . $rowOrderItemDetail["price"] . "</td>";
                            echo "</tr>";
                        }
                    }
                    echo "</tbody></table>";
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

        $conn->close();
        ?>
    </div>

    <!-- Include JS libraries for jQuery, Popper.js, and Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.4.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#searchButton').click(function() {
                var searchDate = $('#searchDate').val();
                if (searchDate) {
                    // Filter the sales report based on the search date
                    $('#salesReport').find('div, p, table').each(function() {
                        var dateAttr = $(this).data('date');
                        if (dateAttr === searchDate) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                } else {
                    // Show all the sales report if no date is selected
                    $('#salesReport').find('div, p, table').show();
                }
            });

            // Toggle table visibility
            $('.toggle-table').click(function() {
                var targetTable = $(this).data('target');
                $(targetTable).toggle();
            });
        });
    </script>
</body>
</html>
<?php 
include('include/connect.php'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $searchDate = $_POST['searchDate']; 

    // Validate the date format (optional but recommended)
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $searchDate)) {
        echo "<p class='text-danger'>Invalid date format. Please use YYYY-MM-DD.</p>";
        exit;
    }

    // Fetch data from the database for the specified date
    $sql = "SELECT 
                DATE(archived_at) AS archived_date, 
                SUM(total_price) AS daily_total 
            FROM 
                orders_history 
            WHERE 
                DATE(archived_at) = '$searchDate'
            GROUP BY 
                archived_date
            ORDER BY 
                archived_date";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Display the results for the searched date
        echo "<div class='mb-4'>
                <h3 class='text-center'>" . date("M d, Y ", strtotime($searchDate)) . "</h3> 
            </div>"; 

        echo "<div class='text-center mb-3'>
                <h4>Total Sales: " . $result->fetch_assoc()['daily_total'] . "</h4>
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
                        DATE(archived_at) = '$searchDate'
                    ORDER BY 
                        archived_at";

        $resultOrders = $conn->query($sqlOrders);

        if ($resultOrders->num_rows > 0) {
            echo "<div class='table-responsive'><table class='table table-striped table-bordered'>"; 
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
            echo "<p class='text-center'>No orders found for this day.</p>";
        }
    } else {
        echo "<p class='text-center'>No sales data found for this date.</p>";
    }

    // User details modals (same as before)
    // ... 

    // Order Item details modals (same as before)
    // ... 

    $conn->close();
}
?>
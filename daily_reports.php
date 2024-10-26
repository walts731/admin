<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Orders Report</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/product.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
</head>

<body style="background-color: #D6EFD8;">
    <!-- Navigation Bar -->
    <?php include('include/nav.php'); ?>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Daily Orders Reports</h1>

        <!-- Search Bar -->
        <div class="input-group mb-3">
            <input type="text" class="form-control" id="searchInput" placeholder="Search by order ID, username, or status...">
            <button class="btn btn-outline-secondary" type="button" id="searchButton">Search</button>
        </div>

        <?php
        include('include/connect.php');

        // SQL query to fetch data from orders_history table, grouped by the day of archiving
        $sql = "SELECT 
                    DATE(oh.archived_at) AS report_date,
                    oh.history_id, 
                    oh.order_id, 
                    oh.user_id, 
                    oh.total_price, 
                    oh.status, 
                    oh.archived_at, 
                    oh.order_item_id, 
                    oh.product_id, 
                    oh.quantity, 
                    oh.price, 
                    oh.shipping_address, 
                    oh.payment_method, 
                    oh.reference_number, 
                    p.product_name AS product_name, 
                    u.username AS username 
                FROM orders_history AS oh
                JOIN products AS p ON oh.product_id = p.product_id
                JOIN users AS u ON oh.user_id = u.user_id
                ORDER BY report_date DESC, oh.order_date DESC"; // Grouped by archived_at date
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Group orders by report_date (archived_at date)
            $dailyReports = [];
            while ($row = $result->fetch_assoc()) {
                $date = $row['report_date'];
                if (!isset($dailyReports[$date])) {
                    $dailyReports[$date] = [];
                }
                $dailyReports[$date][] = $row;
            }

            // Output each day's report
            foreach ($dailyReports as $reportDate => $orders) : 
                // Calculate total price for the day
                $dailyTotal = 0;
                foreach ($orders as $order) {
                    $dailyTotal += $order['total_price'];
                }
        ?>
                <!-- Daily Report Section -->
                <div class="card mb-4">
                    <div class="card-header text-white" style="background-color: #508D4E;">
                        <h3 class="card-title"><?= date('F j, Y', strtotime($reportDate)) ?></h3>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><strong>Total Sales for the Day: </strong> $<?= number_format($dailyTotal, 2) ?></p>
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead class="" style="background-color: #508D4E; color: white;">
                                    <tr>
                                        <th>Order Number</th>
                                        <th>Username</th>
                                        <th>Status</th>
                                        <th>Archived At</th>
                                        <th>Shipping Address</th>
                                        <th>Payment Method</th>
                                        <th>Reference Number</th>
                                        <th>Total Amount</th>
                                        <th>Items</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order) : ?>
                                        <tr>
                                            <td><?= $order['order_id'] ?></td>
                                            <td><?= $order['username'] ?></td>
                                            <td style="font-weight: bold;">
                                                <?php if ($order['status'] === 'completed') : ?>
                                                    <span style="color: green;"><?= $order['status'] ?></span>
                                                <?php elseif ($order['status'] === 'cancelled') : ?>
                                                    <span style="color: red;"><?= $order['status'] ?></span>
                                                <?php else : ?>
                                                    <?= $order['status'] ?>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('M d Y h:ia', strtotime($order['archived_at'])) ?></td>
                                            <td><?= $order['shipping_address'] ?></td>
                                            <td><?= $order['payment_method'] ?></td>
                                            <td><?= $order['reference_number'] ?></td>
                                            <td><?= number_format($order['total_price'], 2) ?></td>
                                            <td>
                                                <!-- Eye icon to view order items -->
                                                <button class="btn btn-link" data-bs-toggle="modal" data-bs-target="#orderItemsModal<?= $order['order_id'] ?>">
                                                    <i class="bi bi-eye"></i>
                                                </button>

                                                <!-- Modal for displaying order items -->
                                                <div class="modal fade" id="orderItemsModal<?= $order['order_id'] ?>" tabindex="-1" aria-labelledby="orderItemsModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="orderItemsModalLabel">Order Items for Order #<?= $order['order_id'] ?></h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <ul class="list-group">
                                                                    <?php
                                                                    $item_sql = "SELECT product_name, quantity, price FROM order_items WHERE order_id = " . $order['order_id'];
                                                                    $item_result = $conn->query($item_sql);
                                                                    while ($item_row = $item_result->fetch_assoc()) : ?>
                                                                        <li class="list-group-item">
                                                                            <strong><?= $item_row['product_name'] ?></strong> (<?= $item_row['quantity'] ?>) - 
                                                                            Price: <?= number_format($item_row['price'], 2) ?>
                                                                        </li>
                                                                    <?php endwhile; ?>
                                                                </ul>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

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
                $("tbody tr").each(function() {
                    var orderID = $(this).find("td:first").text().toLowerCase();
                    var username = $(this).find("td:nth-child(2)").text().toLowerCase();
                    var status = $(this).find("td:nth-child(3)").text().toLowerCase();

                    if (
                        orderID.indexOf(searchTerm) > -1 || 
                        username.indexOf(searchTerm) > -1 || 
                        status.indexOf(searchTerm) > -1
                    ) {
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

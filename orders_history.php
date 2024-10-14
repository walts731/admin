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
            <input type="text" class="form-control" id="searchInput" placeholder="Search by order ID, username, or status...">
            <button class="btn btn-outline-secondary" type="button" id="searchButton">Search</button>
        </div>

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
                    `oh`.`shipping_address`,
                    `oh`.`payment_method`,
                    `oh`.`reference_number`,
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
            // Group orders by order_id
            $orders = [];
            while ($row = $result->fetch_assoc()) {
                if (!isset($orders[$row['order_id']])) {
                    $orders[$row['order_id']] = [
                        'details' => [
                            'order_id' => $row['order_id'],
                            'username' => $row['username'],
                            'status' => $row['status'],
                            'archived_at' => date('M d Y h:ia', strtotime($row["archived_at"])),
                            'shipping_address' => $row['shipping_address'],
                            'payment_method' => $row['payment_method'],
                            'reference_number' => $row['reference_number'],
                            'total_price' => $row['total_price'] // Assuming total_price is already calculated
                        ],
                        'items' => []
                    ];
                }
                $orders[$row['order_id']]['items'][] = [
                    'product_name' => $row['product_name'],
                    'quantity' => $row['quantity'],
                    'price' => $row['price'],
                    'order_item_id' => $row['order_item_id'] // Add order_item_id for reference
                ];
            }

            // Output data of each row
            echo '<div class="table-responsive">';
            echo '<table class="table table-striped table-bordered" id="ordersTable">';
            echo '<thead class="" style="background-color: #508D4E; color: white;">

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
                  </thead>';
            echo '<tbody>';

            foreach ($orders as $order) : ?>
                <tr>
                    <td><?= $order['details']['order_id'] ?></td>
                    <td><?= $order['details']['username'] ?></td>
                    <td style="font-weight: bold;">
                        <?php if ($order['details']['status'] === 'completed') : ?>
                            <span style="color: green;"><?= $order['details']['status'] ?></span>
                        <?php elseif ($order['details']['status'] === 'cancelled') : ?>
                            <span style="color: red;"><?= $order['details']['status'] ?></span>
                        <?php else : ?>
                            <?= $order['details']['status'] ?>
                        <?php endif; ?>
                    </td>
                    <td><?= $order['details']['archived_at'] ?></td>
                    <td><?= $order['details']['shipping_address'] ?></td>
                    <td><?= $order['details']['payment_method'] ?></td>
                    <td><?= $order['details']['reference_number'] ?></td>
                    <td><?= number_format($order['details']['total_price'], 2) ?></td>
                    <td>
                        <!-- Eye icon to view order items -->
                        <button class="btn btn-link" data-bs-toggle="modal" data-bs-target="#orderItemsModal<?= $order['details']['order_id'] ?>">
                            <i class="bi bi-eye"></i>
                        </button>

                        <!-- Modal for displaying order items -->
                        <div class="modal fade" id="orderItemsModal<?= $order['details']['order_id'] ?>" tabindex="-1" aria-labelledby="orderItemsModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="orderItemsModalLabel">Order Items for Order #<?= $order['details']['order_id'] ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <ul class="list-group">
                                            <?php foreach ($order['items'] as $item): ?>
                                                <li class="list-group-item">
                                                    <strong><?= $item['product_name'] ?></strong> (<?= $item['quantity'] ?>) - 
                                                    Price: <?= number_format($item['price'], 2) ?>
                                                </li>
                                            <?php endforeach; ?>
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
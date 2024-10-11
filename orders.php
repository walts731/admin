<?php 
include('include/connect.php');

// Handle order update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $orderId = $_POST['order_id'];
    $status = $_POST['status'];

    // Update order status
    $updateQuery = "UPDATE orders SET status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("si", $status, $orderId);
    $stmt->execute();
    $stmt->close();

    // If the order status is "completed", move the order to orders_history and delete it from orders
    if ($status === 'completed') {
        // Fetch the order details from orders table
        $orderQuery = "SELECT * FROM orders WHERE order_id = ?";
        $stmt = $conn->prepare($orderQuery);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();
        $stmt->close();

        if ($order) {
            // Fetch the order items for this order
            $itemsQuery = "SELECT order_item_id, product_id, quantity, price FROM order_items WHERE order_id = ?";
            $stmt = $conn->prepare($itemsQuery);
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $itemsResult = $stmt->get_result();

            // Prepare to insert into orders_history
            $insertHistoryQuery = "INSERT INTO orders_history (order_id, user_id, total_price, status, order_date, order_item_id, product_id, quantity, price) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            // Loop through each item and insert it into orders_history and update inventory
            while ($item = $itemsResult->fetch_assoc()) {
                $stmt = $conn->prepare($insertHistoryQuery);
                $stmt->bind_param("iissiiidd", 
                    $order['order_id'], 
                    $order['user_id'], 
                    $order['total_price'], 
                    $order['status'], 
                    $order['order_date'], 
                    $item['order_item_id'], 
                    $item['product_id'], 
                    $item['quantity'], 
                    $item['price']
                );
                $stmt->execute();

                // Update inventory stock
                $updateInventoryQuery = "UPDATE inventory SET stock = stock - ? WHERE product_id = ?";
                $inventoryStmt = $conn->prepare($updateInventoryQuery);
                $inventoryStmt->bind_param("ii", $item['quantity'], $item['product_id']);
                $inventoryStmt->execute();
                $inventoryStmt->close();
            }
            $stmt->close();

            // Delete associated order items from order_items table
            $deleteItemsQuery = "DELETE FROM order_items WHERE order_id = ?";
            $stmt = $conn->prepare($deleteItemsQuery);
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $stmt->close();

            // Delete the order from orders table
            $deleteOrderQuery = "DELETE FROM orders WHERE order_id = ?";
            $stmt = $conn->prepare($deleteOrderQuery);
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $stmt->close();
        }

        // Redirect to orders.php after update
        header("Location: orders.php");
        exit();
    }
}

// Handle order deletion (without completion)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_order_id'])) {
    $deleteOrderId = $_POST['delete_order_id'];

    // First, delete associated order items
    $deleteItemsQuery = "DELETE FROM order_items WHERE order_id = ?";
    $stmt = $conn->prepare($deleteItemsQuery);
    $stmt->bind_param("i", $deleteOrderId);
    $stmt->execute();
    $stmt->close();

    // Now, delete the order
    $deleteQuery = "DELETE FROM orders WHERE order_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $deleteOrderId);
    $stmt->execute();
    $stmt->close();

    // Redirect to orders.php after deletion
    header("Location: orders.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body>
    <!-- Navigation Bar -->
    <?php include('include/nav.php') ?>

    <div class="container mt-5">
        <h2>Orders Management</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Order Date</th>
                    <th>Items</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch orders along with their items, user details, and product details
                $query = "
                    SELECT o.order_id, o.user_id, o.total_price, o.status, o.order_date, 
                           u.username, 
                           oi.order_item_id, oi.product_id, oi.quantity, oi.price, 
                           p.product_name 
                    FROM orders o
                    LEFT JOIN order_items oi ON o.order_id = oi.order_id
                    LEFT JOIN users u ON o.user_id = u.user_id
                    LEFT JOIN products p ON oi.product_id = p.product_id
                ";
                $result = mysqli_query($conn, $query);
                $orders = [];

                while ($order = mysqli_fetch_assoc($result)) {
                    $orders[$order['order_id']]['details'] = $order;
                    if (!isset($orders[$order['order_id']]['items'])) {
                        $orders[$order['order_id']]['items'] = [];
                    }
                    if ($order['order_item_id']) {
                        $orders[$order['order_id']]['items'][] = $order;
                    }
                }

                foreach ($orders as $orderId => $order): ?>
                <tr>
                    <td>#<?= $order['details']['order_id'] ?></td>
                    <td><?= htmlspecialchars($order['details']['user_id']) ?></td>
                    <td><?= htmlspecialchars($order['details']['username']) ?></td>
                    <td><?= htmlspecialchars($order['details']['order_date']) ?></td>
                    <td>
                        <button class="btn btn-link" data-bs-toggle="modal" data-bs-target="#orderItemsModal<?= $orderId ?>">
                            View Items <i class="bi bi-eye"></i>
                        </button>

                        <!-- Modal for displaying order items -->
                        <div class="modal fade" id="orderItemsModal<?= $orderId ?>" tabindex="-1" aria-labelledby="orderItemsModalLabel" aria-hidden="true">
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
                                                    <strong><?= htmlspecialchars($item['product_name']) ?></strong> (<?= $item['quantity'] ?>) - 
                                                    Price: <?= htmlspecialchars($item['price']) ?>
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
                    <td><span class="badge bg-warning"><?= htmlspecialchars($order['details']['status']) ?></span></td>
                    <td>
                        <!-- Form for updating order status -->
                        <form action="" method="post" class="d-inline">
                            <input type="hidden" name="order_id" value="<?= $order['details']['order_id'] ?>">
                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="pending" <?= $order['details']['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="completed" <?= $order['details']['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="cancelled" <?= $order['details']['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                        </form>
                        <!-- Form for deleting order -->
                        <form action="" method="post" class="d-inline">
                            <input type="hidden" name="delete_order_id" value="<?= $order['details']['order_id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
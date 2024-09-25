<?php 
include('include/connect.php');

// Handle order update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $orderId = $_POST['order_id'];
    $status = $_POST['status'];

    $updateQuery = "UPDATE orders SET status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("si", $status, $orderId);
    $stmt->execute();
    $stmt->close();
    
    // Redirect to orders.php after update
    header("Location: orders.php");
    exit();
}

// Handle order deletion
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
                           p.product_name, p.product_description 
                    FROM orders o
                    LEFT JOIN order_items oi ON o.order_id = oi.order_id
                    LEFT JOIN users u ON o.user_id = u.user_id
                    LEFT JOIN products p ON oi.product_id = p.product_id
                ";
                $result = mysqli_query($conn, $query);
                $orders = [];

                while ($order = mysqli_fetch_assoc($result)) {
                    $orders[$order['order_id']]['details'] = $order;
                    if ($order['order_item_id']) {
                        $orders[$order['order_id']]['items'][] = $order;
                    } else {
                        $orders[$order['order_id']]['items'] = [];
                    }
                }

                foreach ($orders as $orderId => $order): ?>
                <tr>
                    <td>#<?= $order['details']['order_id'] ?></td>
                    <td><?= htmlspecialchars($order['details']['user_id']) ?></td>
                    <td><?= htmlspecialchars($order['details']['username']) ?></td>
                    <td><?= htmlspecialchars($order['details']['order_date']) ?></td>
                    <td>
                        <?php foreach ($order['items'] as $item): ?>
                            <strong><?= htmlspecialchars($item['product_name']) ?></strong> (<?= $item['quantity'] ?>) - 
                            <em><?= htmlspecialchars($item['product_description']) ?></em> - 
                            Price: <?= htmlspecialchars($item['price']) ?><br>
                        <?php endforeach; ?>
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

</body>
</html>

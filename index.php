<?php include ('include/connect.php')?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DELIDAZE Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

<?php
    
    
    // Example SQL Queries
    $totalProducts = $conn->query("SELECT COUNT(*) as total FROM products")->fetch_assoc()['total'];
    $totalOrders = $conn->query("SELECT COUNT(*) as total FROM orders")->fetch_assoc()['total'];
    $pendingOrders = $conn->query("SELECT COUNT(*) as pending FROM orders WHERE status='pending'")->fetch_assoc()['pending'];
    $salesSummary = $conn->query("SELECT SUM(total_price) as total_sales FROM orders")->fetch_assoc()['total_sales'];
    $totalRevenue = $conn->query("SELECT SUM(total_price) as revenue FROM sales")->fetch_assoc()['revenue'];
?>

<!-- Navigation Bar -->
<?php include ('include/nav.php')?>

<!-- Dashboard Summary -->
<div class="container mt-4">
    <div class="row">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Total Products</h5>
                    <p class="card-text"><?php echo $totalProducts; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Total Orders</h5>
                    <p class="card-text"><?php echo $totalOrders; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Pending Orders</h5>
                    <p class="card-text"><?php echo $pendingOrders; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Sales Summary</h5>
                    <p class="card-text">₱<?php echo number_format($salesSummary, 2); ?></p>
                </div>
            </div>
        </div>
        <!-- Added Analytics Card -->
        <div class="col-md-3 mt-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Total Revenue</h5>
                    <p class="card-text">₱<?php echo number_format($totalRevenue, 2); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section Buttons -->
<div class="container mt-5">
        <h2>Manage Sections</h2>
        <div class="row">
            <div class="col-md-3">
                <a href="products.php" class="btn btn-custom btn-products">Manage Products</a>
            </div>
            <div class="col-md-3">
                <a href="orders.php" class="btn btn-custom btn-orders">Manage Orders</a>
            </div>
            <div class="col-md-3">
                <a href="inventory.php" class="btn btn-custom btn-inventory">Manage Inventory</a>
            </div>
            <div class="col-md-3">
                <a href="users.php" class="btn btn-custom btn-users">Manage Users</a>
            </div>
            <!-- Added Analytics Button -->
            <div class="col-md-3 mt-3">
                <a href="analytics.php" class="btn btn-custom btn-analytics">View Analytics</a>
            </div>
        </div>
    </div></body>
</html>

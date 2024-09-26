<?php
include ('include/connect.php');

// Example SQL Queries (unchanged)
$totalProducts = $conn->query("SELECT COUNT(*) as total FROM products")->fetch_assoc()['total'];
$totalOrders = $conn->query("SELECT COUNT(*) as total FROM orders")->fetch_assoc()['total'];
$pendingOrders = $conn->query("SELECT COUNT(*) as pending FROM orders WHERE status='pending'")->fetch_assoc()['pending'];
$salesSummary = $conn->query("SELECT SUM(total_price) as total_sales FROM orders WHERE status='completed'")->fetch_assoc()['total_sales'];

// Calculate total revenue dynamically
$revenueSql = "
    SELECT SUM(p.price - i.cost) AS total_revenue 
    FROM products p 
    JOIN inventory i ON p.product_id = i.product_id
    WHERE p.stock > 0"; // Considering only products that are in stock

$totalRevenue = $conn->query($revenueSql)->fetch_assoc()['total_revenue'];

// Fetch low stock products (e.g., stock < 5)
$lowStockSql = "
    SELECT i.product_id, p.product_name, i.stock 
    FROM inventory i 
    JOIN products p ON i.product_id = p.product_id 
    WHERE i.stock < 5"; // Adjust the stock threshold as needed

$lowStockResult = $conn->query($lowStockSql);

// SQL query to get top-selling products from completed orders
$topSellingSql = "
    SELECT oi.product_id, p.product_name, p.image_url, SUM(oi.quantity) as total_sold
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.order_id
    JOIN products p ON oi.product_id = p.product_id
    WHERE o.status = 'completed'
    GROUP BY oi.product_id
    ORDER BY total_sold DESC
    LIMIT 3"; // You can adjust the limit to show more top-selling products

$topSellingResult = $conn->query($topSellingSql);
?>
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
    $salesSummary = $conn->query("SELECT SUM(total_price) as total_sales FROM orders WHERE status='completed'")->fetch_assoc()['total_sales'];

    // Calculate total revenue dynamically based on product prices and inventory costs
    $revenueSql = "
        SELECT SUM(p.price - i.cost) AS total_revenue 
        FROM products p 
        JOIN inventory i ON p.product_id = i.product_id
        WHERE p.stock > 0"; // Assuming you only want to consider products that are in stock

    $totalRevenue = $conn->query($revenueSql)->fetch_assoc()['total_revenue']; 
?>

<!-- Navigation Bar -->
<?php include ('include/nav.php') ?>

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
        <!-- Dynamic Total Revenue Card -->
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
        <div class="col-md-3 mt-3">
            <a href="analytics.php" class="btn btn-custom btn-analytics">View Analytics</a>
        </div>
    </div>
</div>

<!-- Dynamic Low Stock Alert Section -->
<div class="container mt-5">
    <h2>Low Stock Alerts</h2>
    <div class="row">
        <?php if ($lowStockResult->num_rows > 0): ?>
            <?php while ($row = $lowStockResult->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $row['product_name']; ?></h5>
                            <p class="card-text">Stock: <?php echo $row['stock']; ?> units</p>
                            <p class="text-danger">⚠️ Low Stock Alert!</p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center">No low stock products at the moment.</p>
        <?php endif; ?>
    </div>
</div>


    <!-- Top Selling Products Section -->
    <div class="container top-products-container mt-5">
    <h2>Top Selling Products</h2>
    <div class="row">
        <?php if ($topSellingResult->num_rows > 0): ?>
            <?php while ($row = $topSellingResult->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="product-card">
                        <img src="<?php echo $row['image_url']; ?>" alt="<?php echo $row['product_name']; ?>" class="product-image">
                        <div class="product-details">
                            <h5 class="product-title"><?php echo $row['product_name']; ?></h5>
                            <p class="product-sales">Sold: <?php echo $row['total_sold']; ?> units</p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center">No top-selling products to display.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>

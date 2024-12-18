<?php
include ('include/connect.php');

// Example SQL Queries (unchanged)
$totalProducts = $conn->query("SELECT COUNT(*) as total FROM products")->fetch_assoc()['total'];
$totalOrders = $conn->query("SELECT COUNT(*) as total FROM orders")->fetch_assoc()['total'];
$pendingOrders = $conn->query("SELECT COUNT(*) as pending FROM orders WHERE status='pending'")->fetch_assoc()['pending'];
$salesSummary = $conn->query("SELECT SUM(total_price) as total_sales FROM orders_history WHERE status='completed'")->fetch_assoc()['total_sales']; // Fetched from orders_history
$revenueSql = "
    SELECT SUM(p.price - i.cost) AS total_revenue 
    FROM products p 
    JOIN inventory i ON p.product_id = i.product_id
    WHERE i.stock > 0"; // Now using stock from the inventory table

$totalRevenue = $conn->query($revenueSql)->fetch_assoc()['total_revenue'];


// Fetch low stock products (e.g., stock < 5)
$lowStockSql = "
    SELECT i.product_id, p.product_name, i.stock 
    FROM inventory i 
    JOIN products p ON i.product_id = p.product_id 
    WHERE i.stock <= 5"; // Adjust threshold as needed

$lowStockResult = $conn->query($lowStockSql);

$topSellingSql = "
    SELECT oh.product_id, p.product_name, p.image_url, SUM(oh.quantity) as total_sold
    FROM orders_history oh
    JOIN products p ON oh.product_id = p.product_id
    WHERE oh.status = 'completed'
    GROUP BY oh.product_id
    HAVING SUM(oh.quantity) >= 5 
    ORDER BY total_sold DESC
    "; // Adjust the limit to show more top-selling products

$topSellingResult = $conn->query($topSellingSql);



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DELIDAZE Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style_admin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cutive+Mono&family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>

<?php
    // Example SQL Queries
    $totalProducts = $conn->query("SELECT COUNT(*) as total FROM products")->fetch_assoc()['total'];
    $totalOrders = $conn->query("SELECT COUNT(*) as total FROM orders")->fetch_assoc()['total'];
    $pendingOrders = $conn->query("SELECT COUNT(*) as pending FROM orders WHERE status='pending'")->fetch_assoc()['pending'];
    $salesSummary = $conn->query("SELECT SUM(total_price) as total_sales FROM orders_history WHERE status='completed'")->fetch_assoc()['total_sales']; // Fetched from orders_history

    $revenueSql = "
    SELECT SUM(p.price - i.cost) AS total_revenue 
    FROM products p 
    JOIN inventory i ON p.product_id = i.product_id
    WHERE i.stock > 0"; // Now using stock from the inventory table

    $totalRevenue = $conn->query($revenueSql)->fetch_assoc()['total_revenue'];

?>

<!-- Navigation Bar -->
<?php include ('include/nav.php') ?>

<!-- Welcome Message -->


<!-- Dashboard Summary and Section Buttons in Two Columns -->
<div class="container mt-5">
    <div class="row">
        <!-- Dashboard Summary Column -->
        <div class="col-md-4 dashboard-body">
            <h2>Dashboard Summary</h2>
            <div class="row">
            <div class="col-md-12">
                    <div class="card text-center mt-3 dashboard-summary">
                        <div class="card-body">
                            <h5 class="card-title dashboard-summary">Total Orders</h5>
                            <p class="card-text"><?php echo $totalOrders; ?></p>
                        </div>
                    </div>
                </div> 

                <div class="col-md-6">
                    <div class="card text-center mt-3 dashboard-summary">
                        <div class="card-body">
                            <h5 class="card-title dashboard-summary">Total Products</h5>
                            <p class="card-text"><?php echo $totalProducts; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card text-center mt-3 dashboard-summary">
                        <div class="card-body">
                            <h5 class="card-title dashboard-summary">Pending Orders</h5>
                            <p class="card-text"><?php echo $pendingOrders; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card text-center mt-3 dashboard-summary">
                        <div class="card-body">
                            <h5 class="card-title dashboard-summary">Sales Summary</h5>
                            <p class="card-text">₱<?php echo number_format($salesSummary, 2); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card text-center mt-3 dashboard-summary">
                        <div class="card-body">
                            <h5 class="card-title dashboard-summary">Total Revenue</h5>
                            <p class="card-text">₱<?php echo number_format($totalRevenue, 2); ?></p>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        <!-- Section Buttons Column -->
        <div class="col-md-4">
            <h2>Manage Sections</h2>
            <div class="row">
                <div class="col-md-12">
                    <a href="products.php" class="btn btn-custom rounded-pill btn-products w-100 mb-3">Manage Products</a>
                </div>
                <div class="col-md-12">
                    <a href="orders.php" class="btn btn-custom rounded-pill btn-orders w-100 mb-3">Manage Orders</a>
                </div>
                <div class="col-md-12">
                    <a href="inventory.php" class="btn btn-custom rounded-pill btn-inventory w-100 mb-3">Manage Inventory</a>
                </div>
                <div class="col-md-12">
                    <a href="users.php" class="btn btn-custom rounded-pill btn-users w-100 mb-3">Manage Users</a>
                </div>
                <div class="col-md-12">
                    <a href="analytics.php" class="btn btn-custom rounded-pill btn-analytics w-100 mb-3">View Analytics</a>
                </div>
            </div>
        </div>

        <!-- Dynamic Low Stock Alert Section -->
<div class="col-md-4">
    <h2>Low Stock Alerts</h2>
    <div id="lowStockCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php if ($lowStockResult->num_rows > 0): ?>
                <?php $first = true; ?>
                <?php while ($row = $lowStockResult->fetch_assoc()): ?>
                    <div class="carousel-item <?php if ($first) { echo 'active'; $first = false; } ?>">
                        <div class="card text-center dashboard-summary">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $row['product_name']; ?></h5>
                                <p class="card-text">Stock: <?php echo $row['stock']; ?> units</p>
                                <p class="text-danger">⚠️ Low Stock Alert!</p>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="carousel-item active">
                    <div class="card text-center dashboard-summary">
                        <div class="card-body">
                            <p class="text-center">No low stock products at the moment.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#lowStockCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#lowStockCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>

    </div>
</div>

            </br>
    <!-- Carousel Container -->
<div class="container mt-5">
    <h2>Top Selling Products</h2>
    <div class="carousel">
        <div class="carousel-wrapper">
            <?php if ($topSellingResult->num_rows > 0): ?>
                <?php while ($row = $topSellingResult->fetch_assoc()): ?>
                    <div class="carousel-card">
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
                <div class="carousel-card">
                    <div class="product-card">
                        <p class="text-center">No top-selling products to display.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <button class="carousel-btn left" onclick="swipeLeft()">&#10094;</button>
        <button class="carousel-btn right" onclick="swipeRight()">&#10095;</button>
    </div>
</div>

            </br>



<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
<?php include('include/footer.php')?>
</body>
</html>

<script>
    let currentIndex = 0;
    const cards = document.querySelectorAll('.carousel-card');
    const totalCards = cards.length;
    const wrapper = document.querySelector('.carousel-wrapper');

    function showCards(index) {
        const cardsToShow = 3; // Number of cards to show
        const maxIndex = Math.ceil(totalCards / cardsToShow) - 1;
        wrapper.style.transform = `translateX(-${index * (100 / cardsToShow)}%)`;
    }

    function swipeLeft() {
        currentIndex = (currentIndex > 0) ? currentIndex - 1 : Math.ceil(totalCards / 3) - 1;
        showCards(currentIndex);
    }

    function swipeRight() {
        currentIndex = (currentIndex < Math.ceil(totalCards / 3) - 1) ? currentIndex + 1 : 0;
        showCards(currentIndex);
    }
</script>
<?php
include('include/connect.php');

// Check if product ID is set in the URL
if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']); // Sanitize input by converting to integer

    // Fetch product details from the database
    $sql = "SELECT * FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        echo "Product not found!";
        exit;
    }
} else {
    echo "No product ID provided!";
    exit;
}

// Check if the delete confirmation form has been submitted
if (isset($_POST['confirmDelete'])) {
    $productId = intval($_POST['productId']); // Sanitize input

    // Delete the product from the database
    $sql = "DELETE FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productId);

    if ($stmt->execute()) {
        echo "<script>alert('Product deleted successfully!'); window.location.href='products.php';</script>";
    } else {
        echo "Error deleting product: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Product</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body style="background-color: #D6EFD8;">
<?php include('include/nav.php')?>

<div class="container mt-5">
    <h2 class="mb-4">Delete Product</h2>
    <p>Are you sure you want to delete the following product?</p>
    <div class="card mb-4">
        <div class="card-body" style="background-color: #FFFFE0">
            <h5 class="card-title"><?php echo htmlspecialchars($product['product_name']); ?></h5>
            <p class="card-text">Price: ₱<?php echo number_format($product['price'], 2); ?></p>
            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" width="80" class="img-fluid">
        </div>
    </div>
    <button class="btn btn-outline-danger rounded-pill" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">Delete Product</button>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog rounded-3" style="background-color: #cce7c9; ">
            <div class="modal-content rounded-3" style="background-color: #cce7c9;">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this product? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <form action="" method="POST">
                        <input type="hidden" name="productId" value="<?php echo htmlspecialchars($product['product_id']); ?>">
                        <button type="submit" name="confirmDelete" class="btn btn-outline-danger rounded-pill">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<?php include('include/footer.php')?>

</body>
</html>
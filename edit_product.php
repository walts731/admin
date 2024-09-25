<?php
include('include/connect.php');

// Check if product ID is set in the URL
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Fetch product details from the database
    $sql = "SELECT * FROM products WHERE product_id = $product_id";
    $result = $conn->query($sql);

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body>
<?php include ('include/nav.php')?>
    <div class="container mt-5">
        <h2 class="mb-4">Edit Product</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="productId" value="<?php echo $product['product_id']; ?>">
            <div class="mb-3">
                <label for="productName" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="productName" name="productName" value="<?php echo $product['product_name']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="productDescription" class="form-label">Product Description</label>
                <textarea class="form-control" id="productDescription" name="productDescription" rows="3" required><?php echo $product['product_description']; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="productStock" class="form-label">Stock</label>
                <input type="number" class="form-control" id="productStock" name="productStock" value="<?php echo $product['stock']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="productPrice" class="form-label">Price</label>
                <input type="text" class="form-control" id="productPrice" name="productPrice" value="<?php echo $product['price']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="productImage" class="form-label">Image</label>
                <input type="file" class="form-control" id="productImage" name="productImage">
            </div>
            <button type="submit" name="updateProduct" class="btn btn-primary">Update Product</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
if (isset($_POST['updateProduct'])) {
    $productId = $_POST['productId'];
    $productName = $_POST['productName'];
    $productDescription = $_POST['productDescription'];
    $productStock = $_POST['productStock'];
    $productPrice = $_POST['productPrice'];

    // Optional: Handle image upload if a new image is provided
    if ($_FILES['productImage']['name']) {
        $image = $_FILES['productImage']['name'];
        $target = "img/" . basename($image);
        move_uploaded_file($_FILES['productImage']['tmp_name'], $target);
        $sql = "UPDATE products SET product_name='$productName', product_description='$productDescription', stock='$productStock', price='$productPrice', image_url='$target' WHERE product_id='$productId'";
    } else {
        $sql = "UPDATE products SET product_name='$productName', product_description='$productDescription', stock='$productStock', price='$productPrice' WHERE product_id='$productId'";
    }

    if ($conn->query($sql) === TRUE) {
        // Redirect to product.php after successful update
        header("Location: products.php");
        exit(); // Always call exit after header redirection
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

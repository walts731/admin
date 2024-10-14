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
<body style="background-color: #D6EFD8;">
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
                <label for="productPrice" class="form-label">Price</label>
                <input type="text" class="form-control" id="productPrice" name="productPrice" value="<?php echo $product['price']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="productCategory" class="form-label">Category</label>
                <select class="form-select" id="productCategory" name="productCategory" required>
                    <option value="">Select Category</option>
                    <?php
                    // Fetch categories from the database
                    $categoriesSql = "SELECT * FROM categories";
                    $categoriesResult = $conn->query($categoriesSql);

                    if ($categoriesResult->num_rows > 0) {
                        while ($categoryRow = $categoriesResult->fetch_assoc()) {
                            // Check if the current category is the one selected for this product
                            $selected = ($categoryRow['category_id'] == $product['category_id']) ? 'selected' : '';
                            echo "<option value='" . $categoryRow['category_id'] . "' $selected>" . $categoryRow['category_name'] . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="productImage" class="form-label">Image</label>
                <input type="file" class="form-control" id="productImage" name="productImage">
                <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['product_name']; ?>" width="100" class="mt-2">
            </div>
            <button type="submit" name="updateProduct" class="btn rounded-pill" style="background-color: #85AF97; color: white; border: none; cursor: pointer;" 
onmouseover="this.style.backgroundColor='#6E947E'" 
onmouseout="this.style.backgroundColor='#85AF97'">Update Product</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <?php include('include/footer.php')?>
</body>
</html>

<?php
if (isset($_POST['updateProduct'])) {
    $productId = $_POST['productId'];
    $productName = $_POST['productName'];
    $productDescription = $_POST['productDescription'];
    $productPrice = $_POST['productPrice'];
    $productCategory = $_POST['productCategory']; // Get the category ID from the dropdown

    // Optional: Handle image upload if a new image is provided
    if ($_FILES['productImage']['name']) {
        $image = $_FILES['productImage']['name'];
        $target = "img/" . basename($image);
        move_uploaded_file($_FILES['productImage']['tmp_name'], $target);
        $sql = "UPDATE products SET product_name='$productName', product_description='$productDescription', price='$productPrice', image_url='$target', category_id='$productCategory' WHERE product_id='$productId'";
    } else {
        $sql = "UPDATE products SET product_name='$productName', product_description='$productDescription', price='$productPrice', category_id='$productCategory' WHERE product_id='$productId'";
    }

    if ($conn->query($sql) === TRUE) {
        // Redirect to product.php after successful update
        header("Location: product.php");
        exit(); // Always call exit after header redirection
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
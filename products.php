<?php include ('include/connect.php')?>
<?php
// Add Product Function
if (isset($_POST['addProduct'])) {
    $productName = $_POST['productName'];
    $productDescription = $_POST['productDescription'];
    $productStock = $_POST['productStock'];
    $productPrice = $_POST['productPrice'];
    $productImage = $_FILES['productImage']['name'];

    // File upload handling
    $target_dir = "img/";
    $target_file = $target_dir . basename($productImage);
    move_uploaded_file($_FILES['productImage']['tmp_name'], $target_file);

    // Insert into products table
    $sql = "INSERT INTO products (product_name, product_description, stock, price, image_url)
            VALUES ('$productName', '$productDescription', '$productStock', '$productPrice', '$target_file')";

    if ($conn->query($sql) === TRUE) {
        $productId = $conn->insert_id; // Get the last inserted product_id

        // Insert into inventory table
        $inventorySql = "INSERT INTO inventory (product_id, stock, cost) VALUES (?, ?, ?)";
        $cost = $productPrice; // Assuming the cost is the same as product price for simplicity
        $inventoryStmt = $conn->prepare($inventorySql);
        $inventoryStmt->bind_param("iid", $productId, $productStock, $cost);

        if ($inventoryStmt->execute()) {
            echo "New product added successfully and inventory updated!";
        } else {
            echo "Error inserting into inventory: " . $inventoryStmt->error;
        }

        $inventoryStmt->close();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/product.css">
</head>

<body>
    <!-- Navigation Bar -->
    <?php include ('include/nav.php')?>

    <div class="container mt-5">
        <h2 class="mb-4">Products Management</h2>
        <!-- Button to trigger modal -->
        <button class="btn btn-success btn-custom mb-3" data-bs-toggle="modal" data-bs-target="#addProductModal">Add New Product</button>
        
        <!-- Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <form method="POST" enctype="multipart/form-data">
                    <!-- Product Name -->
                    <div class="mb-3">
                        <label for="productName" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="productName" name="productName" placeholder="Enter product name" required>
                    </div>

                    <!-- Product Description -->
                    <div class="mb-3">
                        <label for="productDescription" class="form-label">Product Description</label>
                        <textarea class="form-control" id="productDescription" name="productDescription" rows="3" placeholder="Enter product description" required></textarea>
                    </div>

                    <!-- Product Stock -->
                    <div class="mb-3">
                        <label for="productStock" class="form-label">Stock</label>
                        <input type="number" class="form-control" id="productStock" name="productStock" placeholder="Enter available stock" required>
                    </div>

                    <!-- Product Price -->
                    <div class="mb-3">
                        <label for="productPrice" class="form-label">Price</label>
                        <input type="text" class="form-control" id="productPrice" name="productPrice" placeholder="Enter product price" required>
                    </div>

                    <!-- Product Image -->
                    <div class="mb-3">
                        <label for="productImage" class="form-label">Image</label>
                        <input type="file" class="form-control" id="productImage" name="productImage" required>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="addProduct" class="btn btn-primary">Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


       <!-- Table -->
       <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Image</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Rating</th>
                            <th>Availability</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch products from the database
                        $sql = "SELECT * FROM products";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            // Output data for each product
                            while ($row = $result->fetch_assoc()) {
                                // Determine availability badge color
                                $availability = ($row['stock'] > 0) ? '<span class="badge bg-success">In Stock</span>' : '<span class="badge bg-danger">Out of Stock</span>';
                        
                                echo "<tr>
                                    <td><img src='" . $row['image_url'] . "' alt='" . $row['product_name'] . "' width='80' class='img-fluid'></td>
                                    <td>" . $row['product_name'] . "</td>
                                    <td>₱" . number_format($row['price'], 2) . "</td>
                                    <td>⭐⭐⭐⭐</td> <!-- Assuming a static 4-star rating for now -->
                                    <td>$availability</td>
                                    <td>
                                        <a href='edit_product.php?id=" . $row['product_id'] . "' class='btn btn-edit rounded-pill btn-custom me-2'>Edit</a>
                                        <a href='delete_product.php?id=" . $row['product_id'] . "' class='btn btn-delete rounded-pill btn-custom '>Delete</a>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No products available</td></tr>";
                        }
                        
                        

                        $conn->close(); // Close the database connection
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

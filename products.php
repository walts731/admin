<?php include ('include/connect.php')?>
<?php
// Add Product Function
if (isset($_POST['addProduct'])) {
    $productName = $_POST['productName'];
    $productDescription = $_POST['productDescription'];
    $productPrice = $_POST['productPrice'];
    $productImage = $_FILES['productImage']['name'];
    $productCategory = $_POST['productCategory']; // Get category ID from dropdown

    // File upload handling
    $target_dir = "img/";
    $target_file = $target_dir . basename($productImage);
    move_uploaded_file($_FILES['productImage']['tmp_name'], $target_file);

    // Insert into products table
    $sql = "INSERT INTO products (product_name, product_description, price, image_url, product_status, category_id)
            VALUES ('$productName', '$productDescription', '$productPrice', '$target_file', 'active', '$productCategory')";

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

// Update Product Status (Active/Inactive)
if (isset($_GET['action']) && $_GET['action'] == 'updateStatus') {
    $productId = $_GET['id'];
    $status = $_GET['status']; // Get the new status (active or inactive)

    $updateSql = "UPDATE products SET product_status = '$status' WHERE product_id = $productId";

    if ($conn->query($updateSql) === TRUE) {
        // Redirect back to the product list page after successful update
        header("Location: products.php"); 
        exit; 
    } else {
        echo "Error updating product status: " . $conn->error;
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

<body style="background-color: #D6EFD8;">
    <!-- Navigation Bar -->
    <?php include ('include/nav.php')?>

    <div class="container mt-5">
    <h1 class="text-center mb-4">Products Management</h1>
        <!-- Button to trigger modal -->
        <button class="btn btn-success btn-custom mb-3 rounded-pill" data-bs-toggle="modal" data-bs-target="#addProductModal">Add New Product</button>

        <a href="add_category.php" class="btn btn-outline-success rounded-pill mb-3">Add Category</a>
        
        <!-- Search Bar -->
        <div class="mb-3">
            <form action="products.php" method="GET"> 
                <div class="input-group">
                    <input type="text" class="form-control" name="search" placeholder="Search by product name..." aria-label="Search">
                    <button class="btn btn-outline-secondary" type="submit">Search</button>
                </div>
            </form>
        </div>

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

                    <!-- Product Price -->
                    <div class="mb-3">
                        <label for="productPrice" class="form-label">Price</label>
                        <input type="text" class="form-control" id="productPrice" name="productPrice" placeholder="Enter product price" required>
                    </div>

                    <!-- Product Category -->
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
                                    echo "<option value='" . $categoryRow['category_id'] . "'>" . $categoryRow['category_name'] . "</option>";
                                }
                            }
                            ?>
                        </select>
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
                            <th>Status</th>
                            <th>Category</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
    <?php
    // Fetch products and stock from the database using JOIN between products and inventory tables
    $sql = "SELECT p.*, i.stock, c.category_name FROM products p 
            LEFT JOIN inventory i ON p.product_id = i.product_id
            LEFT JOIN categories c ON p.category_id = c.category_id";
    
    // Apply search filter if a search term is provided
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $searchTerm = $_GET['search'];
        $sql .= " WHERE p.product_name LIKE '%$searchTerm%'";
    }

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Output data for each product
        while ($row = $result->fetch_assoc()) {
            // Determine availability badge color based on stock from the inventory table
            $availability = ($row['stock'] > 0) ? '<span class="badge bg-success">In Stock</span>' : '<span class="badge bg-danger">Out of Stock</span>';

            // Determine status badge color based on product_status from the products table
            $statusBadge = ($row['product_status'] == 'active') ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>';

            echo "<tr>
                <td><img src='" . $row['image_url'] . "' alt='" . $row['product_name'] . "' width='80' class='img-fluid'></td>
                <td>" . $row['product_name'] . "</td>
                <td>₱" . number_format($row['price'], 2) . "</td>
                <td>⭐⭐⭐⭐</td> <!-- Assuming a static 4-star rating for now -->
                <td>$availability</td>
                <td>$statusBadge</td>
                <td>" . $row['category_name'] . "</td>
                <td>
                    <a href='edit_product.php?id=" . $row['product_id'] . "' class='btn btn-edit rounded-pill btn-custom me-2'>Edit</a>
                    <div class='form-check form-switch'>
                        <input class='form-check-input status-switch' type='checkbox' id='statusSwitch" . $row['product_id'] . "' data-product-id='" . $row['product_id'] . "' " . (($row['product_status'] == 'active') ? 'checked' : '') . ">
                        <label class='form-check-label' for='statusSwitch" . $row['product_id'] . "'></label>
                    </div>
                </td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='8'>No products available</td></tr>";
    }

    $conn->close(); // Close the database connection
    ?>
</tbody>

                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <?php include('include/footer.php')?>
    <script>
        // Add event listener for status switch
        const statusSwitches = document.querySelectorAll('.status-switch');
        statusSwitches.forEach(switchElement => {
            switchElement.addEventListener('change', function() {
                const productId = this.dataset.productId;
                const newStatus = this.checked ? 'active' : 'inactive';

                // AJAX request to update the status
                fetch('?action=updateStatus&id=' + productId + '&status=' + newStatus)
                    .then(response => {
                        if (response.ok) {
                            // Refresh the page after successful update
                            location.reload();
                        } else {
                            console.error('Error updating status');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            });
        });
    </script>
</body>
</html>
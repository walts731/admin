<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/product_style.css">
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
                    <div class="modal-header">
                        <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="mb-3">
                                <label for="productName" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="productName" placeholder="Enter product name">
                            </div>
                            <div class="mb-3">
                                <label for="productPrice" class="form-label">Price</label>
                                <input type="text" class="form-control" id="productPrice" placeholder="Enter product price">
                            </div>
                            
                            <div class="mb-3">
                                <label for="productImage" class="form-label">Image </label>
                                <input type="file" class="form-control" id="productImage" placeholder="Enter image URL">
                            </div>
                            <div class="mb-3">
                                <label for="productAvailability" class="form-label">Availability</label>
                                <select class="form-select" id="productAvailability">
                                    <option value="inStock">In Stock</option>
                                    <option value="outOfStock">Out of Stock</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save Product</button>
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
                        <tr>
                            <td><img src="img/menu 1.jpg" alt="Mini Snackbox" width="80" class="img-fluid"></td>
                            <td>Mini Snackbox</td>
                            <td>₱69.00</td>
                            <td>⭐⭐⭐⭐</td>
                            <td><span class="badge bg-success">In Stock</span></td>
                            <td>
                                <button class="btn btn-primary btn-custom me-2">Edit</button>
                                <button class="btn btn-danger btn-custom">Delete</button>
                            </td>
                        </tr>
                        <!-- More products go here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

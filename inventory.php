<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body>
    <!-- Navigation Bar -->
    <?php include ('include/nav.php')?>

    <div class="container mt-5">
        <h2>Inventory Management</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Stock Quantity</th>
                    <th>Stock Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Mini Snackbox</td>
                    <td>50</td>
                    <td><span class="badge bg-success">In Stock</span></td>
                    <td><button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#updateStockModal">Update Stock</button></td>
                </tr>
                <tr>
                    <td>Ham Overload</td>
                    <td>10</td>
                    <td><span class="badge bg-warning">Low Stock</span></td>
                    <td><button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#updateStockModal">Update Stock</button></td>
                </tr>
                <tr>
                    <td>Chicken Wrap</td>
                    <td>0</td>
                    <td><span class="badge bg-danger">Out of Stock</span></td>
                    <td><button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#updateStockModal">Update Stock</button></td>
                </tr>
                <!-- More inventory items go here -->
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="updateStockModal" tabindex="-1" aria-labelledby="updateStockModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStockModalLabel">Update Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        
                        <div class="mb-3">
                            <label for="stockQuantity" class="form-label">Stock Quantity</label>
                            <input type="number" class="form-control" id="stockQuantity" placeholder="Stock Quantity">
                        </div>
                        <!-- Add other fields as needed -->
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

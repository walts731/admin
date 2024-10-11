<?php
include('include/connect.php');

// Check and create inventory table structure if it doesn't exist
$checkTableSql = "CREATE TABLE IF NOT EXISTS inventory (
    inventory_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    stock INT DEFAULT 0,
    cost DECIMAL(10, 2) NOT NULL,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id)
)";
$conn->query($checkTableSql);

// Fetch products from the database
$sql = "SELECT `product_id`, `product_name`, `product_description`, `price`, `image_url`, `created_at` FROM `products`";
$result = $conn->query($sql);

// Check if the form has been submitted to update a product in the inventory
if (isset($_POST['updateInventory'])) {
    $productId = $_POST['productId'];
    $stock = $_POST['stock'];
    $cost = $_POST['cost']; // Assuming cost is provided in the form

    // Update the selected product in the inventory table
    $updateSql = "UPDATE inventory SET stock = ?, cost = ? WHERE product_id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("idi", $stock, $cost, $productId);

    if ($stmt->execute()) {
        // **Update the inventory table's stock, not the products table.**
        $updateInventorySql = "UPDATE inventory SET stock = ? WHERE product_id = ?";
        $updateInventoryStmt = $conn->prepare($updateInventorySql);
        $updateInventoryStmt->bind_param("ii", $stock, $productId);
        
        if ($updateInventoryStmt->execute()) {
            echo "<script>alert('Inventory updated successfully!');</script>";
        } else {
            echo "<script>alert('Error updating inventory: " . $conn->error . "');</script>";
        }
        
        $updateInventoryStmt->close();
        
    } else {
        echo "<script>alert('Error updating inventory: " . $conn->error . "');</script>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/inventory.css">

</head>
<body style="background-color: #D6EFD8;">
    <!-- Navigation Bar -->
    <?php include('include/nav.php') ?>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Inventory Management</h2>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="" style="background-color: #508D4E; color: white;">
                    <tr>
                        <th scope="col">Product Name</th>
                        <th scope="col">Current Stock</th>
                        <th scope="col">Cost</th>
                        <th scope="col" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch inventory items to display
                    $inventorySql = "SELECT inv.*, p.product_name FROM inventory inv JOIN products p ON inv.product_id = p.product_id";
                    $inventoryResult = $conn->query($inventorySql);

                    if ($inventoryResult->num_rows > 0) {
                        while ($row = $inventoryResult->fetch_assoc()) {
                            echo "<tr>
                                <td>" . htmlspecialchars($row['product_name']) . "</td>
                                <td>" . htmlspecialchars($row['stock']) . "</td>
                                <td>" . htmlspecialchars($row['cost']) . "</td>
                                <td class='text-center'>
                                    <form method='POST' action=''>
                                        <input type='hidden' name='productId' value='" . $row['product_id'] . "'>
                                        <div class='input-group'>
                                            <input type='number' name='stock' class='form-control' placeholder='Stock Quantity' value='" . htmlspecialchars($row['stock']) . "' required>
                                            <input type='number' step='0.01' name='cost' class='form-control' placeholder='Cost' value='" . htmlspecialchars($row['cost']) . "' required>
                                            <button type='submit' name='updateInventory' class='btn btn-inventory'>Update</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No inventory items available</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
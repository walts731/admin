<?php
include('include/connect.php');

// Handle adding a new payment method
if (isset($_POST['add_payment_method'])) {
    $methodName = $_POST['method_name'];
    $description = $_POST['description'];
    $imagePath = $_FILES['image_path']['name'];

    // Upload the image (if provided)
    if (!empty($imagePath)) {
        $targetDir = "img/payment_methods/"; // Specify the directory for payment method images
        $targetFile = $targetDir . basename($imagePath);
        if (move_uploaded_file($_FILES['image_path']['tmp_name'], $targetFile)) {
            // Image uploaded successfully
        } else {
            // Handle image upload error
            echo "Error uploading payment method image.";
        }
    }

    // Insert new payment method into the database
    $sql = "INSERT INTO `payment_methods` (`method_name`, `description`, `image_path`) 
            VALUES ('$methodName', '$description', '$imagePath')";

    if (mysqli_query($conn, $sql)) {
        // Redirect to the payment method management page or show a success message
        header('Location: payment_method_management.php');
        exit;
    } else {
        // Handle database insertion error
        echo "Error adding payment method: " . mysqli_error($conn);
    }
}

// Handle updating an existing payment method
if (isset($_POST['update_payment_method'])) {
    $paymentMethodId = $_POST['payment_method_id'];
    $methodName = $_POST['method_name'];
    $description = $_POST['description'];
    $imagePath = $_FILES['image_path']['name'];

    // Upload the image (if provided)
    if (!empty($imagePath)) {
        $targetDir = "img/payment_methods/"; // Specify the directory for payment method images
        $targetFile = $targetDir . basename($imagePath);
        if (move_uploaded_file($_FILES['image_path']['tmp_name'], $targetFile)) {
            // Image uploaded successfully
        } else {
            // Handle image upload error
            echo "Error uploading payment method image.";
        }
    }

    // Update the payment method in the database
    $sql = "UPDATE `payment_methods` 
            SET `method_name` = '$methodName', 
                `description` = '$description',
                `image_path` = '$imagePath' 
            WHERE `payment_method_id` = '$paymentMethodId'";

    if (mysqli_query($conn, $sql)) {
        // Redirect to the payment method management page or show a success message
        header('Location: payment_method_management.php');
        exit;
    } else {
        // Handle database update error
        echo "Error updating payment method: " . mysqli_error($conn);
    }
}

// Handle deleting a payment method
if (isset($_POST['delete_payment_method'])) {
    $paymentMethodId = $_POST['payment_method_id'];

    // Delete the payment method from the database
    $sql = "DELETE FROM `payment_methods` WHERE `payment_method_id` = '$paymentMethodId'";

    if (mysqli_query($conn, $sql)) {
        // Redirect to the payment method management page or show a success message
        header('Location: payment_method_management.php');
        exit;
    } else {
        // Handle database deletion error
        echo "Error deleting payment method: " . mysqli_error($conn);
    }
}

// Fetch all payment methods from the database
$sql = "SELECT * FROM `payment_methods`";
$result = mysqli_query($conn, $sql);
$paymentMethods = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Method Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/payment_methods.css">
</head>
<body>
    <!-- Navigation Bar -->
    <?php include ('include/nav.php')?> 

    <div class="container mt-5">
        <h2 class="text-center mb-4">Payment Method Management</h2>

        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPaymentMethodModal">
            Add New Payment Method
        </button>

        <!-- Modal -->
        <div class="modal fade" id="addPaymentMethodModal" tabindex="-1" aria-labelledby="addPaymentMethodModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addPaymentMethodModalLabel">Add New Payment Method</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="payment_method_management.php" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="method_name">Payment Method Name:</label>
                                <input type="text" class="form-control" id="method_name" name="method_name" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Description:</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="image_path">Image (Optional):</label>
                                <input type="file" class="form-control-file" id="image_path" name="image_path">
                            </div>
                            <button type="submit" class="btn btn-success" name="add_payment_method">Add Payment Method</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <h3>Available Payment Methods</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Method Name</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($paymentMethods as $method) { ?>
                    <tr>
                        <td><?php echo $method['payment_method_id']; ?></td>
                        <td><?php echo $method['method_name']; ?></td>
                        <td><?php echo $method['description']; ?></td>
                        <td>
                            <?php if (!empty($method['image_path'])) { ?>
                                <img src="img/<?php echo $method['image_path']; ?>" alt="Payment Method Image" width="50" height="50">
                            <?php } ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editPaymentMethodModal" 
                                    data-payment-method-id="<?php echo $method['payment_method_id']; ?>" 
                                    data-method-name="<?php echo $method['method_name']; ?>" 
                                    data-description="<?php echo $method['description']; ?>"
                                    data-image-path="img/payment_methods/<?php echo $method['image_path']; ?>">
                                Edit
                            </button>
                            <form method="POST" action="payment_method_management.php" style="display: inline;">
                                <input type="hidden" name="payment_method_id" value="<?php echo $method['payment_method_id']; ?>">
                                <button type="submit" class="btn btn-danger" name="delete_payment_method" onclick="return confirm('Are you sure you want to delete this payment method?');">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Modal for Editing Payment Method -->
    <div class="modal fade" id="editPaymentMethodModal" tabindex="-1" aria-labelledby="editPaymentMethodModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPaymentMethodModalLabel">Edit Payment Method</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="payment_method_management.php" enctype="multipart/form-data">
                        <input type="hidden" name="payment_method_id" id="paymentMethodId">
                        <div class="form-group">
                            <label for="method_name">Payment Method Name:</label>
                            <input type="text" class="form-control" id="method_name" name="method_name" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description:</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="image_path">Image (Optional):</label>
                            <input type="file" class="form-control-file" id="image_path" name="image_path">
                        </div>
                        <button type="submit" class="btn btn-success" name="update_payment_method">Update Payment Method</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const editPaymentMethodModal = document.getElementById('editPaymentMethodModal');
        editPaymentMethodModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const paymentMethodId = button.getAttribute('data-payment-method-id');
            const methodName = button.getAttribute('data-method-name');
            const description = button.getAttribute('data-description');
            const imagePath = button.getAttribute('data-image-path');

            const modalPaymentMethodId = editPaymentMethodModal.querySelector('#paymentMethodId');
            const modalMethodName = editPaymentMethodModal.querySelector('#method_name');
            const modalDescription = editPaymentMethodModal.querySelector('#description');

            modalPaymentMethodId.value = paymentMethodId;
            modalMethodName.value = methodName;
            modalDescription.value = description;
        });
    </script>
    <?php include('include/footer.php')?>

</body>
</html>
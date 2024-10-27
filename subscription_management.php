<?php
include('include/connect.php');


// Handle adding a new subscription
if (isset($_POST['add_subscription'])) {
    $subscriptionPlan = $_POST['subscription_plan'];
    $status = $_POST['status'];
    $description = $_POST['subscription_description'];
    $price = $_POST['price'];
    $qrCodeImg = $_FILES['qr_code_img']['name'];

    // Upload QR code image (if provided)
    if (!empty($qrCodeImg)) {
        $targetDir = "img/";
        $targetFile = $targetDir . basename($qrCodeImg);
        if (move_uploaded_file($_FILES['qr_code_img']['tmp_name'], $targetFile)) {
            // Image uploaded successfully
        } else {
            // Handle image upload error
            echo "Error uploading QR code image.";
        }
    }

    // Insert new subscription into the database
    $sql = "INSERT INTO `subscriptions` (`subscription_plan`, `status`, `subscription_description`, `price`, `qr_code_img`) 
            VALUES ('$subscriptionPlan', '$status', '$description', '$price', '$qrCodeImg')";

    if (mysqli_query($conn, $sql)) {
        // Redirect to the subscription management page or show a success message
        header('Location: subscription_management.php'); 
        exit;
    } else {
        // Handle database insertion error
        echo "Error adding subscription: " . mysqli_error($conn);
    }
}

// Handle updating an existing subscription
if (isset($_POST['update_subscription'])) {
    $subscriptionId = $_POST['subscription_id'];
    $subscriptionPlan = $_POST['subscription_plan'];
    $status = $_POST['status'];
    $description = $_POST['subscription_description'];
    $price = $_POST['price'];
    $qrCodeImg = $_FILES['qr_code_img']['name'];

    // Upload QR code image (if provided)
    if (!empty($qrCodeImg)) {
        $targetDir = "uploads/qr_codes/";
        $targetFile = $targetDir . basename($qrCodeImg);
        if (move_uploaded_file($_FILES['qr_code_img']['tmp_name'], $targetFile)) {
            // Image uploaded successfully
        } else {
            // Handle image upload error
            echo "Error uploading QR code image.";
        }
    }

    // Update the subscription in the database
    $sql = "UPDATE `subscriptions` 
            SET `subscription_plan` = '$subscriptionPlan', 
                `status` = '$status', 
                `subscription_description` = '$description',
                `price` = '$price',
                `qr_code_img` = '$qrCodeImg' 
            WHERE `subscription_id` = '$subscriptionId'";

    if (mysqli_query($conn, $sql)) {
        // Redirect to the subscription management page or show a success message
        header('Location: subscription_management.php');
        exit;
    } else {
        // Handle database update error
        echo "Error updating subscription: " . mysqli_error($conn);
    }
}

// Handle deleting a subscription
if (isset($_POST['delete_subscription'])) {
    $subscriptionId = $_POST['subscription_id'];

    // Delete the subscription from the database
    $sql = "DELETE FROM `subscriptions` WHERE `subscription_id` = '$subscriptionId'";

    if (mysqli_query($conn, $sql)) {
        // Redirect to the subscription management page or show a success message
        header('Location: subscription_management.php');
        exit;
    } else {
        // Handle database deletion error
        echo "Error deleting subscription: " . mysqli_error($conn);
    }
}

// Fetch all subscriptions from the database
$sql = "SELECT * FROM `subscriptions`";
$result = mysqli_query($conn, $sql);
$subscriptions = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/subscriptions.css">
</head>
<body>
    <!-- Navigation Bar -->
    <?php include ('include/nav.php')?> 

    <div class="container mt-5">
        <h2 class="text-center mb-4">Subscription Management</h2>

        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubscriptionModal">
            Add New Subscription
        </button>

        <!-- Modal -->
        <div class="modal fade" id="addSubscriptionModal" tabindex="-1" aria-labelledby="addSubscriptionModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addSubscriptionModalLabel">Add New Subscription</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="subscription_management.php" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="subscription_plan">Subscription Plan:</label>
                                <input type="text" class="form-control" id="subscription_plan" name="subscription_plan" required>
                            </div>
                            <div class="form-group">
                                <label for="status">Status:</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="subscription_description">Description:</label>
                                <textarea class="form-control" id="subscription_description" name="subscription_description" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="price">Price:</label>
                                <input type="number" class="form-control" id="price" name="price" required>
                            </div>
                            <div class="form-group">
                                <label for="qr_code_img">QR Code Image:</label>
                                <input type="file" class="form-control-file" id="qr_code_img" name="qr_code_img">
                            </div>
                            <button type="submit" class="btn btn-success" name="add_subscription">Add Subscription</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <h3>Available Subscriptions</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Plan</th>
                    <th>Status</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>QR Code</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subscriptions as $subscription) { ?>
                    <tr>
                        <td><?php echo $subscription['subscription_id']; ?></td>
                        <td><?php echo $subscription['subscription_plan']; ?></td>
                        <td>
                            <?php if ($subscription['status'] == 'active') { ?>
                                <span class="badge bg-success">Active</span>
                            <?php } else { ?>
                                <span class="badge bg-danger">Inactive</span>
                            <?php } ?>
                        </td>
                        <td><?php echo $subscription['subscription_description']; ?></td>
                        <td><?php echo $subscription['price']; ?></td>
                        <td>
                            <?php if (!empty($subscription['qr_code_img'])) { ?>
                                <img src="img/<?php echo $subscription['qr_code_img']; ?>" alt="QR Code" width="50" height="50">
                            <?php } ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editSubscriptionModal" 
                                    data-subscription-id="<?php echo $subscription['subscription_id']; ?>" 
                                    data-subscription-plan="<?php echo $subscription['subscription_plan']; ?>" 
                                    data-status="<?php echo $subscription['status']; ?>" 
                                    data-description="<?php echo $subscription['subscription_description']; ?>"
                                    data-price="<?php echo $subscription['price']; ?>"
                                    data-qr-code-img="img/<?php echo $subscription['qr_code_img']; ?>">
                                Edit
                            </button>
                            <form method="POST" action="subscription_management.php" style="display: inline;">
                                <input type="hidden" name="subscription_id" value="<?php echo $subscription['subscription_id']; ?>">
                                <button type="submit" class="btn btn-danger" name="delete_subscription" onclick="return confirm('Are you sure you want to delete this subscription?');">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Modal for Editing Subscription -->
    <div class="modal fade" id="editSubscriptionModal" tabindex="-1" aria-labelledby="editSubscriptionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSubscriptionModalLabel">Edit Subscription</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="subscription_management.php" enctype="multipart/form-data">
                        <input type="hidden" name="subscription_id" id="subscriptionId">
                        <div class="form-group">
                            <label for="subscription_plan">Subscription Plan:</label>
                            <input type="text" class="form-control" id="subscription_plan" name="subscription_plan" required>
                        </div>
                        <div class="form-group">
                            <label for="status">Status:</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="subscription_description">Description:</label>
                            <textarea class="form-control" id="subscription_description" name="subscription_description" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="price">Price:</label>
                            <input type="number" class="form-control" id="price" name="price" required>
                        </div>
                        <div class="form-group">
                            <label for="qr_code_img">QR Code Image:</label>
                            <input type="file" class="form-control-file" id="qr_code_img" name="qr_code_img">
                        </div>
                        <button type="submit" class="btn btn-success" name="update_subscription">Update Subscription</button>
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
        const editSubscriptionModal = document.getElementById('editSubscriptionModal');
        editSubscriptionModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const subscriptionId = button.getAttribute('data-subscription-id');
            const subscriptionPlan = button.getAttribute('data-subscription-plan');
            const status = button.getAttribute('data-status');
            const description = button.getAttribute('data-description');
            const price = button.getAttribute('data-price');
            const qrCodeImg = button.getAttribute('data-qr-code-img');

            const modalSubscriptionId = editSubscriptionModal.querySelector('#subscriptionId');
            const modalSubscriptionPlan = editSubscriptionModal.querySelector('#subscription_plan');
            const modalStatus = editSubscriptionModal.querySelector('#status');
            const modalDescription = editSubscriptionModal.querySelector('#subscription_description');
            const modalPrice = editSubscriptionModal.querySelector('#price');

            modalSubscriptionId.value = subscriptionId;
            modalSubscriptionPlan.value = subscriptionPlan;
            modalStatus.value = status;
            modalDescription.value = description;
            modalPrice.value = price;
        });
    </script>
    <?php include('include/footer.php')?>

</body>
</html>
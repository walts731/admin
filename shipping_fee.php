<?php
    include('include/connect.php');
    

    // Handle form submission for adding new shipping fees
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['add_fee'])) {
            $city = mysqli_real_escape_string($conn, $_POST['city']);
            $shipping_rate = mysqli_real_escape_string($conn, $_POST['shipping_rate']);

            // Validate shipping rate (ensure it's a positive number)
            if ($shipping_rate <= 0) {
                $error = "Shipping rate must be a positive number.";
            } else {
                // Insert the new shipping fee into the database
                $sql = "INSERT INTO `shipping_fee` (`city`, `shipping_rate`) VALUES ('$city', $shipping_rate)";
                $result = mysqli_query($conn, $sql);

                if ($result) {
                    // Success!
                    $success = "Shipping fee added successfully.";
                } else {
                    $error = "Failed to add shipping fee. Please try again.";
                }
            }
        } else if (isset($_POST['edit_fee'])) {
            $fee_id = mysqli_real_escape_string($conn, $_POST['fee_id']);
            $city = mysqli_real_escape_string($conn, $_POST['city']);
            $shipping_rate = mysqli_real_escape_string($conn, $_POST['shipping_rate']);

            // Validate shipping rate (ensure it's a positive number)
            if ($shipping_rate <= 0) {
                $error = "Shipping rate must be a positive number.";
            } else {
                // Update the shipping fee in the database
                $sql = "UPDATE `shipping_fee` SET `city` = '$city', `shipping_rate` = $shipping_rate WHERE `fee_id` = $fee_id";
                $result = mysqli_query($conn, $sql);

                if ($result) {
                    // Success!
                    $success = "Shipping fee updated successfully.";
                } else {
                    $error = "Failed to update shipping fee. Please try again.";
                }
            }
        } else if (isset($_POST['delete_fee'])) {
            $fee_id = mysqli_real_escape_string($conn, $_POST['fee_id']);

            // Delete the shipping fee from the database
            $sql = "DELETE FROM `shipping_fee` WHERE `fee_id` = $fee_id";
            $result = mysqli_query($conn, $sql);

            if ($result) {
                // Success!
                $success = "Shipping fee deleted successfully.";
            } else {
                $error = "Failed to delete shipping fee. Please try again.";
            }
        }
    }

    // Fetch existing shipping fees from the database (with search functionality)
    $search_term = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
    if (!empty($search_term)) {
        $sql = "SELECT * FROM `shipping_fee` WHERE `city` LIKE '%$search_term%' OR `shipping_rate` LIKE '%$search_term%'";
    } else {
        $sql = "SELECT * FROM `shipping_fee`";
    }
    $result = mysqli_query($conn, $sql);
    $shipping_fees = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $shipping_fees[] = $row;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DELIDAZE - Manage Shipping Fees</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>
<?php include ('include/nav.php')?> 

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <h2>Manage Shipping Fees</h2>

                <?php if (isset($error)) { ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error; ?>
                    </div>
                <?php } ?>

                <?php if (isset($success)) { ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $success; ?>
                    </div>
                <?php } ?>

                <!-- Search Form -->
                <form method="GET" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="search" placeholder="Search by city or rate" value="<?php echo $search_term; ?>">
                        <button class="btn btn-outline-secondary" type="submit">Search</button>
                    </div>
                </form>

                <!-- Form for adding new shipping fees -->
                <h3>Add New Shipping Fee</h3>
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <input type="hidden" name="add_fee" value="1">
                    <div class="form-group">
                        <label for="city">City:</label>
                        <input type="text" class="form-control" id="city" name="city" required>
                    </div>
                    <div class="form-group">
                        <label for="shipping_rate">Shipping Rate:</label>
                        <input type="number" class="form-control" id="shipping_rate" name="shipping_rate" step="0.01" required>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Add Shipping Fee</button>
                </form>

                <!-- Display existing shipping fees -->
                <h3>Current Shipping Fees</h3>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>City</th>
                            <th>Shipping Rate</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($shipping_fees)): ?>
                            <?php foreach ($shipping_fees as $fee): ?>
                                <!-- Modal for editing shipping fee -->
                                <div class="modal fade" id="editFeeModal<?php echo $fee['fee_id']; ?>" tabindex="-1" aria-labelledby="editFeeModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editFeeModalLabel">Edit Shipping Fee</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                                    <input type="hidden" name="edit_fee" value="1">
                                                    <input type="hidden" name="fee_id" value="<?php echo $fee['fee_id']; ?>">
                                                    <div class="form-group">
                                                        <label for="city">City:</label>
                                                        <input type="text" class="form-control" id="city" name="city" value="<?php echo $fee['city']; ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="shipping_rate">Shipping Rate:</label>
                                                        <input type="number" class="form-control" id="shipping_rate" name="shipping_rate" step="0.01" value="<?php echo $fee['shipping_rate']; ?>" required>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <tr>
                                    <td><?php echo $fee['city']; ?></td>
                                    <td><?php echo $fee['shipping_rate']; ?></td>
                                    <td>
                                        <!-- Edit button -->
                                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editFeeModal<?php echo $fee['fee_id']; ?>">Edit</button>
                                        <!-- Delete button -->
                                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" style="display: inline;">
                                            <input type="hidden" name="delete_fee" value="1">
                                            <input type="hidden" name="fee_id" value="<?php echo $fee['fee_id']; ?>">
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this shipping fee?');">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3">No shipping fees found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
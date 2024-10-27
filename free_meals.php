<?php
    include('include/connect.php');
    session_start();

    // Check if the admin is logged in
    if (!isset($_SESSION['admin_id'])) {
        header('Location: admin_login.php');
        exit;
    }

    // Handle delivery status update
    if (isset($_POST['update_delivery_status'])) {
        $free_meal_id = $_POST['free_meal_id'];
        $delivery_status = $_POST['delivery_status'];

        // Update the delivery status in the free_meals table
        $sql = "UPDATE `free_meals` SET `delivery_status` = '$delivery_status' WHERE `id` = $free_meal_id";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            // Update the delivery date if the status is 'Delivered'
            if ($delivery_status == 'Delivered') {
                $sql = "UPDATE `free_meals` SET `delivery_date` = NOW() WHERE `id` = $free_meal_id";
                mysqli_query($conn, $sql);
            }

            // Redirect to the same page (free_meals.php)
            header('Location: free_meals.php'); 
            exit;
        } else {
            // Handle the error if the update fails
            $error = "Failed to update delivery status. Please try again.";
        }
    }

    // Get all free meal claims (not just pending)
    $sql = "SELECT `id`, `user_id`, `delivery_address`, `contact_number`, `delivery_status`, `delivery_date` FROM `free_meals`";
    $result = mysqli_query($conn, $sql);

    // Handle search
    $search_query = "";
    $search_result = $result; 
    if (isset($_POST['search'])) {
        $search_query = $_POST['search_query'];

        // Search by Delivery Address, Contact Number, or User ID
        $sql = "SELECT `id`, `user_id`, `delivery_address`, `contact_number`, `delivery_status`, `delivery_date` FROM `free_meals` WHERE `delivery_address` LIKE '%$search_query%' OR `contact_number` LIKE '%$search_query%' OR `user_id` = '$search_query'";
        $search_result = mysqli_query($conn, $sql);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DELIDAZE - Admin - Delivery Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

    <?php include('include/nav.php')?>

    <div class="container mt-5">
        <h2>Delivery Management</h2>

        <form method="POST" action="free_meals.php">
            <div class="input-group mb-3">
                <input type="text" class="form-control" name="search_query" placeholder="Search by Address, Contact, or User ID" value="<?php echo $search_query; ?>">
                <button class="btn btn-outline-secondary" type="submit" name="search">Search</button>
            </div>
        </form>

        <?php if (isset($error)) { ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php } ?>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User ID</th>
                    <th>Delivery Address</th>
                    <th>Contact Number</th>
                    <th>Delivery Status</th>
                    <th>Delivery Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($search_result)) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['user_id']; ?></td>
                        <td><?php echo $row['delivery_address']; ?></td>
                        <td><?php echo $row['contact_number']; ?></td>
                        <td><?php echo $row['delivery_status']; ?></td>
                        <td><?php echo ($row['delivery_date'] != null) ? date('Y-m-d H:i:s', strtotime($row['delivery_date'])) : ''; ?></td>
                        <td>
                            <form method="POST" action="free_meals.php">
                                <input type="hidden" name="free_meal_id" value="<?php echo $row['id']; ?>">
                                <select name="delivery_status" class="form-select">
                                    <option value="Pending" <?php if ($row['delivery_status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                    <option value="Processing" <?php if ($row['delivery_status'] == 'Processing') echo 'selected'; ?>>Processing</option>
                                    <option value="Delivered" <?php if ($row['delivery_status'] == 'Delivered') echo 'selected'; ?>>Delivered</option>
                                    <option value="Cancelled" <?php if ($row['delivery_status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                                </select>
                                <button type="submit" class="btn btn-primary" name="update_delivery_status">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
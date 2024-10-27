<?php
    include('include/connect.php');
    

    // Handle adding a new free meal
    if (isset($_POST['add_meal'])) {
        $meal_name = $_POST['meal_name'];
        $description = $_POST['description'];
        $image_url = $_FILES['image_url']['name'];

        // Upload image (if provided)
        if (!empty($image_url)) {
            $target_dir = "img/";
            $target_file = $target_dir . basename($image_url);
            if (move_uploaded_file($_FILES['image_url']['tmp_name'], $target_file)) {
                // Image uploaded successfully
            } else {
                // Handle image upload error
                echo "Error uploading image.";
            }
        }

        // Insert new meal into the database
        $sql = "INSERT INTO `free_meals_available` (`meal_name`, `description`, `image_url`) VALUES ('$meal_name', '$description', '$image_url')";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            // Redirect to admin page or show success message
            header('Location: free_meals_items.php'); // Or display a success message
            exit;
        } else {
            // Handle database insertion error
            echo "Error adding meal.";
        }
    }

    // Handle disabling/enabling a free meal
    if (isset($_POST['disable_meal'])) {
        $meal_id = $_POST['meal_id'];
        $sql = "UPDATE `free_meals_available` SET `availability_status` = 'unavailable' WHERE `id` = $meal_id";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            // Redirect to admin page or show success message
            header('Location: free_meals_items.php'); // Or display a success message
            exit;
        } else {
            // Handle database update error
            echo "Error disabling meal.";
        }
    }

    if (isset($_POST['enable_meal'])) {
        $meal_id = $_POST['meal_id'];
        $sql = "UPDATE `free_meals_available` SET `availability_status` = 'available' WHERE `id` = $meal_id";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            // Redirect to admin page or show success message
            header('Location: free_meals_items.php'); // Or display a success message
            exit;
        } else {
            // Handle database update error
            echo "Error enabling meal.";
        }
    }

    // Fetch all free meals from the database
    $sql = "SELECT * FROM `free_meals_available`";
    $result = mysqli_query($conn, $sql);

    $free_meals = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Free Meals</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<?php include('include/nav.php')?>

    <div class="container mt-5">
        <h2>Admin Panel - Free Meals</h2>

        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMealModal">
            Add New Free Meal
        </button>

        <!-- Modal -->
        <div class="modal fade" id="addMealModal" tabindex="-1" aria-labelledby="addMealModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addMealModalLabel">Add New Free Meal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="free_meals_items.php" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="meal_name">Meal Name:</label>
                                <input type="text" class="form-control" id="meal_name" name="meal_name" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Description:</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="image_url">Image:</label>
                                <input type="file" class="form-control-file" id="image_url" name="image_url">
                            </div>
                            <button type="submit" class="btn btn-success" name="add_meal">Add Meal</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <h3>Available Free Meals</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Meal Name</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Availability</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($free_meals as $meal) { ?>
                    <tr>
                        <td><?php echo $meal['id']; ?></td>
                        <td><?php echo $meal['meal_name']; ?></td>
                        <td><?php echo $meal['description']; ?></td>
                        <td>
                            <?php if (!empty($meal['image_url'])) { ?>
                                <img src="img/<?php echo $meal['image_url']; ?>" alt="<?php echo $meal['meal_name']; ?>" width="50" height="50">
                            <?php } ?>
                        </td>
                        <td>
                            <?php if ($meal['availability_status'] == 'available') { ?>
                                <span class="badge bg-success">Available</span>
                            <?php } else { ?>
                                <span class="badge bg-danger">Unavailable</span>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if ($meal['availability_status'] == 'available') { ?>
                                <form method="POST" action="free_meals_items.php">
                                    <input type="hidden" name="meal_id" value="<?php echo $meal['id']; ?>">
                                    <button type="submit" class="btn btn-warning" name="disable_meal">Disable</button>
                                </form>
                            <?php } else { ?>
                                <form method="POST" action="free_meals_items.php">
                                    <input type="hidden" name="meal_id" value="<?php echo $meal['id']; ?>">
                                    <button type="submit" class="btn btn-success" name="enable_meal">Enable</button>
                                </form>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
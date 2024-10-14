<?php
include('include/connect.php'); // Include your database connection file

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryName = $_POST['categoryName'];

    // Sanitize the input (important for security!)
    $categoryName = mysqli_real_escape_string($conn, $categoryName);

    // Insert the category into the database
    $sql = "INSERT INTO categories (category_name) VALUES ('$categoryName')";

    if ($conn->query($sql) === TRUE) {
        // Success message using Bootstrap alert
        $successMessage = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            New category added successfully!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>';
    } else {
        // Error message using Bootstrap alert
        $errorMessage = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            Error adding category: ' . $sql . "<br>" . $conn->error . '
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>';
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body style="background-color: #D6EFD8;">
    <!-- Navigation Bar -->
    <?php include('include/nav.php')?>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Add New Category</h1>

        <?php 
        // Display success or error messages if they exist
        if (isset($successMessage)) {
            echo $successMessage;
        }
        if (isset($errorMessage)) {
            echo $errorMessage;
        }
        ?>

        <form method="POST">
            <div class="mb-3">
                <label for="categoryName" class="form-label">Category Name</label>
                <input type="text" class="form-control" id="categoryName" name="categoryName" placeholder="Enter category name" required>
            </div>

            <button type="submit" class="btn btn-outline-success rounded-pill">Add Category</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
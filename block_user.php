<?php include ('include/connect.php')?>

<?php
// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];
    $action = $_POST['action'];

    

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Update the user's status based on action
    $newStatus = ($action === 'block') ? 'blocked' : 'active';
    $sql = "UPDATE users SET status = ? WHERE user_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $newStatus, $userId);

    if ($stmt->execute()) {
        // Redirect back to user management page
        header("Location: users.php");
    } else {
        echo "Error updating user status: " . $conn->error;
    }

    // Close the database connection
    $stmt->close();
    $conn->close();
}
?>

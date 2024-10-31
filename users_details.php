<?php
    include('include/connect.php');

    // Get user_id from the query string
    $userId = $_GET['user_id'];

    // Fetch user details based on user_id
    $sqlUserDetails = "SELECT * FROM users WHERE user_id = '$userId'";
    $resultUserDetails = $conn->query($sqlUserDetails);

    if ($resultUserDetails->num_rows > 0) {
        $rowUserDetails = $resultUserDetails->fetch_assoc();

        // Display user details
        echo "<div class='container mt-5'>";
        echo "<h2>User Details</h2>";
        echo "<p><strong>User ID:</strong> " . $rowUserDetails["user_id"] . "</p>";
        echo "<p><strong>Username:</strong> " . $rowUserDetails["username"] . "</p>";
        echo "<p><strong>Full Name:</strong> " . $rowUserDetails["full_name"] . "</p>";
        echo "<p><strong>Email:</strong> " . $rowUserDetails["email"] . "</p>";
        echo "<p><strong>Role:</strong> " . $rowUserDetails["role"] . "</p>";
        echo "<p><strong>Status:</strong> " . $rowUserDetails["status"] . "</p>";
        // Add other relevant user details here
        echo "</div>";
    } else {
        echo "<div class='container mt-5'>
                <p>User not found.</p>
              </div>";
    }

    $conn->close();
?>
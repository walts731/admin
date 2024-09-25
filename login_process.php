<?php
session_start();
require 'include/connect.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the username and password from the form
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Check if inputs are empty
    if (empty($username) || empty($password)) {
        echo "Please fill in both the username and password fields.";
        exit;
    }

    // Prepare a SQL statement to select the user where the role is 'admin'
    $sql = "SELECT user_id, username, password, role FROM users WHERE username = ? AND role = 'admin' LIMIT 1";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('s', $username); // Bind the username to the statement

        if ($stmt->execute()) { // Execute the prepared statement
            $result = $stmt->get_result(); // Get the result

            // Check if a user is found
            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();

                // Compare the plain text password directly
                if ($password === $user['password']) {
                    // Set session variables for logged in admin
                    $_SESSION['admin_id'] = $user['user_id'];
                    $_SESSION['admin_username'] = $user['username'];

                    // Redirect to index.php
                    header('Location: index.php');
                    exit;
                } else {
                    // Invalid password
                    echo "Invalid password. Please try again.";
                }
            } else {
                // Invalid username or not an admin
                echo "Invalid username or not authorized.";
            }
        } else {
            echo "Error executing the query: " . $stmt->error;
        }

        $stmt->close(); // Close the prepared statement
    } else {
        echo "Error preparing the query: " . $conn->error;
    }

    $conn->close(); // Close the database connection
}
?>

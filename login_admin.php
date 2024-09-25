
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="css/admin_login.css">
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        
        <form action="login_process.php" method="POST">
            <div class="form-group">
                <label for="username">Admin Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="login-btn">Login</button>
        </form>
    </div>
</body>
</html>

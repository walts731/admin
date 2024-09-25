<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DELIDAZE Admin Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin_style.css">

    <!-- Google reCAPTCHA script -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>

    <div class="login-container">
        <h2>DELIDAZE Admin Login</h2>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Admin Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            
            <!-- Google reCAPTCHA -->
            <div class="g-recaptcha" data-sitekey="YOUR_SITE_KEY" data-theme="light" data-size="normal" data-color="green"></div>
            
            <button type="submit" class="login-btn">Login</button>
        </form>
        <div class="admin-msg">
            <p>Forgot password? <a href="#">Click here</a></p>
        </div>
    </div>

</body>
</html>

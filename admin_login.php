<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DELIDAZE Admin Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
    font-family: 'Poppins', sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    
    /* Add background image from the img folder */
    background-image: url('/img/green.png'); /* Replace with your actual image name */
    background-size: cover; /* Cover the entire viewport */
    background-position: center; /* Center the image */
    background-repeat: no-repeat; /* Avoid repeating the image */
    background-attachment: fixed; /* Keep background fixed on scroll */
    color: #fff;
}

        .login-container {
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 400px;
            backdrop-filter: blur(5px);
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 24px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            margin-bottom: 5px;
            font-weight: 400;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: none;
            font-size: 16px;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-group input:focus {
            outline: none;
            border: 2px solid #ff9900;
        }

        .g-recaptcha {
            display: flex;
            justify-content: center;
            margin-bottom: 15px;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            background-color: #ff9900;
            color: white;
            font-size: 16px;
            cursor: pointer;
            font-weight: 600;
        }

        .login-btn:hover {
            background-color: #e68a00;
        }

        .admin-msg {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }

        .admin-msg a {
            color: #ff9900;
            text-decoration: none;
        }

        .admin-msg a:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-container {
                padding: 20px;
            }

            .login-container h2 {
                font-size: 20px;
            }
        }
    </style>

    <!-- Google reCAPTCHA script -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>

    <div class="login-container">
        <h2>DELIDAZE Admin Login</h2>
        <form action="/admin/dashboard" method="POST">
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

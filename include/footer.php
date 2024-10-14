<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DELIDAZE Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzIA0b5v/y/Qk/+Cx2Q55/1hQ406dGaI+i8V1d7kImV+c0lF4p00k" crossorigin="anonymous">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .footer {
            background-color: #276221; /* Your footer background color */
            color: white; /* Your footer text color */
            position: relative; /* Position the footer */
            bottom: 0; /* Place the footer at the bottom of the page */
            width: 100%; /* Full width */
        }
        .footer .nav-link {
            color: white; /* Set link text color to white */
        }
        .footer .span {
            color: white;
        }
    </style>
</head>
<body>

    <!-- Your content goes here -->

    <footer class="footer mt-5 py-3">
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-6 text-center text-md-start">
                    <span class="text-muted text-light">&copy; 2024 DELIDAZE. All rights reserved.</span>
                </div>
                <div class="col-12 col-md-6 text-center text-md-end">
                    <ul class="nav justify-content-center justify-content-md-end">
                        <li class="nav-item"><a href="index.php" class="nav-link px-2">Home</a></li>
                        <li class="nav-item"><a href="products.php" class="nav-link px-2">Products</a></li>
                        <li class="nav-item"><a href="orders.php" class="nav-link px-2">Orders</a></li>
                        <li class="nav-item"><a href="inventory.php" class="nav-link px-2">Inventory</a></li>
                        <li class="nav-item"><a href="users.php" class="nav-link px-2">Users</a></li>
                        <li class="nav-item"><a href="analytics.php" class="nav-link px-2">Analytics</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geQ8Tj0GJh/Cqeu0xXKq7h49t4VXX1sRmCAi+a8MEWcui+Im/PCaAqO46MgnOM8" crossorigin="anonymous"></script>
</body>
</html>
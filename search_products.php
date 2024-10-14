<?php
     include 'include/connect.php';

     $searchTerm = $_GET['q'];

     $sql = "SELECT * FROM products WHERE product_name LIKE '%$searchTerm%'";
     $result = $conn->query($sql);

     $products = [];
     if ($result->num_rows > 0) {
         while ($row = $result->fetch_assoc()) {
             $products[] = $row;
         }
     }

     header('Content-Type: application/json');
     echo json_encode($products);
     ?>
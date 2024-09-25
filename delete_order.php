<?php
include('db_connection.php');
$data = json_decode(file_get_contents('php://input'), true);
$orderId = $data['orderId'];

$query = "DELETE FROM orders WHERE order_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $orderId);
$stmt->execute();

echo json_encode(['success' => $stmt->affected_rows > 0]);
?>

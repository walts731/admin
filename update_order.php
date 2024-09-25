<?php
include('db_connection.php');
$data = json_decode(file_get_contents('php://input'), true);
$orderId = $data['orderId'];
$status = $data['status'];

$query = "UPDATE orders SET status = ? WHERE order_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('si', $status, $orderId);
$stmt->execute();

echo json_encode(['success' => $stmt->affected_rows > 0]);
?>

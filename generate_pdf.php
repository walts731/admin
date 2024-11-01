<?php
require_once('vendor/TCPDF-main/tcpdf.php');
// Database connection (replace with your actual connection)
include ('include/connect.php');

// Get the date from the query parameter
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Fetch data from the database
$sql = "SELECT 
           history_id, 
           order_id, 
           user_id, 
           total_price, 
           status, 
           archived_at, 
           order_item_id, 
           product_id, 
           quantity, 
           price, 
           shipping_address, 
           payment_method, 
           reference_number, 
           payment_status
       FROM 
           orders_history 
       WHERE 
           DATE(archived_at) = '$date'
       ORDER BY 
           archived_at";
$result = $conn->query($sql);

// Create a new TCPDF object
$pdf = new TCPDF();

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Daily Sales Report');
$pdf->SetSubject('Daily Sales Report');
$pdf->SetKeywords('sales, report, daily');

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 10);

// Add the sales report content (use HTML for formatting)
$html = '
    <h2>Daily Sales Report - ' . date('M d, Y', strtotime($date)) . '</h2>
    <table>
        <thead>
            <tr>
                <th>Order Number</th>
                <th>User</th>
                <th>Order Items</th>
                <th>Total Sum</th>
                <th>Status</th>
                <th>Archived At</th>
                <th>Shipping Address</th>
                <th>Payment Method</th>
                <th>Ref No</th>
                <th>Payment Status</th>
            </tr>
        </thead>
        <tbody>';
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Fetch user details
        $sqlUserDetails = "SELECT username, full_name FROM users WHERE user_id = '" . $row["user_id"] . "'";
        $resultUserDetails = $conn->query($sqlUserDetails);
        $rowUserDetails = $resultUserDetails->fetch_assoc();

        // Fetch payment method name
        $paymentMethodId = $row["payment_method"];
        $sqlPaymentMethod = "SELECT method_name FROM payment_methods WHERE payment_method_id = '$paymentMethodId'";
        $resultPaymentMethod = $conn->query($sqlPaymentMethod);
        $rowPaymentMethod = $resultPaymentMethod->fetch_assoc();

        // Generate HTML for each order row
        $html .= '
            <tr>
                <td>' . $row["order_id"] . '</td>
                <td>' . ($rowUserDetails["username"] ?? 'N/A') . ' (' . ($rowUserDetails["full_name"] ?? 'N/A') . ')</td>
                <td><a href="#" onclick="window.open(\'order_items.php?order_id=' . $row["order_id"] . '\', \'Order Items\', \'width=800,height=600\')">View Order Items</a></td>
                <td>' . $row["total_price"] . '</td>
                <td>' . $row["status"] . '</td>
                <td>' . date("M d, Y h:ia", strtotime($row["archived_at"])) . '</td>
                <td>' . $row["shipping_address"] . '</td>
                <td>' . ($rowPaymentMethod["method_name"] ?? 'N/A') . '</td>
                <td>' . $row["reference_number"] . '</td>
                <td>' . $row["payment_status"] . '</td>
            </tr>';
    }
} else {
    $html .= '<tr><td colspan="10">No orders found for this day.</td></tr>';
}
$html .= '</tbody></table>';

// Output the PDF
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('daily_sales_report_' . $date . '.pdf', 'I'); // 'D' for download, 'I' for inline display
?>
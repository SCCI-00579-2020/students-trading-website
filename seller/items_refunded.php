<?php
session_start();
$HOSTNAME = 'localhost';
$USERNAME = 'root';
$PASSWORD = '';
$DATABASE = 'online_store';

$conn = mysqli_connect($HOSTNAME, $USERNAME, $PASSWORD, $DATABASE);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['seller_id'])) {
    die("Seller not logged in.");
}

$seller_id = $_SESSION['seller_id'];

$query = "SELECT t.*, p.name AS product_name, b.surname AS buyer_name, b.email AS buyer_email, b.phone_number AS buyer_phone
          FROM transactions t
          JOIN products p ON t.product = p.id
          JOIN buyer b ON t.buyer_id = b.id
          WHERE t.seller_id = ? AND t.status = 'refund_requested'
          ORDER BY t.created_at DESC";



$stmt = $conn->prepare($query);
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$result = $stmt->get_result();
$transactions = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refund Requested Purchases</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 20px; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f2f2f2; }
        .message { background-color: #e0f7fa; border: 1px solid #b2ebf2; padding: 10px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>Refund Requested Purchases</h1>
    
    <?php if (empty($transactions)): ?>
        <p>No transactions with refund requested.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Product</th>
                <th>Amount</th>
                <th>Buyer Name</th>
                <th>Buyer Email</th>
                <th>Buyer Phone</th>
                <th>Created At</th>
            </tr>
            <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td><?php echo htmlspecialchars($transaction['product_name']); ?></td>
                    <td>$<?php echo number_format($transaction['amount'], 2); ?></td>
                    <td><?php echo htmlspecialchars($transaction['buyer_name']); ?></td>
                    <td><?php echo htmlspecialchars($transaction['buyer_email']); ?></td>
                    <td><?php echo htmlspecialchars($transaction['buyer_phone']); ?></td>
                    <td><?php echo date('Y-m-d H:i', strtotime($transaction['created_at'])); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
<?php
session_start();
require_once 'config.php';
require_once 'functions.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$buyer_id = $_SESSION['user_id'];

// Fetch transactions for the current user
$query = "SELECT t.*, p.name AS product_name FROM transactions t 
          JOIN products p ON t.product = p.id 
          WHERE t.buyer_id = ? AND t.status = 'completed'
          ORDER BY t.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $buyer_id);
$stmt->execute();
$result = $stmt->get_result();
$transactions = $result->fetch_all(MYSQLI_ASSOC);

// Handle button actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $transaction_id = $_POST['transaction_id'];
    
    if (isset($_POST['release_funds'])) {
        // Update transaction status to 'funds_released'
        $update_query = "UPDATE transactions SET status = 'funds_released' WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("i", $transaction_id);
        $update_stmt->execute();
        
        $_SESSION['message'] = "Funds have been released to the seller.";
    } elseif (isset($_POST['refund_request'])) {
        // Update transaction status to 'refund_requested'
        $update_query = "UPDATE transactions SET status = 'refund_requested' WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("i", $transaction_id);
        $update_stmt->execute();
        
        $_SESSION['message'] = "Refund requested. Your money will be refunded within two days.";
    }
    
    // Redirect to refresh the page and avoid form resubmission
    header('Location: my_purchases.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Purchases</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 20px; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f2f2f2; }
        .message { background-color: #e0f7fa; border: 1px solid #b2ebf2; padding: 10px; margin-bottom: 20px; }
        .button { display: inline-block; padding: 10px 15px; background-color: #4CAF50; color: white; text-decoration: none; border: none; cursor: pointer; }
        .button.refund { background-color: #f44336; }
    </style>
</head>
<body>
    <h1>My Purchases</h1>
    
    <?php if (isset($_SESSION['message'])): ?>
        <div class="message">
            <?php 
            echo $_SESSION['message']; 
            unset($_SESSION['message']);
            ?>
        </div>
    <?php endif; ?>

    <table>
        <tr>
            <th>Product</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($transactions as $transaction): ?>
            <tr>
                <td><?php echo htmlspecialchars($transaction['product_name']); ?></td>
                <td>ksh<?php echo number_format($transaction['amount'], 2); ?></td>
                <td><?php echo date('Y-m-d H:i', strtotime($transaction['created_at'])); ?></td>
                <td><?php echo ucfirst($transaction['status']); ?></td>
                <td>
                    <?php if ($transaction['status'] == 'completed'): ?>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="transaction_id" value="<?php echo $transaction['id']; ?>">
                            <button type="submit" name="release_funds" class="button">Release Funds</button>
                        </form>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="transaction_id" value="<?php echo $transaction['id']; ?>">
                            <button type="submit" name="refund_request" class="button refund">Request Refund</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
<?php
session_start();
echo "Cart page - Session ID: " . session_id() . "<br>";
echo "<pre>Cart page - Session variables: ";
print_r($_SESSION);
echo "</pre>";
$HOSTNAME = 'localhost';
$USERNAME = 'root';
$PASSWORD = '';
$DATABASE = 'online_store';

$conn = mysqli_connect($HOSTNAME, $USERNAME, $PASSWORD, $DATABASE);
require_once 'functions.php';
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Handle actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && isset($_POST['product_id'])) {
        $product_id = $_POST['product_id'];
        $action = $_POST['action'];

        if ($action == 'add') {
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]++;
            } else {
                $_SESSION['cart'][$product_id] = 1;
            }
        } elseif ($action == 'remove') {
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]--;
                if ($_SESSION['cart'][$product_id] <= 0) {
                    unset($_SESSION['cart'][$product_id]);
                }
            }
        }
    } elseif (isset($_POST['pay'])) {
        $product_id = $_POST['product_id'];
        $amount = $_POST['amount'];
        $partyA = '254715088731';
        $partyB = '174379';
        $phoneNumber = $_POST['phone'];
        
        // Fetch seller information from the product
        $query = "SELECT seller_id FROM products WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $product = mysqli_fetch_assoc($result);
        $seller_id = $product['seller_id'];
        
        $buyer_id = $_SESSION['user_id']; 
        
        $transaction = createTransaction($buyer_id, $seller_id,$product_id, $amount);
        
        $callBackURL = 'https://7e0f-41-203-214-9.ngrok-free.app/callback.php'; 
        $response = initiateSTKPush($transaction['id'], $amount, $partyA, $partyB, $phoneNumber, $callBackURL);
        
        if (isset($response->errorCode)) {
            $_SESSION['error_message'] = $response->errorMessage;
        } else {
            $_SESSION['success_message'] = "Payment initiated successfully. Please check your phone to complete the transaction.";
        }
    }
}



// Fetch cart items
$cart_items = array();
if (!empty($_SESSION['cart'])) {
    $product_ids = implode(',', array_keys($_SESSION['cart']));
    $query = "SELECT * FROM products WHERE id IN ($product_ids)";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $row['quantity'] = $_SESSION['cart'][$row['id']];
        $cart_items[] = $row;
    }
}
// if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pay'])) {
//     $seller = $_POST['seller'];
//     $amount = $_POST['amount'];
//     $partyA = '254715088731';
//     $partyB = '174379';
//     $phoneNumber = $_POST['phone'];
    
//     $seller_user = getUserByUsername($seller);
//     $seller_id = $seller_user['id'];
//     $buyer_id = $_SESSION['user_id']; 
    
//     $transaction = createTransaction($buyer_id, $seller_id, $amount);
    
//     $callBackURL = 'https://7e0f-41-203-214-9.ngrok-free.app/callback.php'; 
//     $response = initiateSTKPush($transaction['id'], $amount, $partyA, $partyB, $phoneNumber, $callBackURL);
    
//     if (isset($response['error'])) {
//         $_SESSION['error_message'] = $response['error'];
//     } else {
//         $_SESSION['success_message'] = "Payment initiated successfully. Please check your phone to complete the transaction.";
//     }
// }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h1 {
            color: #2c3e50;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #3498db;
            color: #fff;
        }
        button, .checkout-button, .continue-shopping {
            background-color: #3498db;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
        }
        button:hover, .checkout-button:hover, .continue-shopping:hover {
            background-color: #2980b9;
        }
        .total {
            font-weight: bold;
            font-size: 1.2em;
            margin-top: 20px;
        }
        .pay-form {
            display: inline-block;
            margin-left: 10px;
        }
        .pay-form input[type="text"] {
            width: 120px;
            padding: 5px;
            margin-right: 5px;
        }
        .success-message {
            color: green;
            font-weight: bold;
        }
        .error-message {
            color: red;
            font-weight: bold;}
    </style>
</head>
<body>
    <h1>Shopping Cart</h1>
    
    <?php if (empty($cart_items)) : ?>
        <p>Your cart is empty.</p>
    <?php else : ?>
        <table>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Action</th>
                <th>Pay</th>
            </tr>
            <?php foreach ($cart_items as $item) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                    
                    <td>
                        <form action="cart.php" method="post">
                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                            <input type="hidden" name="action" value="remove">
                            <button type="submit">Remove</button>
                        </form>
                    </td>
                    <td>
                        <form action="cart.php" method="post" class="pay-form">
                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                            <input type="hidden" name="amount" value="<?php echo $item['price'] * $item['quantity']; ?>">
                            <input type="text" name="phone" placeholder="Phone number" required>
                            <button type="submit" name="pay">Pay</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        
        <p class="total">Total: $<?php echo number_format(array_sum(array_map(function($item) { return $item['price'] * $item['quantity']; }, $cart_items)), 2); ?></p>
        
        <a href="checkout.php" class="checkout-button">Proceed to Checkout</a>
    <?php endif; ?>
    
    <p><a href="browse_products.php" class="continue-shopping">Continue Shopping</a></p>
</body>
</html>
<?php
mysqli_close($conn);
?>
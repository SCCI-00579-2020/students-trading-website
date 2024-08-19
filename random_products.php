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

// Assuming the buyer's ID is stored in the session
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    die("User not logged in");
}

// Function to log user activity
function logUserActivity($conn, $buyer_id, $product_id, $action) {
    $query = "INSERT INTO user_activity (buyer_id, product_id, action) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("iis", $buyer_id, $product_id, $action);

    if (!$stmt->execute()) {
        echo "Error logging activity: " . $conn->error;
    }

    $stmt->close();
}

$query = "SELECT p.*, s.name AS seller_name
          FROM products p
          JOIN seller s ON p.seller_id = s.id
          LEFT JOIN (
              SELECT product_id, MAX(timestamp) as last_viewed
              FROM user_activity
              WHERE buyer_id = ? AND action = 'viewed'
              GROUP BY product_id
          ) ua ON p.id = ua.product_id
          ORDER BY 
              CASE 
                  WHEN ua.product_id IS NULL THEN 0  -- Prioritize unseen products
                  ELSE 1
              END,
              ua.last_viewed ASC,  -- Then order by least recently viewed
              RAND()  -- Add randomness within each group
          LIMIT 1";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
// Select a random product, avoiding recently viewed ones
$query = "SELECT p.*, s.name AS seller_name
          FROM products p
          JOIN seller s ON p.seller_id = s.id
          LEFT JOIN (
              SELECT product_id
              FROM user_activity
              WHERE buyer_id = ? AND action = 'viewed'
              ORDER BY timestamp DESC
              LIMIT 10
          ) recent ON p.id = recent.product_id
          WHERE recent.product_id IS NULL
          ORDER BY RAND()
          LIMIT 1";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

// Fetch a random product
$query = "SELECT p.*, s.name AS seller_name
          FROM products p
          JOIN seller s ON p.seller_id = s.id
          ORDER BY RAND()
          LIMIT 1";

$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$product = mysqli_fetch_assoc($result); 

if ($product) {
    // Log the user activity for viewing the product
    logUserActivity($conn, $user_id, $product['id'], 'viewed');
}

mysqli_free_result($result);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Random Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f4f4f4;
        }
        .product {
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 20px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 100%;
        }
        .product img {
            max-width: 100%;
            height: auto;
            margin-bottom: 10px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
        }
        .another-product {
            background-color: #008CBA;
        }
    </style>
</head>
<body>
    <div class="product">
        <?php if ($product): ?>
            <h2><?php echo htmlspecialchars($product['name']); ?></h2>
            <p>Price: kshs<?php echo number_format($product['price'], 2); ?></p>
            <p>Seller: <?php echo htmlspecialchars($product['seller_name']); ?></p>
            <p><?php echo htmlspecialchars($product['description']); ?></p>
            <?php if (!empty($product['image_path'])): ?>
                <img src="seller/<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            <?php endif; ?>
            <form action="cart.php" method="post">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <input type="hidden" name="action" value="add">
                <button type="submit">Add to Cart</button>
            </form>
        <?php else: ?>
            <h2>No products found</h2>
        <?php endif; ?>
        <button class="another-product" onclick="location.reload()">Show Another Random Product</button>
        <button onclick="window.location.href='rtdash.php'">Back to Trade</button>
    </div>
</body>
</html>
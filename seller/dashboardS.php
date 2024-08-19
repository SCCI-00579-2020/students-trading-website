<?php
// Your existing database connection code
$HOSTNAME = 'localhost';
$USERNAME = 'root';
$PASSWORD = '';
$DATABASE = 'online_store';
session_start();

// Fetching the user details from session variables
$first_name = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : 'Guest';
$first_initial = strtoupper(substr($first_name, 0, 1));

$conn = mysqli_connect($HOSTNAME, $USERNAME, $PASSWORD, $DATABASE);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if 'seller_id' is set
if (isset($_SESSION['seller_id'])) {
    $seller_id = $_SESSION['seller_id'];
    $seller_email = $_SESSION['email'];

    // Counting transactions with different statuses (existing code)
    $status_queries = [
        'pending' => "SELECT COUNT(*) as count FROM transactions WHERE seller_id = ? AND status = 'pending'",
        'completed' => "SELECT COUNT(*) as count FROM transactions WHERE seller_id = ? AND status = 'completed'",
        'funds_released' => "SELECT COUNT(*) as count FROM transactions WHERE seller_id = ? AND status = 'funds_released'",
        'refund_requested' => "SELECT COUNT(*) as count FROM transactions WHERE seller_id = ? AND status = 'refund_requested'"
    ];

    $counts = [];

    foreach ($status_queries as $status => $query) {
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $seller_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $counts[$status] = mysqli_fetch_assoc($result)['count'];
        mysqli_stmt_close($stmt);
    }

    // Counting products uploaded by the seller (existing code)
    $product_query = "SELECT COUNT(*) as product_count FROM products WHERE seller_id = ?";
    $product_stmt = mysqli_prepare($conn, $product_query);
    mysqli_stmt_bind_param($product_stmt, "i", $seller_id);
    mysqli_stmt_execute($product_stmt);
    $product_result = mysqli_stmt_get_result($product_stmt);
    $product_count = mysqli_fetch_assoc($product_result)['product_count'];
    mysqli_stmt_close($product_stmt);

    // Counting messages received by the seller
    $messages_query = "SELECT COUNT(*) as message_count FROM messages WHERE receiver_email = ?";
    $messages_stmt = mysqli_prepare($conn, $messages_query);
    if ($messages_stmt === false) {
        die("Error preparing messages statement: " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($messages_stmt, "s", $seller_email);
    mysqli_stmt_execute($messages_stmt);
    $message_result = mysqli_stmt_get_result($messages_stmt);
    $message_count = mysqli_fetch_assoc($message_result)['message_count'];
    mysqli_stmt_close($messages_stmt);
} else {
    // Handle the case where 'seller_id' is not set
    echo "Error: Seller ID not found. Please log in.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="boxicons/css/boxicons.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style.css">
    <title>Dashboard</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f8ff;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #fff;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-weight: bold;
            color: #005f99;
        }
        .search-bar {
            width: 300px;
        }
        .search-bar input {
            padding: 5px;
            width: 80%;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .search-bar button {
            padding: 5px 10px;
            background-color: #005f99;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .main-content {
            display: flex;
        }
        .sidebar {
            width: 250px;
            padding: 20px;
            background-color: #005f99;
            color: white;
        }
        .sidebar ul {
            list-style-type: none;
            padding: 0;
        }
        .sidebar li {
            margin-bottom: 20px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
            border-radius: 4px;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #007bff;
        }
        .content {
            flex: 1;
            padding: 20px;
        }
        .user-info {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .avatar {
            width: 50px;
            height: 50px;
            background-color: #ccc;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-size: 24px;
            color: white;
            background-color: #005f99;
        }
        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .stat-box {
            flex: 1;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .stat-box h4 {
            color: #007bff;
        }
        .dashboard-section {
            margin-bottom: 20px;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        .dashboard-item {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .sell-button {
            background-color: #d81b60;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
        }
        .sell-button:hover {
            background-color: #e67e22;
        }
    </style>
</head>
<body>
    <header>
        <span class="logo">TUK MARKET</span>
       

    </header>
    <div class="main-content">
    <div class="sidebar">
    <ul>
        <li><a href="#" class="link active">My TUK Trade Circle</a></li>
        <li><a href="products.php">Upload Products</a></li>
        <li><a href="seller_messages.php">My Messages
            <?php if ($message_count > 0): ?>
                <span class="message-count">(<?php echo $message_count; ?>)</span>
            <?php endif; ?>
        </a></li>            
        <li><a href="items_funds_released.php">Items Funds Released</a></li>
        <li><a href="items_on_hold.php">Items on Hold</a></li>
        <li><a href="items_pending_release.php">Items Pending Release</a></li>
        <li><a href="logout.html">Logout</a></li>
    </ul>
</div>

        <div class="content">
            <div class="user-info">
                <div class="avatar"><?php echo $first_initial; ?></div>
                <h2><?php echo htmlspecialchars($first_name); ?></h2>
            </div>
            <div class="dashboard-section">
                <h3>My Purchases</h3>
                <div class="stats">
                    <div class="stat-box">
                        <h4><?php echo $counts['funds_released']; ?></h4>
                        <p>Funds released</p>
                    </div>
                    <div class="stat-box">
                        <h4><?php echo $counts['refund_requested']; ?></h4>
                        <p>Orders Pending Refund or Replacement</p>
                    </div>
                    <div class="stat-box">
                        <h4><?php echo $counts['completed']; ?></h4>
                        <p>Completed Orders</p>
                    </div>
                </div>
            </div>
            <div class="dashboard-section">
                <h3>My Products</h3>
                <div class="dashboard-grid">
                    <div class="dashboard-item">
                        <h4><?php echo $product_count; ?></h4>
                        <p>Products Uploaded</p>
                    </div>
                 
                    
                    <div class="dashboard-item">
                    <form action="../dashboard.php" method="get">
        <button type="submit" class="sell-button">Back to home</button>
    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
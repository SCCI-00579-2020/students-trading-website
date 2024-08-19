<?php
$HOSTNAME = 'localhost';
$USERNAME = 'root';
$PASSWORD = '';
$DATABASE = 'online_store';
session_start();
$conn = mysqli_connect($HOSTNAME, $USERNAME, $PASSWORD, $DATABASE);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$seller_id = $_SESSION['seller_id'];
$seller_email=$_SESSION['email'];
//counting products one has uploaded
//$count_query ="SELECT COUNT(*) as orders FROM transaction WHERE seller_email = ? AND status='pending'";
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
//for products count
$product_query = "SELECT COUNT(*) as product_count FROM products WHERE seller_id = ?";
$product_stmt = mysqli_prepare($conn, $product_query);
mysqli_stmt_bind_param($product_stmt, "i", $seller_id);
mysqli_stmt_execute($product_stmt);
$product_result = mysqli_stmt_get_result($product_stmt);
$product_count = mysqli_fetch_assoc($product_result)['product_count'];
mysqli_stmt_close($product_stmt);
//html code for showing different querries for transaction table
// // <h4>Pending Orders: <?php echo $counts['pending']; ?></h4>
// <!-- // <h4>Completed Orders: <?php echo $counts['completed']; ?></h4> -->
// <!-- // <h4>Funds Released: <?php echo $counts['funds_released']; ?></h4> -->
// <!-- // <h4>Refund Requested: <?php echo $counts['refund_requested']; ?></h4> -->



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
    <title>Dasboard</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #fff;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .logo {
            font-weight: bold;
        }
        .search-bar {
    width: 300px; /* Set a fixed width */
    margin: 0 auto; /* Center the search bar */
    margin-left: -1000px;


        }
        .search-bar input {
            padding: 5px;
            width: 300px;
        }
        .search-bar button {
            padding: 5px 10px;
            background-color: #f28b00;
            color: white;
            border: none;
        }
        .main-content {
            display: flex;
        }
        .sidebar {
            width: 200px;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .sidebar ul {
            list-style-type: none;
            padding: 0;
        }
        .sidebar li {
            margin-bottom: 40px;
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
        }
        .stats {
            display: flex;
            gap: 20px;
        }
        .stat-box {
            text-align: center;
        }
        .dashboard-section {
            margin-bottom: 20px;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }
        .dashboard-item {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        .sell-button {
            background-color: #f28b00;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
        }
        .link.active{
            color: black;
}
    </style>
</head>
<body>
    <header>
        <span class="logo">TUK MARKET</span>
        <div class="search-bar">
            <input type="text" placeholder="I'm looking for...">
            <button>SEARCH</button>
        </div>
    </header>
    <div class="main-content">
        <div class="sidebar">
        <ul>
    <li><a href="#" class="link active">My TUK Trade Circle</a></li>
    <li><a href="messages.html">My Messages</a></li>

    <li><a href="ratings.html">Random Trade</a></li>
    <li><a href="purchases.html">Stuff I got</a></li>

    <li><a href="sales.html">My Sales</a></li>
    <li><a href="products.html">My Products</a></li>
    
    <li><a href="settings.html">Settings</a></li>
    <li><a href="logout.html">Logout</a></li>
</ul>

        </div>
        <div class="content">
        <div class="user-info">
        <div class="avatar"><?php echo $seller_id; ?></div>
        <h2><?php echo htmlspecialchars($seller_email); ?></h2>
     
    </div>
            <div class="dashboard-section">
                <h3>Stuff I got</h3>
                <div class="dashboard-grid">
                    <div class="dashboard-item">
                        <h4>0</h4>
                        <p>Pending Purchases</p>
                    </div>
                    <div class="dashboard-item">
                        <h4>0</h4>
                        <p>Purchases History</p>
                    </div>
                </div>
            </div>
            <div class="dashboard-section">
                <h3>My sales</h3>
                <div class="dashboard-grid">
                 <div class="dashboard-item">
                <h4><?php echo $counts['funds_released']; ?></h4>
                <p>Orders</p>
                  </div>
               
                    <div class="dashboard-item">
                        <h4><?php echo $counts['refund_requested']; ?></h4>
                        <p>Orders Pending Refund or Replacement</p>
                    </div>
                    <div class="dashboard-item">
                        <h4>0</h4>
                        <p>Sales History</p>
                    </div>
                </div>
            </div>
            <div class="dashboard-section">
                <h3>My Products</h3>
                <div class="dashboard-grid">
                <div class="dashboard-item">
                <h4><?php echo $product_count; ?></h4>
                <p>Products Uploaded</p>
            </div> <div class="dashboard-item">
                        <h4>0</h4>
                        <p>Under Review</p>
                    </div>
                    <div class="dashboard-item">
                        <h4>0</h4>
                        <p>Cancelled Products</p>
                    </div>
                    <div class="dashboard-item">
                        <button class="sell-button">Sell Now</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
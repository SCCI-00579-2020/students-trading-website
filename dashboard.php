<?php
$HOSTNAME = 'localhost';
$USERNAME = 'root';
$PASSWORD = '';
$DATABASE = 'online_store';
session_start();
$first_name = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : 'Guest';
$first_initial = strtoupper(substr($first_name, 0, 1));

$conn = mysqli_connect($HOSTNAME, $USERNAME, $PASSWORD, $DATABASE);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$buyer_id = $_SESSION['user_id'];
$buyer_email=$_SESSION['email'];
//counting products one has uploaded
//$count_query ="SELECT COUNT(*) as orders FROM transaction WHERE buyer_email = ? AND status='pending'";
$status_queries = [
    'pending' => "SELECT COUNT(*) as count FROM transactions WHERE buyer_id = ? AND status = 'pending'",
    'completed' => "SELECT COUNT(*) as count FROM transactions WHERE buyer_id = ? AND status = 'completed'",
    'funds_released' => "SELECT COUNT(*) as count FROM transactions WHERE buyer_id = ? AND status = 'funds_released'",
    'refund_requested' => "SELECT COUNT(*) as count FROM transactions WHERE buyer_id = ? AND status = 'refund_requested'"
];

$counts = [];

foreach ($status_queries as $status => $query) {
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $buyer_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $counts[$status] = mysqli_fetch_assoc($result)['count'];
    mysqli_stmt_close($stmt);
}
//for products count
//html code for showing different querries for transaction table
// // <h4>Pending Orders: <?php echo $counts['pending']; ?></h4>
 <!-- // <h4>Completed Orders: <?php echo $counts['completed']; ?></h4> -->
 <!-- // <h4>Funds Released: <?php echo $counts['funds_released']; ?></h4> -->
<!-- // <h4>Refund Requested: <?php echo $counts['refund_requested']; ?></h4> -->





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
        <div class="search-bar">
    <button onclick="location.href='browse_products.php'">SEARCH</button>
</div>

    </header>
    <div class="main-content">
        <div class="sidebar">
            <ul>
                <li><a href="#" class="link active">My TUK Trade Circle</a></li>
                <li><a href="messages.php">My Messages</a></li>
                <li><a href="rtdash.php">Random Trade</a></li>
                <li><a href="my_purchases.php">Stuff I got</a></li>                
                <li><a href="admin/admin_register.php">Admin</a></li>
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
            
                    <div class="dashboard-item">
                    <form action="seller/loginS.php" method="get">
        <button type="submit" class="sell-button">Sell Now</button>
    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

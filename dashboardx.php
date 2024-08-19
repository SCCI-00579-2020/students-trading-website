<?php
// Start the session
session_start();

// Assuming you have set these session variables when the user logs in
// $_SESSION['username'] = 'nancy'; // Example username
// $_SESSION['email'] = 'nancy@example.com'; // Example email

// Fetching the user details from session variables
$first_name = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : 'Guest';
$first_initial = strtoupper(substr($first_name, 0, 1));
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
            background-color: #f0f2f5;  
            color: #333;
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
    display: flex;
    justify-content: center;
    margin: 20px 0;
}

        .search-bar input {
            padding: 5px;
            width: 300px;
        }
          .search-button {
  background-color: #4a90e2;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
}
      
.search-button:hover {
  background-color: #3a7bd5;
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
        .stats-label {
  color: #666;
  font-size: 14px;
}
.stats-number {
  font-size: 24px;
  font-weight: bold;
  color: #4a90e2;
}
.stats-card:hover {
  transform: translateY(-5px);
}
.stats-card {
  background-color: white;
  border-radius: 8px;
  padding: 20px;
  margin: 10px 0;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  transition: transform 0.3s;
}
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}
.header {
  background-color: #4a90e2;
  color: white;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
       .sell-button:hover {
  background-color: #45a049;
}
        .sell-button {
  background-color: #4caf50;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
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
            
            <button onclick="location.href='browse_products.php'">SEARCH</button>

        </div>
    </header>
    <div class="main-content">
        <div class="sidebar">
        <ul>
    <li><a href="#" class="link active">My TUK Trade Circle</a></li>
    <li><a href="messages.php">My Messages</a></li>

    <li><a href="random_products.php">Random Trade</a></li>
    <li><a href="my_purchases.php">Stuff I got</a></li>
    <li><a href="products.html">My Products</a></li>     
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
                        <h4>0</h4>
                        <p>Orders </p>
                    </div>
                    <div class="dashboard-item">
                        <h4>0</h4>
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
                        <h4>0</h4>
                        <p>Active Products</p>
                    </div>
                    <div class="dashboard-item">
                        <h4>0</h4>
                        <p>Under Review</p>
                    </div>
                    <div class="dashboard-item">
                        <h4>0</h4>
                        <p>Cancelled Products</p>
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
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$HOSTNAME = 'localhost';
$USERNAME = 'root';
$PASSWORD = '';
$DATABASE = 'online_store';

$conn = mysqli_connect($HOSTNAME, $USERNAME, $PASSWORD, $DATABASE);
session_start();
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


if (!isset($_SESSION['seller_id'])) {
    die("Please log in as a seller to view messages!.");
}

$seller_id = $_SESSION['seller_id'];

// Fetch seller's email
$seller_query = "SELECT email FROM seller WHERE id = ?";
$seller_stmt = mysqli_prepare($conn, $seller_query);
mysqli_stmt_bind_param($seller_stmt, "i", $seller_id);
mysqli_stmt_execute($seller_stmt);
$seller_result = mysqli_stmt_get_result($seller_stmt);
$seller = mysqli_fetch_assoc($seller_result);
$seller_email = $seller['email'];

// Fetch messages
$query = "SELECT m.*, b.first_name, b.surname 
          FROM messages m 
          LEFT JOIN buyer b ON m.sender_email = b.email OR m.receiver_email = b.email
          WHERE m.sender_email = ? OR m.receiver_email = ? 
          ORDER BY m.timestamp DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ss", $seller_email, $seller_email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$messages = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S Messages</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #e6eff2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 600px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #004d66;
            text-align: center;
            margin-bottom: 20px;
        }

        a {
            display: block;
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #004d66;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        a:hover {
            background-color: #00394d;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            background-color: #f2f8fb;
            margin-bottom: 10px;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease;
        }

        li:hover {
            background-color: #e0e0e0;
        }

        .message-header {
            font-weight: bold;
            color: #333;
        }

        .message-content {
            margin: 10px 0;
            color: #555;
        }

        .message-timestamp {
            font-size: 12px;
            color: #888;
        }
        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .quit-link {
            text-decoration: none;
        color: #d81b60;;
            font-size: 1.2em;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Your Messages</h1>
        <a href="send_messages.php">Send New Message</a>
           <a href="dashboardS.php" class="quit-link">Quit</a>
        <ul>
            <?php foreach ($messages as $message): ?>
                <li>
                    <div class="message-header">
                        <?php if ($message['sender_email'] === $seller_email): ?>
                            <span>You to <?php echo htmlspecialchars($message['first_name'] . ' ' . $message['surname']); ?>:</span>
                        <?php else: ?>
                            <span><?php echo htmlspecialchars($message['first_name'] . ' ' . $message['surname']); ?> to You:</span>
                        <?php endif; ?>
                    </div>
                    <div class="message-content">
                        <?php echo htmlspecialchars($message['message']); ?>
                    </div>
                    <div class="message-timestamp">
                        <?php echo $message['timestamp']; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>

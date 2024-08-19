<?php
$HOSTNAME = 'localhost';
$USERNAME = 'root';
$PASSWORD = '';
$DATABASE = 'online_store';

$conn = mysqli_connect($HOSTNAME, $USERNAME, $PASSWORD, $DATABASE);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Please log in.");
}

$user_id = $_SESSION['user_id'];

// Fetch buyer's email
$buyer_query = "SELECT email FROM buyer WHERE id = ?";
$buyer_stmt = mysqli_prepare($conn, $buyer_query);
mysqli_stmt_bind_param($buyer_stmt, "i", $user_id);
mysqli_stmt_execute($buyer_stmt);
$buyer_result = mysqli_stmt_get_result($buyer_stmt);
$buyer = mysqli_fetch_assoc($buyer_result);
$buyer_email = $buyer['email'];

// Fetch messages
$query = "SELECT m.*, s.name as seller_name 
          FROM messages m 
          LEFT JOIN seller s ON m.sender_email = s.email OR m.receiver_email = s.email
          WHERE m.sender_email = ? OR m.receiver_email = ? 
          ORDER BY m.timestamp DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ss", $buyer_email, $buyer_email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$messages = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyer Messages</title>
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
            max-width: 800px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #005f99;
            margin-bottom: 20px;
            text-align: center;
        }

        a {
            display: block;
            margin: 0 auto 20px auto;
            padding: 10px 20px;
            background-color: #005f99;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            transition: background-color 0.3s ease;
            max-width: 200px;
        }

        a:hover {
            background-color: #004b7a;
        }

        ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        li {
            background-color: #f2f8fb;
            margin-bottom: 10px;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
        .quit-link {
            text-decoration: none;
            color: #d81b60;
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <div class="container">
      
        <div class="header-row">
         <h1>Your Messages</h1>
        <a href="dashboard.php" class="quit-link">Quit</a>
    </div>
        <a href="send_message.php">Send New Message</a>
        <ul>
            <?php foreach ($messages as $message): ?>
                <li>
                    <div class="message-header">
                        <?php if ($message['sender_email'] === $buyer_email): ?>
                            <span>You to <?php echo htmlspecialchars($message['seller_name']); ?>:</span>
                        <?php else: ?>
                            <span><?php echo htmlspecialchars($message['seller_name']); ?> to You:</span>
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

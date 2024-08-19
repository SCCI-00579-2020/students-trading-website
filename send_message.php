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
    die("Please log in as a buyer to send messages.");
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

// Fetch all sellers for the dropdown
$seller_query = "SELECT id, email, name FROM seller";
$seller_result = mysqli_query($conn, $seller_query);
$sellers = mysqli_fetch_all($seller_result, MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $receiver_email = $_POST['receiver_email'];
    $message = $_POST['message'];

    if (empty($receiver_email) || empty($message)) {
        $error = "Both fields are required.";
    } else {
        $query = "INSERT INTO messages (sender_email, receiver_email, message) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        if (!$stmt) {
            die("Prepare failed: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt, "sss", $buyer_email, $receiver_email, $message);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = "Message sent successfully!";
        } else {
            $error = "Error sending message: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Message to Seller</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        h1 { color: #333; }
        form { margin-bottom: 20px; }
        label { display: block; margin-top: 10px; }
        select, textarea { width: 100%; padding: 8px; margin-top: 5px; }
        input[type="submit"] { background-color: #4CAF50; color: white; padding: 10px 15px; border: none; cursor: pointer; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <h1>Send Message to Seller</h1>
    
    <?php
    if (isset($error)) {
        echo "<p class='error'>$error</p>";
    }
    if (isset($success)) {
        echo "<p class='success'>$success</p>";
    }
    ?>

    <form method="POST">
        <label for="receiver_email">Select Seller:</label>
        <select id="receiver_email" name="receiver_email" required>
            <option value="">Choose a seller</option>
            <?php foreach ($sellers as $seller): ?>
                <option value="<?php echo htmlspecialchars($seller['email']); ?>">
                    <?php echo htmlspecialchars($seller['name'] . ' (' . $seller['email'] . ')'); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="message">Message:</label>
        <textarea id="message" name="message" required></textarea>

        <input type="submit" value="Send Message">
    </form>

    <a href="messages.php">Back to Messages</a>
</body>
</html>
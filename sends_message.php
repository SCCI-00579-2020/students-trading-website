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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seller_email = filter_var($_POST['seller_email'], FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars($_POST['message']);
    $buyer_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : null;

    if ($buyer_email === null) {
        echo "User email not set in session.";
        exit;
    }

    if (!empty($seller_email) && !empty($message) && filter_var($seller_email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $conn->prepare("INSERT INTO messages (seller_email, buyer_email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $seller_email, $buyer_email, $message);

        if ($stmt->execute()) {
            echo "Message sent successfully.";
        } else {
            echo "Failed to send the message: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Invalid input. Please go back and try again.";
    }
} else {
    echo "Invalid request method.";
}

mysqli_close($conn);


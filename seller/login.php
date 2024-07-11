<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$HOSTNAME = 'localhost';
$USERNAME = 'root';
$PASSWORD = '';
$DATABASE = 'online_store';

$conn = mysqli_connect($HOSTNAME, $USERNAME, $PASSWORD, $DATABASE);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$error_message = '';
$debug_message = '';

if (isset($_POST["submit"])) {
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    $debug_message .= "Email submitted: " . $email . "<br>";

    $query = "SELECT * FROM seller WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);

    if (!$stmt) {
        $error_message = "Prepared statement failed: " . mysqli_error($conn);
    } else {
        mysqli_stmt_bind_param($stmt, "s", $email);
        $execute_result = mysqli_stmt_execute($stmt);
        
        if (!$execute_result) {
            $error_message = "Statement execution failed: " . mysqli_stmt_error($stmt);
        } else {
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {
                $debug_message .= "User found in database. Stored password: " . $row['password'] . "<br>";
                $debug_message .= "Input password: " . $password . "<br>";
                
                if ($password === $row['password']) {
                    $_SESSION["logged_in"] = true;
                    $_SESSION['name'] = $row['name'];
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['phone_number'] = $row['phone_number'];
                    $_SESSION['seller_id'] = $row['id'];
                    
                    
                    
                  
                    header("Location: products.php");
                    exit();
                } else {
                    $error_message = "Login failed: Invalid password.";
                    $debug_message .= "Password verification failed.<br>";
                }
            } else {
                $error_message = "Login failed: User not found.";
            }
        }
        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }
        button[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        button[type="submit"]:hover {
            background-color: #0056b3;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Seller Login</h2>
        <?php
        if (!empty($error_message)) {
            echo "<p style='color: red;'>" . $error_message . "</p>";
        }
        if (!empty($debug_message)) {
            echo "<p style='color: blue;'><strong>Debug info:</strong><br>" . $debug_message . "</p>";
        }
        ?>
        <form action="" method="post" autocomplete="off">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" placeholder="Enter email" required>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" placeholder="Enter password" required>
            <button type="submit" name="submit">Login</button>
        </form>
        <a href="register.php">Don't have an account? Register here</a>
    </div>
</body>
</html>

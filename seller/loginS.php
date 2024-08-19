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
                    
                    header("Location: dashboardS.php");
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
    <title>Login to Sell</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e6eff2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            background-color: #f2f8fb;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .header-container {
            background-color: #004d66;
            color: white;
            text-align: center;
            padding: 20px;
            border-radius: 8px 8px 0 0;
        }

        .header-container h2 {
            margin: 0;
            font-size: 24px;
        }

        .login-form {
            padding: 20px;
        }

        .login-form p {
            margin-bottom: 20px;
            font-size: 16px;
            color: #333;
        }

        .login-form input[type="email"],
        .login-form input[type="password"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            background-color: #fff;
            transition: background-color 0.3s ease;
        }

        .login-form input[type="email"]:focus,
        .login-form input[type="password"]:focus {
            background-color: #e0e0e0;
        }

        .login-form label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }

        .login-form input[type="submit"] {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: none;
            background-color: #004d66;
            color: white;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .login-form input[type="submit"]:hover {
            background-color: #00394d;
        }

        .login-form a {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #004d66;
            text-decoration: none;
            font-size: 14px;
        }

        .login-form a:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>
    <div class="login-container">
        <div class="header-container">
            <h2>Login to Sell</h2>
        </div>
        <form class="login-form" action="" method="post" autocomplete="off">
            <?php
            if (!empty($error_message)) {
                echo "<p style='color: red;'>" . $error_message . "</p>";
            }
            if (!empty($debug_message)) {
                echo "<p style='color: blue;'><strong>Debug info:</strong><br>" . $debug_message . "</p>";
            }
            ?>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" placeholder="Enter email" required>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" placeholder="Enter password" required>
            <input type="submit" name="submit" value="Login">
        </form>
        <a href="register.php">Don't have an account? Register here</a>
    </div>
</body>
</html>

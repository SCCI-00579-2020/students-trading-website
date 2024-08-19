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

if (isset($_POST["submit"])) {
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM admin WHERE username = ?";
    $stmt = mysqli_prepare($conn, $query);

    if (!$stmt) {
        die("Prepared statement failed: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_username'] = $row['username'];
            header("Location: admin.php");
            exit();
        } else {
            echo "Invalid username or password.";
        }
    } else {
        echo "Invalid username or password.";
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="styles.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('background.jpg'); /* Replace with your image URL */
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
            background-color: rgba(255, 255, 255, 0.9); /* Semi-transparent background */
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            padding: 30px;
            box-sizing: border-box;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .login-form label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: bold;
        }
        .input-field {
            font-size: 15px;
            background: rgba(255, 255, 255, 0.8);
            color: #333;
            height: 50px;
            width: 100%;
            padding: 0 15px;
            border: 1px solid #ccc;
            border-radius: 25px;
            outline: none;
            transition: .3s ease;
            box-sizing: border-box;
        }
        .input-field:focus {
            border-color: #3498db;
        }
        .input-box {
            margin-bottom: 20px;
            position: relative;
        }
        .input-box i {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: #aaa;
        }
        .submit {
            font-size: 16px;
            font-weight: bold;
            color: white;
            height: 45px;
            width: 100%;
            border: none;
            border-radius: 25px;
            outline: none;
            background: #3498db;
            cursor: pointer;
            transition: .3s ease-in-out;
            text-transform: uppercase;
        }
        .submit:hover {
            background: #2980b9;
        }
        .two-col {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }
        .two-col label {
            font-size: 14px;
            color: #333;
        }
        .two-col input[type="checkbox"] {
            margin-right: 5px;
        }
        .top span {
            color: #fff;
            font-size: small;
            padding: 10px 0;
            display: flex;
            justify-content: center;
        }
        .top span a {
            font-weight: 500;
            color: #fff;
            margin-left: 5px;
        }
        header {
            color: #fff;
            font-size: 30px;
            text-align: center;
            padding: 10px 0 30px 0;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <form action="" method="post" autocomplete="off">
            <div class="input-box">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" class="input-field" placeholder="Enter username" required>
            </div>
            <div class="input-box">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" class="input-field" placeholder="Enter password" required>
            </div>
            <div class="input-box">
                <input type="submit" name="submit" class="submit" value="Login">
            </div>
            <div class="two-col">
                <div>
                    <input type="checkbox" id="login-check">
                    <label for="login-check">Remember Me</label>
                </div>
            </div>
        </form>
    </div>
</body>
</html>

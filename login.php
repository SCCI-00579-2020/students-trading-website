<?php
$HOSTNAME = 'localhost';
$USERNAME = 'root';
$PASSWORD = '';
$DATABASE = 'online_store';

$conn = mysqli_connect($HOSTNAME, $USERNAME, $PASSWORD, $DATABASE);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}




// if (isset($_POST["submit"])) {
//     $usernameemail = trim($_POST["usernameemail"]);
//     $password = $_POST['password'];

 
//     $query = "SELECT * FROM buyer WHERE username=? OR email=?";
//     $stmt = mysqli_prepare($conn, $query);

//     if (!$stmt) {
//         die("Prepared statement failed: " . mysqli_error($conn));
//     }

  
//     mysqli_stmt_bind_param($stmt, "ss", $usernameemail, $usernameemail);
//     mysqli_stmt_execute($stmt);

  
//     $result = mysqli_stmt_get_result($stmt);
//     $user = mysqli_fetch_assoc($result);

   
//     echo "Username/Email: " . $usernameemail . "<br>";
//     echo "Password: " . $password . "<br>";

//     if ($user && password_verify($password, $user['password'])) {
       
//         $_SESSION['username'] = $user['username'];
//         $_SESSION['first_name'] = $user['first_name'];
if (isset($_POST["submit"])) {
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM buyer WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);

    if (!$stmt) {
        die("Prepared statement failed: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        if ($password === $row['password']) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            echo "Login successful! Welcome, " . $row['username'] . "!";
    
        header("Location: browse_products.php");
        exit();
    } else {
        echo "Login failed: Invalid username or password.";
    }
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
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="css/reg.css">
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
        <h2>Buyer Login</h2>
        <form action="" method="post" autocomplete="off">
            <label for="username">Username:</label>
            <input type="text" name="email" id="email" placeholder="Enter username or email" required>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" placeholder="Enter password" required>
            <button type="submit" name="submit">Login</button>
        </form>
        <a href="register.php">Don't have an account? Register here</a>
    </div>
</body>
</html>

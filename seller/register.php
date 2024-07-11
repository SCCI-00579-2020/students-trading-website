<?php
$HOSTNAME = 'localhost';
$USERNAME = 'root';
$PASSWORD = '';
$DATABASE = 'online_store';

$conn = mysqli_connect($HOSTNAME, $USERNAME, $PASSWORD, $DATABASE);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST["submit"])) {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone_number = htmlspecialchars($_POST['number']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmpassword'];

    if (!preg_match('/@students\.tukenya\.ac\.ke$/', $email)) {
        echo "Registration Failed: this is only accessible to tuk students";
    } elseif ($password === $confirmPassword) {
        // Prepare SQL statement
        $query = "INSERT INTO seller (name, email, phone_number, password) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);

        if (!$stmt) {
            die("Prepared statement failed: " . mysqli_error($conn));
        }

        // Bind parameters
        mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $phone_number, $password);

        // Execute statement
        if (mysqli_stmt_execute($stmt)) {
            echo "Registration successful! You can now log in.";
            header("Location: dashboard.php");
        } else {
            echo "Error: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Registration Failed: Passwords do not match.";
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
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
        input[type="email"],
        input[type="password"],
        input[type="number"] {
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
        <h2>Seller Registration</h2>
        <form action="" method="post" autocomplete="off">
    <label for="name">First name:</label>
    <input type="text" name="name" id="name" placeholder="Enter  name" required>
    
   
    <label for="email">Email:</label>
    <input type="email" name="email" id="email" placeholder="Enter email" required>
    <label for="number">Phone Number:</label>
    <input type="tel" name="number" id="phone_number" placeholder="Enter phone number" required>
    <label for="password">Password:</label>
    <input type="password" name="password" id="password" placeholder="Enter password" required>
    <label for="confirmpassword">Confirm Password:</label>
    <input type="password" name="confirmpassword" id="confirmpassword" placeholder="Confirm password" required>
    <button type="submit" name="submit">Register</button>
</form>
        <a href="login.php">Already have an account? Login</a>
    </div>
</body>
</html>

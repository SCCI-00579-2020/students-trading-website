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

if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION["id"];
$message = "";

// Fetch user details
$query = "SELECT * FROM buyer WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$user) {
    echo "User not found!";
    exit();
}

// Update user details
if (isset($_POST["update"])) {
    $first_name = htmlspecialchars($_POST["name"]);
    $surname = htmlspecialchars($_POST["surname"]);
    $username = htmlspecialchars($_POST["username"]);
    $email = htmlspecialchars($_POST["email"]);
    $phone_number = htmlspecialchars($_POST["number"]);

    if (!preg_match('/@students\.tukenya\.ac\.ke$/', $email)) {
        $message = "Update Failed: This is only accessible to TUK students.";
    } else {
        $updateQuery = "UPDATE buyer SET first_name = ?, surname = ?, username = ?, email = ?, phone_number = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($stmt, "sssssi", $first_name, $surname, $username, $email, $phone_number, $userId);

        if (mysqli_stmt_execute($stmt)) {
            $message = "Profile updated successfully!";
        } else {
            $message = "Error: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    }
}

// Delete user account
if (isset($_POST["delete"])) {
    $deleteQuery = "DELETE FROM buyer WHERE id = ?";
    $stmt = mysqli_prepare($conn, $deleteQuery);
    mysqli_stmt_bind_param($stmt, "i", $userId);

    if (mysqli_stmt_execute($stmt)) {
        session_destroy();
        header("Location: index.php");
        exit();
    } else {
        $message = "Error: " . mysqli_stmt_error($stmt);
    }
    mysqli_stmt_close($stmt);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
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
        input[type="number"],
        button {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }
        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .delete-btn {
            background-color: #dc3545;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }
        .message {
            text-align: center;
            margin-bottom: 10px;
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Profile</h2>
        <div class="message"><?php echo $message; ?></div>
        <form action="" method="post" autocomplete="off">
            <label for="name">First name:</label>
            <input type="text" name="name" id="first_name" placeholder="Enter first name" required value="<?php echo htmlspecialchars($user['first_name']); ?>">
            <label for="surname">Surname:</label>
            <input type="text" name="surname" id="surname" placeholder="Enter surname" required value="<?php echo htmlspecialchars($user['surname']); ?>">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" placeholder="Enter username" required value="<?php echo htmlspecialchars($user['username']); ?>">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" placeholder="Enter email" required value="<?php echo htmlspecialchars($user['email']); ?>">
            <label for="number">Phone Number:</label>
            <input type="number" name="number" id="phone_number" placeholder="Enter phone number" required value="<?php echo htmlspecialchars($user['phone_number']); ?>">
            <button type="submit" name="update">Update</button>
            <button type="submit" name="delete" class="delete-btn" onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">Delete Account</button>
        </form>
        <a href="index.php">Go to Home</a>
    </div>
</body>
</html>
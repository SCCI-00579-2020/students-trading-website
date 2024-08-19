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

if (!isset($_SESSION['seller_id'])) {
    die("Seller not logged in.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["submit"])) {
    $seller_id = $_SESSION['seller_id'];
    $product_type = mysqli_real_escape_string($conn, $_POST['product_type']);
    $name = htmlspecialchars($_POST['name']);
    $price = $_POST['price'];
    $description = htmlspecialchars($_POST['description']);

    // Handle the image upload
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check === false) {
        echo "Error: File is not an image.";
        $uploadOk = 0;
    }

    if ($_FILES["image"]["size"] > 5000000) {
        echo "Error: File is too large.";
        $uploadOk = 0;
    }

    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Error: Only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;

            // Combine the insert query to include the product type
            $query = "INSERT INTO products (seller_id, name, type, price, description, image_path) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);

            if (!$stmt) {
                die("Prepared statement failed: " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($stmt, "issdss", $seller_id, $name, $product_type, $price, $description, $image_path);

            if (mysqli_stmt_execute($stmt)) {
                echo "Product uploaded successfully.";
            } else {
                echo "Error: " . mysqli_stmt_error($stmt);
            }

            mysqli_stmt_close($stmt);
        } else {
            echo "Error uploading file.";
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 20px auto;
            padding: 10px;
            background-color: #e0e0e0;
        }
        h2 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        input[type="text"],
        input[type="number"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            background-color: #f9f9f9;
            color: #333;
        }
        textarea {
            resize: vertical;
        }
        .button-container {
            display: flex;
            justify-content: space-between;
        }
        input[type="submit"],
        .view-products-btn {
            background-color: #3498db;
            color: #ffffff;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
            text-align: center;
            width: 48%;
        }
        input[type="submit"]:hover {
            background-color: #2980b9;
        }
        .view-products-btn {
            background-color: #1d6fa5;
        }
        .view-products-btn:hover {
            background-color: #15527b;
        }
        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .quit-link {
            text-decoration: none;
            color: #d81b60;
            font-size: 1.2em;
        }
    </style>
</head>
<body>
  
    <div class="header-row"> 
        <h2>Upload Product</h2>
        <a href="dashboardS.php" class="quit-link">Quit</a>
    </div>
    <form action="products.php" method="post" enctype="multipart/form-data">

  <!-- Product Type Dropdown Menu -->
<label for="product-type">Product Type:</label>
<select name="product_type" id="product-type" required>
    <option value="">-- Select a Product Type --</option>
    <option value="art-craft">Art & Craft</option>
    <option value="clothes">Clothes</option>
    <option value="books">Books</option>
    <option value="electronics">Electronics</option>
    <option value="home-gadgets">Home Gadgets</option>
    <option value="others">Others</option>
</select>




        <label for="name">Product Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="price">Price:</label>
        <input type="number" id="price" name="price" step="0.01" required>

        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="3" required></textarea>

        <label for="image">Upload Image:</label>
        <input type="file" id="image" name="image" accept="image/*" required>

        <div class="button-container">
            <input type="submit" name="submit" value="Upload Product">
            <a href="view_products.php" class="view-products-btn">View Products</a>
        </div>
    </form>
</body>
</html>

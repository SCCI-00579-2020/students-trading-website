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

$query = "SELECT * FROM products ORDER BY id DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $productId = $_POST['product_id'];
    $deleteQuery = "DELETE FROM products WHERE id = ?";

    if ($stmt = mysqli_prepare($conn, $deleteQuery)) {
        mysqli_stmt_bind_param($stmt, "i", $productId);
        if (mysqli_stmt_execute($stmt)) {
            echo "<p style='color: green;'>Product deleted successfully!</p>";
        } else {
            echo "<p style='color: red;'>Error deleting product: " . mysqli_error($conn) . "</p>";
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
    <title>View Products</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f0f0f0;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        .product {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .product img {
            max-width: 150px;
            height: auto;
            margin-right: 20px;
            border-radius: 8px;
        }
        .product h2 {
            margin: 0 0 10px;
            color: #3498db;
        }
        .product p {
            margin: 5px 0;
            color: #555;
        }
        .delete-button {
            background-color: #e74c3c;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .delete-button:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
    <h1>Products</h1>

    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
        <div class="product">
            <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
            <div>
                <h2><?php echo htmlspecialchars($row['name']); ?></h2>
                <p>Price: ksh<?php echo number_format($row['price'], 2); ?></p>
                <p><?php echo htmlspecialchars($row['description']); ?></p>
            </div>
            <form method="post" onsubmit="return confirm('Are you sure you want to delete this product?');">
                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                <button type="submit" name="delete" class="delete-button">Delete</button>
            </form>
        </div>
    <?php endwhile; ?>

    <?php
    mysqli_free_result($result);
    ?>
</body>
</html>

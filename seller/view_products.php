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
        }
        .product {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 20px;
        }
        .product img {
            max-width: 200px;
            height: auto;
        }
    </style>
</head>
<body>
    <h2>heyy</h2>
    <h1>Products</h1>

    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
        <div class="product">
            <h2><?php echo htmlspecialchars($row['name']); ?></h2>
            <p>Price: $<?php echo number_format($row['price'], 2); ?></p>
            <p><?php echo htmlspecialchars($row['description']); ?></p>
            <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
        </div>
    <?php endwhile; ?>

    <?php
    
    mysqli_free_result($result);
    mysqli_close($conn);
    ?>
</body>
</html>
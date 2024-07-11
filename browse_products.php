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

// Fetch all products from the database, including seller information
$query = "SELECT p.*, s.name AS seller_name 
          FROM products p 
          JOIN seller s ON p.seller_id = s.id 
          ORDER BY p.id DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

// Pagination
$products_per_page = 10;
$total_products = mysqli_num_rows($result);
$total_pages = ceil($total_products / $products_per_page);

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$page = max(1, min($page, $total_pages));
$offset = ($page - 1) * $products_per_page;

$query .= " LIMIT $offset, $products_per_page";
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
    <title>Browse Products</title>
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
        .pagination {
            margin-top: 20px;
        }
        .pagination a {
            padding: 5px 10px;
            text-decoration: none;
            border: 1px solid #ddd;
            margin-right: 5px;
        }
        .pagination a.active {
            background-color: #4CAF50;
            color: white;
        }
    </style>
</head>
<body>
    <h1>Browse Products</h1>

    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
        <div class="product">
            <h2><?php echo htmlspecialchars($row['name']); ?></h2>
            <p>Price: $<?php echo number_format($row['price'], 2); ?></p>
            <p>Seller: <?php echo htmlspecialchars($row['seller_name']); ?></p>
            <p><?php echo htmlspecialchars($row['description']); ?></p>
            <img src="seller/<?php echo htmlspecialchars($row['image_path']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
            <form action="cart.php" method="post">
            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
            <input type="hidden" name="action" value="add">
            <button type="submit">Add to Cart</button>
        </form>
        </div>
    <?php endwhile; ?>

    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
            <a href="?page=<?php echo $i; ?>" <?php echo ($i == $page) ? 'class="active"' : ''; ?>><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>

    <?php
    // Free result set
    mysqli_free_result($result);
    mysqli_close($conn);
    ?>
</body>
</html>
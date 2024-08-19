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
$query = "SELECT p.*, s.name AS seller_name, s.email AS seller_email 
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
            background-color: #f2f2f2;
        }
        h1 {
            color: #d81b60;
        }
        .product {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .product img {
            max-width: 200px;
            height: auto;
            border: 1px solid #ddd;
        }
        .pagination {
            margin-top: 20px;
            text-align: center;
        }
        .pagination a {
            padding: 5px 10px;
            text-decoration: none;
            border: 1px solid #ddd;
            margin-right: 5px;
            color: #333;
            background-color: #e0e0e0;
        }
        .pagination a.active {
            background-color: #d81b60;
            color: white;
        }
        button {
            background-color: #d81b60;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background-color: #c2185b;
        }
        .contact-button {
            background-color: #007bff;
            color: white;
        }
        .contact-button:hover {
            background-color: #0056b3;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 10px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
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
        <h1>Browse Products</h1>
            
        <a href="dashboard.php  " class="quit-link">Quit</a>
    </div>

    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
        <div class="product">
            <h2><?php echo htmlspecialchars($row['name']); ?></h2>
            <p>Price: ksh<?php echo number_format($row['price'], 2); ?></p>
            <p>Seller: <?php echo htmlspecialchars($row['seller_name']); ?></p>
            <p><?php echo htmlspecialchars($row['description']); ?></p>
            <img src="seller/<?php echo htmlspecialchars($row['image_path']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
            <form action="cart.php" method="post" style="display: inline;">
                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                <input type="hidden" name="action" value="add">
                <button type="submit">Add to Cart</button>
            </form>
            <button class="contact-button" onclick="openModal('<?php echo htmlspecialchars($row['seller_email']); ?>')">Contact Seller</button>
        </div>
    <?php endwhile; ?>

    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
            <a href="?page=<?php echo $i; ?>" <?php echo ($i == $page) ? 'class="active"' : ''; ?>><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>

    <!-- The Modal -->
    <div id="contactModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <form action="sends_message.php" method="post">
                <input type="hidden" name="seller_email" id="seller_email">
                <label for="message">Message:</label>
                <textarea name="message" id="message" rows="5" style="width: 100%;"></textarea>
                <button type="submit">Send Message</button>
            </form>
        </div>
    </div>

    <script>
        var modal = document.getElementById("contactModal");
        var span = document.getElementsByClassName("close")[0];

        function openModal(sellerEmail) {
            document.getElementById("seller_email").value = sellerEmail;
            modal.style.display = "block";
        }

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>

    <?php
    // Free result set
    mysqli_free_result($result);
    mysqli_close($conn);
    ?>
</body>
</html>

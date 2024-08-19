<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Random Trade</title>
    <style>
        /* Your existing styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .dashboard-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 400px;
        }
        h1 {
            font-size: 36px;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        p {
            color: #666;
            font-size: 18px;
            margin-bottom: 30px;
        }
        .info {
            background-color: #ecf0f1;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        button {
            background-color: #d81b60;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #2980b9;
        }
        select {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <div class="dashboard-container">
        <h1>Random Trade</h1>
     
        <div class="info">
            <h2>Find Items</h2>
            <p>Discover a variety of items available for trade. It's a fun and engaging way to see what your fellow students have to offer!</p>
        </div>

        <!-- Dropdown Menu -->
        <form action="random_products.php" method="GET">
            <select name="category" id="trade-category" required>
                <option value="">-- Select a Category --</option>
                <option value="art-craft">Art & Craft</option>
                <option value="clothes">Clothes</option>
                <option value="books">Books</option>
                <option value="electronics">Electronics</option>
                <option value="home-gadgets">Home Gadgets</option>
                <option value="others">Others</option>
            </select>

            <button type="submit">Begin</button>
        </form>
    </div>

</body>
</html>

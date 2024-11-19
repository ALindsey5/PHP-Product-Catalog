<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Catalog</title>
</head>

<body>
    <h1>Product Catalog</h1>

    <?php
    // Read products from the JSON file
    $jsonFile = 'products.json';
    if (file_exists($jsonFile)) {
        $productData = json_decode(file_get_contents($jsonFile), true);

        if (!empty($productData)) {
            echo "<ul>";
            foreach ($productData as $product) {
                echo "<li>";
                echo "<strong>Name:</strong> " . htmlspecialchars($product['name']) . "<br>";
                echo "<strong>Price:</strong> $" . htmlspecialchars($product['price']) . "<br>";
                if (!empty($product['description'])) {
                    echo "<strong>Description:</strong> " . htmlspecialchars($product['description']) . "<br>";
                }
                echo "</li><br>";
            }
            echo "</ul>";
        } else {
            echo "<p>No products available.</p>";
        }
    } else {
        echo "<p>Error: Unable to load product catalog.</p>";
    }
    ?>
    <?php
    require 'db.php';

    $stmt = $pdo->query('SELECT * FROM products');
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <ul>
        <?php foreach ($products as $product): ?>
            <li>
                <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                <p><?php echo htmlspecialchars($product['description']); ?></p>
                <p>Price: $<?php echo htmlspecialchars($product['price']); ?></p>
            </li>
        <?php endforeach; ?>
    </ul>


</body>

</html>

<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    $query = "SELECT * FROM cart WHERE product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update quantity if product already in cart
        $query = "UPDATE cart SET quantity = quantity + ? WHERE product_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii', $quantity, $product_id);
    } else {
        // Insert new product into cart
        $query = "INSERT INTO cart (product_id, quantity) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii', $product_id, $quantity);
    }
    $stmt->execute();
    header('Location: cart.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <h1>Product Details</h1>
    <?php
        // Read the JSON file
        $json_data = file_get_contents('products.json');
        $products = json_decode($json_data, true);
        
        // Get the product name from the query string
        $selected_product_name = urldecode($_GET['name']);
        
        // Find and display the selected product
        foreach ($products as $product) {
            if ($product['name'] === $selected_product_name) {
                echo "<h2>" . $product['name'] . "</h2>";
                echo "<p>Price: $" . number_format($product['price'], 2) . "</p>";
                echo "<p>Description: " . $product['description'] . "</p>";
                break;
            }
        }
    ?>
</body>
</html>

<?php
// Initialize variables
$productName = $price = $description = "";
$productNameErr = $priceErr = "";

// Database connection file
require 'conn.php'; // Ensure conn.php sets up a PDO instance: $pdo

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $valid = true;

    // Validate Product Name
    if (empty($_POST["product_name"])) {
        $productNameErr = "Product Name is required.";
        $valid = false;
    } else {
        $productName = $_POST["product_name"];
    }

    // Validate Price
    if (empty($_POST["price"])) {
        $priceErr = "Price is required.";
        $valid = false;
    } elseif (!is_numeric($_POST["price"]) || $_POST["price"] <= 0) {
        $priceErr = "Price must be a positive number.";
        $valid = false;
    } else {
        $price = $_POST["price"];
    }

    // Description is optional
    $description = !empty($_POST["description"]) ? $_POST["description"] : null;

    // If validation passed, process the data
    if ($valid) {
        try {
            // Prepare and execute SQL statement
            $stmt = $pdo->prepare("INSERT INTO products (product_name, price, description) VALUES (:product_name, :price, :description)");
            $stmt->bindParam(':product_name', $productName);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':description', $description);

            if ($stmt->execute()) {
                // Redirect to product catalog after successful addition
                header("Location: product_catalog.php");
                exit;
            } else {
                echo "Error: Unable to save product to the database.";
            }
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
</head>

<body>
    <h1>Add New Product</h1>

    <form action="add_product.php" method="post">
        <label for="product_name">Product Name:</label>
        <input type="text" id="product_name" name="product_name" value="<?= htmlspecialchars($productName); ?>">
        <span style="color: red;"><?= $productNameErr; ?></span><br><br>

        <label for="price">Price:</label>
        <input type="number" id="price" name="price" step="0.01" value="<?= htmlspecialchars($price); ?>">
        <span style="color: red;"><?= $priceErr; ?></span><br><br>

        <label for="description">Description (optional):</label><br>
        <textarea id="description" name="description"><?= htmlspecialchars($description); ?></textarea><br><br>

        <input type="submit" value="Add Product">
    </form>
</body>

</html>
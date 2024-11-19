<?php
// Load cart data from cart.json
$cartFile = 'cart.json';
$cart = json_decode(file_get_contents($cartFile), true) ?: [];

// Handle updates to quantity
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productName = $_POST['product'];

    if (isset($_POST['update'])) {
        // Update quantity
        $newQuantity = max(0, intval($_POST['quantity'])); // Ensure no negative values
        foreach ($cart as &$item) {
            if ($item['name'] === $productName) {
                $item['quantity'] = $newQuantity;
                break;
            }
        }
        $cart = array_filter($cart, fn($item) => $item['quantity'] > 0); // Remove items with quantity 0
    } elseif (isset($_POST['delete'])) {
        // Delete item from cart
        $cart = array_filter($cart, fn($item) => $item['name'] !== $productName);
    }

    // Save updated cart data
    file_put_contents($cartFile, json_encode(array_values($cart), JSON_PRETTY_PRINT));

    // Redirect to avoid form resubmission
    header('Location: cart.php');
    exit;
}

// Calculate totals
$totalCost = 0;
$totalItems = 0;
foreach ($cart as $item) {
    $totalCost += $item['price'] * $item['quantity'];
    $totalItems += $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Shopping Cart</title>
    <style>
        /* Add some basic styling */
        .cart-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .cart-item div {
            margin-right: 15px;
        }

        .summary {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <h1>Your Shopping Cart</h1>

    <?php if (!empty($cart)): ?>
        <?php foreach ($cart as $item): ?>
            <div class="cart-item">
                <div><?php echo htmlspecialchars($item['name']); ?></div>
                <div>Price: $<?php echo number_format($item['price'], 2); ?></div>
                <form method="post" style="display: inline;">
                    <input type="hidden" name="product" value="<?php echo htmlspecialchars($item['name']); ?>">
                    Quantity:
                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1">
                    <button type="submit" name="update">Update</button>
                </form>
                <form method="post" style="display: inline;">
                    <input type="hidden" name="product" value="<?php echo htmlspecialchars($item['name']); ?>">
                    <button type="submit" name="delete">Delete</button>
                </form>
                <div>Total: $<?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
            </div>
        <?php endforeach; ?>

        <div class="summary">
            <p>Total Items: <?php echo $totalItems; ?></p>
            <p>Total Cost: $<?php echo number_format($totalCost, 2); ?></p>
        </div>
    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>
</body>

</html>

<?php
include 'db.php';

$query = "SELECT cart.id, products.name, products.price, cart.quantity
          FROM cart
          JOIN products ON cart.product_id = products.id";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    echo "{$row['name']} - {$row['quantity']} x {$row['price']} = " . ($row['quantity'] * $row['price']) . "<br>";
}
?>
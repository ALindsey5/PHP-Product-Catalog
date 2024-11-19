<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $quantity = $_POST['quantity'];

    if (isset($_POST['update'])) {
        $query = "UPDATE cart SET quantity = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii', $quantity, $id);
    } elseif (isset($_POST['delete'])) {
        $query = "DELETE FROM cart WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $id);
    }

    $stmt->execute();
    header('Location: cart.php');
}
?>
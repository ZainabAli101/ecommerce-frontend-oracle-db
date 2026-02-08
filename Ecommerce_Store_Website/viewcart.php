<?php
session_start();
include 'config.php';

if(!isset($_SESSION['customer_id'])){
    die("Please login first.");
}

$customer_id = $_SESSION['customer_id'];

$sql = "SELECT c.cart_id, c.quantity, p.name, p.price
        FROM cart c
        JOIN products p ON c.product_id = p.product_id
        WHERE c.customer_id = :cid";
$stid = oci_parse($conn, $sql);
oci_bind_by_name($stid, ":cid", $customer_id);
oci_execute($stid);

$total = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Cart</title>
    <link rel="stylesheet" href="viewcart.css">
</head>
<body>
<div class="container">
    <h1>Your Cart</h1>
    <p>Welcome, <?php echo $_SESSION['name']; ?> | <a href="products.php">Back to Products</a></p>

    <table>
        <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
        </tr>
        <?php while($row = oci_fetch_assoc($stid)) { 
            $subtotal = $row['PRICE'] * $row['QUANTITY'];
            $total += $subtotal;
        ?>
        <tr>
            <td><?php echo $row['NAME']; ?></td>
            <td><?php echo $row['PRICE']; ?></td>
            <td><?php echo $row['QUANTITY']; ?></td>
            <td><?php echo $subtotal; ?></td>
        </tr>
        <?php } ?>
        <tr>
            <td colspan="3">Total</td>
            <td><?php echo $total; ?></td>
        </tr>
    </table>

    <form method="post" action="order.php">
        <input type="submit" name="place_order" value="Place Order">
    </form>
</div>
</body>
</html>

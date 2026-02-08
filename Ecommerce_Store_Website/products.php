<?php
session_start();
include 'config.php';

if(!isset($_SESSION['customer_id'])){
    header("Location: login.php");
    exit();
}

$sql = "SELECT * FROM products";
$stid = oci_parse($conn, $sql);
oci_execute($stid);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OurProducts</title>
    <link rel="stylesheet" href="product.css">
</head>
<body>
<header>
    <?php
if (isset($_SESSION['success_msg'])) {
    echo '<div class="success-banner">'.$_SESSION['success_msg'].'</div>';
    unset($_SESSION['success_msg']); // remove after showing
}
?>

    <div class="header-container">
        <h1>Our Products</h1>
        <div class="user-info">
            Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?> | 
            <a href="viewcart.php">View Cart</a>
        </div>
    </div>
</header>

<main>
    <div class="product-grid">
        <?php while($row = oci_fetch_assoc($stid)) { ?>
        <div class="product-card">
            <div class="product-image">
                <!-- Placeholder image, replace with your product images -->
                <img src="images/<?php echo strtolower($row['NAME']); ?>.jpg" alt="<?php echo htmlspecialchars($row['NAME']); ?>">
            </div>
            <div class="product-details">
                <h3><?php echo htmlspecialchars($row['NAME']); ?></h3>
                <p><?php echo htmlspecialchars($row['DESCRIPTION']); ?></p>
                <p class="price">PKR <?php echo number_format($row['PRICE']); ?></p>
                <form action="add_to_cart.php" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $row['PRODUCT_ID']; ?>">
                    <input type="number" name="quantity" value="1" min="1">
                    <input type="submit" name="add_to_cart" value="Add to Cart">
                </form>
            </div>
        </div>
        <?php } ?>
    </div>
</main>

<footer>
    <p>&copy; <?php echo date('Y'); ?> Your Store. All rights reserved.</p>
</footer>
</body>
</html>

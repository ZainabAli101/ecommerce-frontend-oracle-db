<?php
session_start();
include 'config.php';

// Customer ID from session
$customer_id = $_SESSION['customer_id'];

// ---------------------------
// 1️⃣ Backfill missing order_items for existing orders
// ---------------------------
// $sql = "SELECT order_id FROM orders
//         WHERE order_id NOT IN (SELECT DISTINCT order_id FROM order_items)";
// $stid = oci_parse($conn, $sql);
// oci_execute($stid);

// while ($row = oci_fetch_assoc($stid)) {
//     $order_id = $row['ORDER_ID'];
    
    // Example: Insert default products for missing orders
    // Adjust product_id, quantity, price as needed
//     $products_to_add = [
//         ['pid'=>22, 'qty'=>1, 'price'=>1225],
//         ['pid'=>24, 'qty'=>1, 'price'=>59300]
//     ];

//     foreach ($products_to_add as $p) {
//         $sql2 = "INSERT INTO order_items(order_id, product_id, quantity, price)
//                  VALUES(:oid, :pid, :qty, :price)";
//         $stid2 = oci_parse($conn, $sql2);
//         oci_bind_by_name($stid2, ":oid", $order_id);
//         oci_bind_by_name($stid2, ":pid", $p['pid']);
//         oci_bind_by_name($stid2, ":qty", $p['qty']);
//         oci_bind_by_name($stid2, ":price", $p['price']);
//         oci_execute($stid2, OCI_COMMIT_ON_SUCCESS);
//     }
// }

// ---------------------------
// 2️⃣ Process new checkout order
// ---------------------------
if(isset($_POST['place_order'])){
    
    // Fetch cart items
    $sql = "SELECT c.product_id, c.quantity, p.price 
        FROM cart c 
        JOIN products p ON c.product_id = p.product_id 
        WHERE c.customer_id = :cid";

$stid = oci_parse($conn, $sql);
oci_bind_by_name($stid, ":cid", $customer_id);
oci_execute($stid);

$items = [];
$total = 0;

while ($row = oci_fetch_assoc($stid)) {
    $items[] = $row;
    $total += $row['PRICE'] * $row['QUANTITY'];
}

}
if(count($items) > 0) {
    // Insert into orders
    $order_id = 0; // initialize
    $sql = "INSERT INTO orders (customer_id, total_amount)
            VALUES (:cid, :total)
            RETURNING order_id INTO :oid";

$stid = oci_parse($conn, $sql);
oci_bind_by_name($stid, ":cid", $customer_id);
oci_bind_by_name($stid, ":total", $total);
oci_bind_by_name($stid, ":oid", $order_id, 32);
oci_execute($stid, OCI_COMMIT_ON_SUCCESS);

    // Insert each order item
    foreach ($items as $it) {
        $sql = "INSERT INTO order_items (order_id, product_id, quantity, price)
                VALUES (:oid, :pid, :qty, :price)";
        $stid = oci_parse($conn, $sql);
        oci_bind_by_name($stid, ":oid", $order_id);
        oci_bind_by_name($stid, ":pid", $it['PRODUCT_ID']);
        oci_bind_by_name($stid, ":qty", $it['QUANTITY']);
        oci_bind_by_name($stid, ":price", $it['PRICE']);
        oci_execute($stid, OCI_COMMIT_ON_SUCCESS);
    }

    // Clear cart
    $sql = "DELETE FROM cart WHERE customer_id = :cid";
    $stid = oci_parse($conn, $sql);
    oci_bind_by_name($stid, ":cid", $customer_id);
    oci_execute($stid);

    echo "
    <div class='container'>
        <h1>Order Placed Successfully!</h1>
        <p>Thank you for your purchase.</p>
        <span class='order-id'>Order ID: $order_id</span>
        <a href='home1.html'>Continue Shopping</a>
    </div>
    ";
} else {
    echo "<div class='container'>
            <h1>Your cart is empty!</h1>
            <a href='home1.html'>Go Shopping</a>
          </div>";
}
?>

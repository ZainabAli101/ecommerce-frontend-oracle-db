<?php
session_start();
include 'config.php';

if (!isset($_SESSION['customer_id'])) {
    die("Please login first.");
}

if (isset($_POST['add_to_cart'])) {

    $customer_id = (int)$_SESSION['customer_id'];
    $product_id  = (int)$_POST['product_id'];
    $quantity    = (int)$_POST['quantity'];

    // Check if product already exists in cart
    $check_sql = "SELECT * FROM cart WHERE customer_id = :cid AND product_id = :pid";
    $check_stid = oci_parse($conn, $check_sql);
    oci_bind_by_name($check_stid, ":cid", $customer_id);
    oci_bind_by_name($check_stid, ":pid", $product_id);
    oci_execute($check_stid);

    $row = oci_fetch_assoc($check_stid);

    if ($row) {
        // Update quantity if product already exists
        $new_qty = $row['QUANTITY'] + $quantity;
        $update_sql = "UPDATE cart SET quantity = :qty WHERE cart_id = :cartid";
        $update_stid = oci_parse($conn, $update_sql);
        oci_bind_by_name($update_stid, ":qty", $new_qty);
        oci_bind_by_name($update_stid, ":cartid", $row['CART_ID']);
        if(!oci_execute($update_stid, OCI_COMMIT_ON_SUCCESS)){
            $e = oci_error($update_stid);
            die("Update failed: ".$e['message']);
        }
    } else {
        // Insert new product if not exists
        $insert_sql = "INSERT INTO cart (customer_id, product_id, quantity) 
                       VALUES (:cid, :pid, :qty)";
        $insert_stid = oci_parse($conn, $insert_sql);
        oci_bind_by_name($insert_stid, ":cid", $customer_id);
        oci_bind_by_name($insert_stid, ":pid", $product_id);
        oci_bind_by_name($insert_stid, ":qty", $quantity);

        if(!oci_execute($insert_stid, OCI_COMMIT_ON_SUCCESS)){
            $e = oci_error($insert_stid);
            die("Insert failed: ".$e['message']);
        }
    }

    $_SESSION['success_msg'] = "Product added to cart successfully!";
    header("Location: viewcart.php"); // redirect to cart page
    exit();
}
?>

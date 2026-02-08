<?php
session_start();
include 'config.php';

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM customer WHERE email = :email";
    $stid = oci_parse($conn, $sql);
    oci_bind_by_name($stid, ":email", $email);
    oci_execute($stid);

    $row = oci_fetch_assoc($stid);
    if($row && password_verify($password, $row['PASSWORD'])){
        $_SESSION['customer_id'] = $row['CUSTOMER_ID'];
        $_SESSION['name'] = $row['NAME'];
        header("Location: products.php");
        exit();
    } else {
        $msg = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
<div class="container">
    <h1>Login</h1>
    <?php if(isset($msg)) echo "<p class='error'>$msg</p>"; ?>
    <form method="post">
        Email: <input type="email" name="email" required><br>
        Password: <input type="password" name="password" required><br>
        <input type="submit" name="login" value="Login">
    </form>
    <p>Don't have an account? <a href="register.php">Register</a></p>
</div>
</body>
</html>

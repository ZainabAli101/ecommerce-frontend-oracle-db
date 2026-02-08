<?php
include 'config.php';

if(isset($_POST['register'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $phone = $_POST['phone'];

    $sql = "INSERT INTO customer (name, email, password, phone) VALUES (:name, :email, :password, :phone)";
    $stid = oci_parse($conn, $sql);

    oci_bind_by_name($stid, ":name", $name);
    oci_bind_by_name($stid, ":email", $email);
    oci_bind_by_name($stid, ":password", $password);
    oci_bind_by_name($stid, ":phone", $phone);

    $result = oci_execute($stid);

    if($result){
        $msg = "Registration successful! <a href='login.php'>Login here</a>";
    } else {
        $e = oci_error($stid);
        $msg = "Error: " . $e['message'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Registration</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
<div class="container">
    <h1>Register</h1>
    <?php if(isset($msg)) echo "<p>$msg</p>"; ?>
    <form method="post">
        Name: <input type="text" name="name" required><br>
        Email: <input type="email" name="email" required><br>
        Password: <input type="password" name="password" required><br>
        Phone: <input type="text" name="phone"><br>
        <input type="submit" name="register" value="Register">
    </form>
    <p>Already have an account? <a href="login.php">Login</a></p>
</div>
</body>
</html>

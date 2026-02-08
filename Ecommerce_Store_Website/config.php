<?php
// Oracle connection
$username = "ecom_user";
$password = "ecom_pass";
$connection_string = "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=localhost)(PORT=1521))(CONNECT_DATA=(SERVICE_NAME=orclpdb)))";

$conn = oci_connect($username, $password, $connection_string);

if (!$conn) {
    $e = oci_error();
    die("❌ Connection Failed: " . $e['message']);
}
?>

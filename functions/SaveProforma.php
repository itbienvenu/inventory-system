<?php
session_start();
include_once "../config/config.php";

if ($_SESSION['role'] !== 'executive') die("Unauthorized");

$company = $_POST['company'];
$vat = $_POST['vat'];
$street = $_POST['street'];
$city = $_POST['city'];
$postal_code = $_POST['postal_code'];
$country = $_POST['country'];
$executive_id = $_SESSION['user_id'];
$invoice_number = "PROF-" . time();

// Insert invoice header (you can make a table: proforma_invoices)
mysqli_query($conn, "INSERT INTO proforma_invoices (invoice_number, company, vat, street, city, postal_code, country, created_by) VALUES (
    '$invoice_number', '$company', '$vat', '$street', '$city', '$postal_code', '$country', $executive_id
)");

$invoice_id = mysqli_insert_id($conn);

// Loop over products and save them
$products = $_POST['products'];
$quantities = $_POST['quantities'];

foreach ($products as $i => $product_id) {
    $qty = (int)$quantities[$i];
    $product_query = mysqli_query($conn, "SELECT price FROM products WHERE id = $product_id");
    $product = mysqli_fetch_assoc($product_query);
    $price = $product['price'];
    $total = $price * $qty;

    mysqli_query($conn, "INSERT INTO proforma_items (invoice_id, product_id, quantity, unit_price, total_price) VALUES (
        $invoice_id, $product_id, $qty, $price, $total
    )");
}

header("Location: ../edits/view_proforma.php?invoice=$invoice_number");

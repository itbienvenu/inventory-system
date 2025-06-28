<?php
session_start();
require_once("../functions/ProductController.php"); // or your correct path

if (isset(($_POST['register_product']))) {
    register_product(
        $_POST['name'],
        $_POST['category'],
        $_POST['sku'],
        $_POST['description'],
        $_POST['cost_price'],
        $_POST['price'],
        $_POST['quantity'],
        $_POST['low_stock'],
        $_POST['supplier'],
        $_FILES['image']
    );

    header("Location: ../executive/products.php");
    exit;
} else{
    echo "Viewing wrong page";
}

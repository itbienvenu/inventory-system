<?php

$conn = mysqli_connect("localhost","root","", "inventory");
if (mysqli_connect_errno()) {
    printf("", mysqli_connect_error());
    exit(1);
}

?>
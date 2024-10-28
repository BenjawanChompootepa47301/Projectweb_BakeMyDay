<?php

$conn = mysqli_connect('localhost','root','12345678','kanom');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
    
}
?>
<?php

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "mysql"
);

if($conn->connect_error){
    die("FAILED: " . $conn->connect_error);
}

echo "CONNECTED SUCCESSFULLY";
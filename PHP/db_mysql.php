<?php
$conn = new mysqli("127.0.0.1", "root", "guvi1234", "guvi", 3306);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

?>



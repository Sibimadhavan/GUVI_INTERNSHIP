<?php
$conn = new mysqli("guvi.cb0a2uom643v.ap-south-1.rds.amazonaws.com", "root", "guvi1234", "guvi", 3306);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

?>




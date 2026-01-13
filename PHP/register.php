<?php
require "db_mysql.php";

header("Content-Type: application/json");

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true); //.Reads raw JSON sent via AJAX and converts JSON into PHP array.
$username = $data['username'] ?? ''; // uses null coalescing operator , use a default value when the value doesn't exist
$email    = $data['email'] ?? '';
$password = $data['password'] ?? '';
$dob     = $data['dob'] ?? '';
$age     = (int)$data['age'] ?? '';
$contact = $data['contact'] ?? '';

if (!$username || !$email || !$password) {
    echo json_encode(["status" => "error"]);
    exit;
}

require "db_mongo.php";
$client = new MongoDB\Client("mongodb://127.0.0.1:27017");
$collection = $client->guvi_profiles->profiles;

// Check if email exists (PREPARED STATEMENT)
$checkStmtemail = $conn->prepare("SELECT id FROM users WHERE email = ?");
$checkStmtemail->bind_param("s", $email);
$checkStmtemail->execute();
$checkStmtemail->store_result();

$checkStmtusername = $conn->prepare("SELECT id FROM users WHERE username = ?");
$checkStmtusername->bind_param("s", $username);
$checkStmtusername->execute();
$checkStmtusername->store_result();

if ($checkStmtemail->num_rows > 0) {
    echo json_encode(["status" => "emailexists"]);
    exit;
}else if($checkStmtusername->num_rows > 0){
    echo json_encode(["status" => "usernameexists"]);
    exit;
}

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert user (PREPARED STATEMENT)
$insertStmt = $conn->prepare(
    "INSERT INTO users (username, email, password) VALUES (?, ?, ?)"
);
$insertStmt->bind_param("sss", $username, $email, $hashedPassword);
if ($insertStmt->execute()) {
    //echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error"]);
}
$userId=$conn->insert_id;
try {
$collection->insertOne([
    "user_id"=>$userId,
    "username" => $username,
    "email" => $email,
    "dob" => $dob,
    "age" => (int)$age,
    "contact" => $contact
]);

  echo json_encode(["status" => "success"]);
  exit;
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
    exit;
}

?>